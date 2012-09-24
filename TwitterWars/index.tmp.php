<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            
            require_once('oauthengine/twitteroauth.php');
            require_once('config.php');
            
            $con = mysql_connect(DB_HOST, DB_USER, DB_PASS);

            if (!$con) {
                die('Could not connect: ' . mysql_error());
            }

            $db_selected = mysql_select_db(DB_NAME, $con);
            
            if (!$db_selected) {
                die ('Can\'t use database ' . DB_NAME . ' : ' . mysql_error());
            }
            
            echo "conn ok";
            mysql_close($con);
            
        /*
            include('twitter.php');
            require_once("dbconfig.php");
            
            $con = mysql_connect($db_host, $db_user, $db_pass);

            if (!$con) {
                die('Could not connect: ' . mysql_error());
            }

            $db_selected = mysql_select_db($db_name, $con);
            
            if (!$db_selected) {
                die ('Can\'t use database ' . $db_name . ' : ' . mysql_error());
            }
            
            //$twitter_query = ' ';
            //$search = new TwitterSearch($twitter_query);
            $search = new TwitterSearch();
            
//            $Exeter = array('lat' => 50.802463, 'long' => -3.522034, 'radius' => 10);
//            $search->geocode($Exeter['lat'], $Exeter['long'], $Exeter['radius'], 'km');
            
            $London = array('lat' => 51.534377, 'long' => -0.122452, 'radius' => 30);
            $search->geocode($London['lat'], $London['long'], $London['radius'], 'km');
            
//            $search->lang('it');
//            $search->count(100);
//            $search->page(15);
            $results = $search->results();
            
            foreach ($results as $result) {
                
                $query = sprintf("INSERT INTO london_tweets (id_str, username, profile_image_url, timestamp, text) 
                                  VALUES ('%s', '%s', '%s', %s, '%s')", $result->id_str, 
                                                                        $result->from_user, 
                                                                        $result->profile_image_url, 
                                                                        strtotime($result->created_at), 
                                                                        $result->text);
//                echo 'tweet insert query: ' . $query . '<br>';
                mysql_query($query);
                
                echo '<div class="twitter_status">';
                echo '<img src="' . $result->profile_image_url . '" class="twitter_image">';
                echo "<div style='color:red'>" . $result->text . "</div>";
                echo '<div class="twitter_small">';
                echo 'id_str: ' . $result->id_str . '<br>';
                echo '<strong>From:</strong> <a href="http://www.twitter.com/' . $result->from_user . '">' . $result->from_user . '</a>: ';
                echo '<strong>at:</strong> ' . $result->created_at;
                echo '<br>' . strtotime($result->created_at);
                echo '</div>';
                echo '</div>';
            }
            
            mysql_close($con);
         * */
        
        
//        require_once("config.php");
//        print "streaming...";
        
        /*set_time_limit(0);
        
        $London = array('lat1'=>51.334044, 'lng1'=>-0.432816, 'lat2'=>51.658075, 'lng2'=>0.170059);
        $London_area = $London['lng1'].','.$London['lat1'].','.$London['lng2'].','.$London['lat2'];
//        echo $London_bounding_box;
        
//        $query_data = array('track'=>'hi', 'locations' => $London_area);
        $query_data = array('locations'=>'-180,-90,180,90');
        $errno = 0;
        $errstr = '';
        
        $fp = fsockopen("ssl://stream.twitter.com", 443, $errno, $errstr, 30);
        
        if(!$fp){
            print "$errstr ($errno)\n";
        } else {
            $request = "GET /1/statuses/filter.json?" . http_build_query($query_data) . " HTTP/1.1\r\n";
            $request .= "Host: stream.twitter.com\r\n";
            $request .= "Authorization: Basic " . base64_encode($twitter_user . ':' . $twitter_pass) . "\r\n\r\n";
            echo "request = " . $request . "<br>";
            fwrite($fp, $request);
            
            while(!feof($fp)){
                    $json = fgets($fp);
                    $data = json_decode($json, true);
                    if($data){
                        echo 'data received...<br>';
                            //
                            // Do something with the data!
                            //
                        
                    }
            }
            fclose($fp);
        }*/
        
            /*
            $oauth_conn = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
            $content = $oauth_conn->get('account/verify_credentials');
//            $content = 'hello';
            
            echo '<p><pre>';
            print_r($content);
            echo '</pre></p>';
             * 
             */
        ?>
        
    </body>
</html>
