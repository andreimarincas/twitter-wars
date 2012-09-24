<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Twitter Wars!</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
    <div>
      <h2><b>Connect</b></h2>
      <hr />
    </div>
    <?php
        require_once('../config.php');

        if (CONSUMER_KEY === '' || CONSUMER_SECRET === '') {
            echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>.';
            exit;
        }

        $content = '<a href="./redirect.php"><img src="../images/twitter_sign_in.png" alt="Sign in with Twitter"/></a>';
        print_r($content);
    ?>
</body>
</html>