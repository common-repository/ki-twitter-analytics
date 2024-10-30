<?php

session_start();
require ("autoload.php");
use Abraham\TwitterOAuth\TwitterOAuth;
$consumer_key    = get_option( "ki_twitter_consumer_key" );
$consumer_secret = get_option( "ki_twitter_consumer_secret" );
define('KI_TWITTER_ANALYTICS_CONSUMER_KEY', $consumer_key ); 	// add your app consumer key between single quotes
define('KI_TWITTER_ANALYTICS_CONSUMER_SECRET', $consumer_secret); // add your app consumer 																			secret key between single quotes
define('KI_TWITTER_ANALYTICS_OAUTH_CALLBACK', site_url() . '/wp-admin/admin-ajax.php?action=ki_twitter_analytics_login'); // your app callback URL i.e. page 																			you want to load after successful 																			  getting the data

if (!isset($_SESSION['access_token'])) {
	$connection = new TwitterOAuth(KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => KI_TWITTER_ANALYTICS_OAUTH_CALLBACK));
	$_SESSION['oauth_token'] = sanitize_text_field($request_token['oauth_token']);
	$_SESSION['oauth_token_secret'] = sanitize_text_field($request_token['oauth_token_secret']);
	$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

	echo "<a href='".esc_attr($url)."'><img src='twitter-login-blue.png' style='margin-left:4%; margin-top: 4%'></a>";
} else {
	$access_token = sanitize_text_field($_SESSION['access_token']);
	$connection = new TwitterOAuth(KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	$user = $connection->get("account/verify_credentials", ['include_email' => 'true']);
//    $user1 = $connection->get("https://api.twitter.com/1.1/account/verify_credentials.json", ['include_email' => true]);
    echo '<img src=' . esc_attr($user->profile_image_url) . '>';
    echo "<br>";		//profile image twitter link
    echo esc_attr($user->name);echo "<br>";									//Full Name
    echo esc_attr($user->location);echo "<br>";								//location
    echo esc_attr($user->screen_name);echo "<br>";							//username
    echo esc_attr($user->created_at);echo "<br>";
    echo esc_attr($user->email);echo "<br>";									//Email, note you need to check permission on Twitter App Dashboard and it will take max 24 hours to use email
    echo "<pre>";
    print_r($user);
    echo "<pre>";								//These are the sets of data you will be getting from Twitter 												Database
}
