<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Twitter Wars!</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
    <div>
      <h2><b>Callback</b></h2>
      <hr />
    </div>
    <?php
        session_start();
        require_once('./twitteroauth.php');
        require_once('../config.php');
        

        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
            $_SESSION['oauth_status'] = 'oldtoken';
            header('Location: ./clearsessions.php');
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_SESSION['access_token'] = $access_token;

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            $_SESSION['status'] = 'verified';
    //        header('Location: ./index.php');
            echo "oauth_token : <b>" . $access_token['oauth_token'] . "</b><br>";
            echo "oauth_token_secret : <b>" . $access_token['oauth_token_secret'] . "</b><br>";
            
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
            $content = $connection->get('account/verify_credentials');
            echo '<p><pre>'; print_r($content); echo '</pre></p>';
            
        } else {
            /* Save HTTP status for error dialog on connnect page.*/
            header('Location: ./clearsessions.php');
        }
    ?>
</body>
</html>