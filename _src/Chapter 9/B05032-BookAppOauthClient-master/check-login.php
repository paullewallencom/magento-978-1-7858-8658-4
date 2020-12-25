<?php

file_put_contents(
    'external-book-app.log',
    'check-login.php' . print_r($_POST, true) . print_r($_GET, true),
    FILE_APPEND
);

require '../../vendor/autoload.php';

$consumer = $_REQUEST['consumer_id'];
$callback = $_REQUEST['callback_url'];

session_id('BookAppOAuth');
session_start();

$consumerKey = $_SESSION['oauth_consumer_key'];
$consumerSecret = $_SESSION['oauth_consumer_secret'];
$magentoBaseUrl = rtrim($_SESSION['store_base_url'], '/');
$oauthVerifier = $_SESSION['oauth_verifier'];

define('MAGENTO_BASE_URL', $magentoBaseUrl);

$credentials = new \OAuth\Common\Consumer\Credentials($consumerKey, $consumerSecret, $magentoBaseUrl);
$oAuthClient = new BookAppOauthClient($credentials);
$requestToken = $oAuthClient->requestRequestToken();

$accessToken = $oAuthClient->requestAccessToken(
    $requestToken->getRequestToken(),
    $oauthVerifier,
    $requestToken->getRequestTokenSecret()
);

header('Location: '. $callback);