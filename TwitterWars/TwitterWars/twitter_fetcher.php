<?php
    
    require_once('phirehose/Phirehose.php');
    require_once('phirehose/OAuthPhirehose.php');
    require_once('config.php');
    
    class TwitterFetcher extends Phirehose
    {
        public static $London = array(-0.432816, 51.334044, 0.170059, 51.658075);
        public static $Exeter = array(-3.549671, 50.694935, -3.493366, 50.741452);
        
        private static $dbConn;
        
        const CHAR_SET = "utf8";
        private $usingCharSet = "TRUE";
        
        private $echoTweets = TRUE;
        
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
            if (!is_array($data)) return FALSE;
            if (!isset($data['id_str'])) return FALSE;
            if (!isset($data['created_at'])) return FALSE;
            if (!isset($data['user'])) return FALSE;
            if (!isset($data['user']['screen_name'])) return FALSE;
            if (!isset($data['user']['lang'])) return FALSE;
            if (!isset($data['text'])) return FALSE;
            return TRUE;
        }
        
        private function tweetInArea($tweet, $area)
        {
            if (isset($tweet['coordinates']) && $tweet['coordinates'] != NULL && 
                    is_array($tweet['coordinates']['coordinates'])) {
                
                $loc = $tweet['coordinates']['coordinates'];
                
                if ($loc[0] >= $area[0] && $loc[0] <= $area[2] && 
                    $loc[1] >= $area[1] && $loc[1] <= $area[3]) {
                    return TRUE;
                }
            }
            
            if (isset($tweet['place'])) {
                $place = $tweet['place'];
                
                if (isset($place['bounding_box'])) {
                    $bb = $place['bounding_box'];

                    if (is_array($bb['coordinates']) && isset($bb['type'])) {
                        if (strtolower($bb['type']) == 'polygon') {
                            $polygons = $bb['coordinates'];

                            foreach ($polygons as $poly) {
                                if (is_array($poly)) {
                                    $p1 = $poly[0];
                                    $p2 = $poly[2];

                                    if ($p1[0] < $area[2] && $p2[0] > $area[0] && 
                                        $p1[1] < $area[3] && $p2[1] > $area[1]) {
                                        return TRUE;
                                    }
                                }
                            }
                        }
                    }
                }
                
                if (is_string($place['full_name'])) {
                    $locName = $place['full_name'];
                    
                    if ($this->locationAssociatedWithArea($locName, $area)) {
                        return TRUE;
                    }
                }
            }
            
            if (isset($tweet['user']['location'])) {
                $locName = $tweet['user']['location'];
                
                if ($this->locationAssociatedWithArea($locName, $area)) {
                    return TRUE;
                }
            }
            
            return FALSE;
        }
        
        private function locationAssociatedWithArea($locName, $area)
        {
            $locName = strtolower($locName);
            
            if ($area == self::$London && strpos($locName, 'london') !== FALSE) {
                return TRUE;
            }
            if ($area == self::$Exeter && strpos($locName, 'exeter') !== FALSE) {
                return TRUE;
            }
            return FALSE;
        }
        
        public function enqueueStatus($status)
        {
            $data = json_decode($status, true);
            
            if ($this->tweetIsValid($data)) {
//                print_r($data);
                $user = $data['user'];
                
                if ($user['lang'] == 'en') {
                    $tableName = NULL;
                    
                    $tweetInLondon = $this->tweetInArea($data, self::$London);
                    $tweetInExeter = $this->tweetInArea($data, self::$Exeter);
                    
                    if ($tweetInLondon && !$tweetInExeter) {
                        $tableName = "london_tweets";
                        
                    } elseif (!$tweetInLondon && $tweetInExeter) {
                        $tableName = "exeter_tweets";
                    }
                    
                    if ($tableName) {
                        $profile_image_url = isset($user['profile_image_url']) ? $user['profile_image_url'] : "";
                        $dbText = mysql_real_escape_string($data['text']);
                        
                        if ($dbText && $dbText != "") {
                            $dbData = array(
                                'id_str' => $data['id_str'], 
                                'user'   => $user['screen_name'], 
                                'img'    => $profile_image_url, 
                                'ts'     => strtotime($data['created_at']), 
                                'text'   => $dbText
                            );

    //                        print $tableName . "\n";
                            if ($this->echoTweets) {
                                print $tableName . " => "; print_r($dbData); print "\n";
                            }

                            $query = sprintf("INSERT INTO %s (id_str, username, profile_image_url, unix_ts, text) 
                                              VALUES ('%s', '%s', '%s', %s, '%s')", $tableName, 
                                              $dbData['id_str'], $dbData['user'], $dbData['img'], $dbData['ts'], $dbData['text']);
                            mysql_query($query);

//                            $affectedRows = mysql_affected_rows();
//                            print "affected rows: " . $affectedRows . "\n";
//
//                            if ($affectedRows < 0) {
//                                print "mysql error: " . mysql_error() . "\n";
//                            }
                        } // dbText
                    } // tableName
                } // lang = en
            } // tweetIsValid
        } // enqueueStatus
        
        public function setEchoTweets($echoTweets)
        {
            $this->echoTweets = $echoTweets;
            
            if (!$echoTweets) {
                print "Echoing tweets disabled.\n";
            }
            return $this;
        }
    }
    
    if (TwitterFetcher::init()) {
        
        $fetcher = new TwitterFetcher(TWITTER_USER, TWITTER_PASS, Phirehose::METHOD_FILTER);

        if (isset($argv[1]) && $argv[1] == "h") {
            $fetcher->setEchoTweets(FALSE);
        }
        
        $fetcher->setLocations(array(TwitterFetcher::$London, TwitterFetcher::$Exeter));
        $fetcher->consume();
        
    } else {
        print "Cannot initialize TwitterFetcher.\n";
    }
?>