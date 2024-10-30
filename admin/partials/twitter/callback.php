<?php
//namespace KiTwitterAnalytics;

session_start();
require 'autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
if (isset($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] == $_SESSION['oauth_token']) {			   //In project use this session to change login header after successful login 
	$request_token = [];
	$request_token['oauth_token'] = esc_attr($_SESSION['oauth_token']);
	$request_token['oauth_token_secret'] = esc_attr($_SESSION['oauth_token_secret']);
	$connection = new TwitterOAuth(KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
	$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => esc_attr($_REQUEST['oauth_verifier'])));
	$_SESSION['access_token'] = esc_attr($access_token);
	// redirect user back to index page
	header("location:index.php");
}