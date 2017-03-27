<?php
    session_start();

    $config['base_url']             =   'http://localhost/auth.php';//example path
    $config['callback_url']         =   'http://localhost/demo.php';///example path 
      $config['linkedin_access']      =   '81na1lyzoskrhe';
    $config['linkedin_secret']      =   'ne2eXfF0pBkZqf31';

    include_once "linkedin.php";//linkedin file

    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
    //$linkedin->debug = true;

    # Now we retrieve a request token. It will be set as $linkedin->request_token
    $linkedin->getRequestToken();
    $_SESSION['requestToken'] = serialize($linkedin->request_token);
  
    # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
    //echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
    header("Location: " . $linkedin->generateAuthorizeUrl());
?>
