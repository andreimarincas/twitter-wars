<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Twitter Wars!</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
    <div>
      <h2><b>Redirect</b></h2>
      <hr />
    </div>
    <?php
        session_start();
        require_once('./twitteroauth.php');
        require_once('../config.php');

        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
        $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        switch ($connection->http_code) {
            case 200:
                $url = $connection->getAuthorizeURL($token);
                echo 'Authorize URL: <a href=' . $url . '>' . $url . '</a>';
                header('Location: ' . $url);
                break;
            default:
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
    ?>
</body>
</html>