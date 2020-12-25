<?php

file_put_contents(
    'external-book-app.log',
    'callback-url.php' . print_r($_POST, true) . print_r($_GET, true),
    FILE_APPEND
);

session_id('BookAppOAuth');
session_start();

$_SESSION['oauth_consumer_key'] = $_POST['oauth_consumer_key'];
$_SESSION['oauth_consumer_secret'] = $_POST['oauth_consumer_secret'];
$_SESSION['store_base_url'] = $_POST['store_base_url'];
$_SESSION['oauth_verifier'] = $_POST['oauth_verifier'];

session_write_close();

header('HTTP/1.0 200 OK');

echo 'Response';