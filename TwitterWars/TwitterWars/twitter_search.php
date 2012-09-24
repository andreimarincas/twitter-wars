<?php
    
    require_once('oauthengine/twitteroauth.php');
    require_once('config.php');
    
    class TwitterSearch
    {
        public static $London = array('lat' => 51.534377, 'lng' => -0.122452, 'rad' => 30);
        public static $Exeter = array('lat' => 50.802463, 'lng' => -3.522034, 'rad' => 10);
        
        private static $dbConn;
        private $location;
        
        const DAYS_COUNT = 7;
        const TWEETS_PER_DAY = 1000;
        
        const CHAR_SET = "utf8";
        private $usingCharSet = "TRUE";
        
        public static function init()
        {
            // Set timezone
            date_default_timezone_set(G_TIMEZONE);
            
            // Connect to mysql database
            self::$dbConn = mysql_connect(DB_HOST, DB_USER, DB_PASS);
            
            if (!self::$dbConn) {
                print "Could not connect: " . mysql_error() . "\n";
                return FALSE;
            }
            
            $db_selected = mysql_select_db(DB_NAME, self::$dbConn);
            
            if (!$db_selected) {
                print "Can't use database " . DB_NAME . " : " . mysql_error() . "\n";
                return FALSE;
            }
            
            if (!mysql_set_charset(self::CHAR_SET, self::$dbConn)) {
                print "Warning: Unable to set the character set.\n";
                $this->usingCharSet = FALSE;
            }
            
            return TRUE;
        }
        
        public static function destroy()
        {
            if (isset(self::$dbConn) && self::$dbConn != NULL) {
                mysql_close(self::$dbConn);
                self::$dbConn = NULL;
            }
        }
        
        private function tweetIsValid($data)
        {
            if (!isset($data->id_str)) return FALSE;
            if (!isset($data->created_at)) return FALSE;
            if (!isset($data->user)) return FALSE;
            if (!isset($data->user->screen_name)) return FALSE;
            if (!isset($data->user->lang)) return FALSE;
            if (!isset($data->text)) return FALSE;
            return TRUE;
        }
        
        private function getDatabaseTable()
        {
            if ($this->location == TwitterSearch::$London) {
                return "london_tweets";
            }
            if ($this->location == TwitterSearch::$Exeter) {
                return "exeter_tweets";
            }
            return NULL;
        }
        
        private function insertTweet($tweet)
        {
            $tableName = $this->getDatabaseTable();
            
            if (!$tableName) return FALSE;
            
            if ($this->tweetIsValid($tweet)) {
                if ($tweet->user->lang == "en") {
                    
//                    print $tweet->user->screen_name . ": " . $tweet->text . " (" . $tweet->id . ")\n";
                    $profile_img = isset($tweet->user->profile_image_url) ? $tweet->user->profile_image_url : "";
                    $dbText = mysql_real_escape_string($tweet->text);
                    
                    if ($dbText && $dbText != "") {
                        $query = sprintf("INSERT INTO %s (id_str, username, profile_image_url, unix_ts, text) 
                                          VALUES ('%s', '%s', '%s', %s, '%s')", $tableName, 
                                          $tweet->id_str, 
                                          mysql_real_escape_string($tweet->user->screen_name), 
                                          $profile_img, 
                                          strtotime($tweet->created_at), 
                                          $dbText);
    //                    print_r($query);
                        mysql_query($query);

                        $affectedRows = mysql_affected_rows();
//                        print "affected rows: " . $affectedRows . "\n";

                        if ($affectedRows > 0) {
//                            print $tweet->user->screen_name . ": " . $tweet->text . " (" . $tweet->id . ")\n";
                            return TRUE;
                        }
//                        if ($affectedRows < 0) {
//                            print "mysql error: " . mysql_error() . "\n";
//                            print_r($tweet);
//                        } else {
//                            print "No rows affected.\n";
//                        }
                    }
                }
            }
            return FALSE;
        }
        
//        private function getMinID($year, $month, $day)
//        {
//            $givenDate = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
//            $givenDateTS = strtotime($givenDate);
//            $nextDate = date("Y-m-d", mktime(0, 0, 0, $month, $day + 1, $year));
//            $nextDayTS = strtotime($nextDate);
//            
//            $tableName = $this->getDatabaseTable();
//            
//            $query = sprintf("SELECT MIN(CAST(id_str AS UNSIGNED)) AS id FROM %s WHERE unix_ts >= %s AND unix_ts < %s;", 
//                              $tableName, $givenDateTS, $nextDayTS);
//            $results = mysql_query($query);
//            
//            if (($row = mysql_fetch_assoc($results))) {
//                $id = $row['id'];
//                
//                $query = sprintf("SELECT * FROM %s WHERE id_str = '%s'", $tableName, $id);
//                $results = mysql_query($query);
//                
//                if (($row = mysql_fetch_assoc($results))) {
//                    return $row['unix_ts'];
//                }
//            }
//            return -1;
//        }
        
        public function fetchTweets($location)
        {
            $url = 'search/tweets';
            $query = " ";
            $this->location = $location;
            $geo = $location['lat'] . ',' . $location['lng'] . ',' . $location['rad'] . 'km';

            $params = array(
                'q'       => urlencode($query),
                'lang'    => 'en',
                'geocode' => $geo,
                'count'   => 100
            );

            $twitterOAuth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
//            $results = $twitterOAuth->get('account/rate_limit_status');
//            print_r($results);
//            return;
            
            $year   = date("Y");
            $month  = date("m");
            $day    = date("d");

            for ($i = 0; $i < self::DAYS_COUNT; $i++) {
                $date = date("Y-m-d", mktime(0, 0, 0, $month, $day - $i, $year));
                print "\ndate : " . $date . " (" . strtotime($date) . ")\n";
                $params['until'] = $date;
                unset($params['max_id']);
                $max_id = -1; //$this->getMinID($year, $month, $day - $i);
//                print "max_id: " . $max_id . "\n";
                $tweetsCount = 0;

                do {
                    if ($max_id != -1) {
                        $params['max_id'] = $max_id - 1;
                        $max_id = -1;
                    }
                    $results = $twitterOAuth->get($url, $params);
//                    print_r($results);

                    if (isset($results->statuses) && is_array($results->statuses)) {
                        $tweets = $results->statuses;

                        foreach ($tweets as $tweet) {
                            if ($tweetsCount < self::TWEETS_PER_DAY && isset($tweet->id)) {
                                
                                if ($this->insertTweet($tweet)) {
                                    $tweetsCount++;
                                }
                                if ($max_id == -1 || $tweet->id < $max_id) {
                                    $max_id = $tweet->id;
                                }
                            } else {
                                break;
                            }
                        }
                        print "tweetsCount: " . $tweetsCount . "\n";
                    }

                } while ($max_id != -1 && $tweetsCount < self::TWEETS_PER_DAY);
            }
        } // fetchTweets
        
    } // TwitterSearch
    
    if (TwitterSearch::init()) {
        
        if (isset($argv[1])) {
            $place = NULL;
            
            if (strtolower($argv[1]) == "london") {
                $place = TwitterSearch::$London;
            
            } elseif (strtolower($argv[1]) == "exeter") {
                $place = TwitterSearch::$Exeter;
            }
            
            if ($place != NULL) {
                print "Fetching tweets from the given place: $argv[1]\n";
                
                $fetcher = new TwitterSearch();
                $fetcher->fetchTweets($place);
                
            } else {
                print "Wrong parameter.\n";
                print "Please give me the place from where to fetch tweets (either London or Exeter).\n";
            }
        } else {
            print "No param given.\n";
            print "Fetching tweets from both London and Exeter.\n";
            
            $scriptPath = getcwd() . "/" . "twitter_search.php";
//            print "Script Path: " . $scriptPath . "\n";
            
            $command = "php " . $scriptPath . " London > london_tweets.txt &";
            print "Execute command: " . $command . "\n";
            exec($command);
            
            $command = "php " . $scriptPath . " Exeter > exeter_tweets.txt";
            print "Execute command: " . $command . "\n";
            exec($command);
        }
    } else {
        print "Cannot initialize TwitterSearch.\n";
    }
?>