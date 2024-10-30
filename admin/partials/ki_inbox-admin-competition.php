<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://waelhassan.com
 * @since      1.0.0
 *
 * @package    ki-twitter-analytics
 * @subpackage Ki_inbox/admin/partials
 */

//namespace KiTwitterAnalytics;

require plugin_dir_path( __FILE__ ) . 'twitter/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key    = get_option( "ki_twitter_consumer_key" );
$consumer_secret = get_option( "ki_twitter_consumer_secret" );
define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', $consumer_key ); // add your app consumer key between single quotes
define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', $consumer_secret ); // add your app consumer
define( 'KI_TWITTER_ANALYTICS_OAUTH_CALLBACK', site_url() . '/wp-admin/admin-ajax.php?action=ki_twitter_analytics_login' ); // your app callback URL i.e.
$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET );
global $wpdb;
if ( empty( $_SESSION['access_token'] ) ) {
	// get a request token from twitter
	$request_token = $connection->oauth( 'oauth/request_token', array(
		'oauth_callback' => KI_TWITTER_ANALYTICS_OAUTH_CALLBACK
	) );
	// save twitter token info to the session
	$_SESSION['oauth_token']        = sanitize_text_field( $request_token['oauth_token'] );
	$_SESSION['oauth_token_secret'] = sanitize_text_field( $request_token['oauth_token_secret'] );

	// not logged in, get and display the login with twitter link
	$tw_auth_url = $connection->url( 'oauth/authorize', array(
		'oauth_token' => sanitize_text_field( $request_token['oauth_token'] )
	) );
}

if ( ! empty( $_GET['nickname'] ) ) {
	$nickname = $_GET['nickname'];
} else {
	$nickname = "";
}

$connection_tw        = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret'] );
$userInfo             = $connection_tw->get( 'users/show', array( 'screen_name' => $nickname ) );
$_SESSION['userInfo'] = $userInfo;


if ( ! empty( $_SESSION['userInfo'] ) ) {
	$tw_userID       = $_SESSION['userInfo']->id;
	$joinDate        = $_SESSION['userInfo']->created_at;
	$followers_count = $_SESSION['userInfo']->followers_count;
	$friends_count   = $_SESSION['userInfo']->friends_count;
	$name            = $_SESSION['userInfo']->name;
	$location        = $_SESSION['userInfo']->location;
	$url             = $_SESSION['userInfo']->url;
} else {
	$name = "";
}

if ( ! empty( $userInfo ) ) {
	$wpdb->get_results( " DELETE FROM followers WHERE user_id ='" . sanitize_text_field( $tw_userID ) . "'" );
	$wpdb->get_results( " DELETE FROM friends WHERE user_id ='" . sanitize_text_field( $tw_userID ) . "'" );
}
if ( ! empty( $_SESSION['userInfo'] ) ) {
	$name = sanitize_text_field($_SESSION['userInfo']->name);
} else {
	$name = "";
}

$_SESSION['page'] = "network";
if ( isset( $_POST['search'] ) ) {
	$url = site_url() ."/wp-admin/admin.php?page=network&nickname=".$_POST['nickname'];
	echo "<script>window.location.href='".$url."';</script>";
	exit;
}
unset( $_SESSION['cursor'] );
unset( $_SESSION['tw_cursor'] );
function get_twitter_follower() {
	global $wpdb;
	session_start();
	$tw_userID = $_SESSION['userInfo']->id;
	if ( ! empty( $_GET['nickname'] ) ) {
		$nickname = $_GET['nickname'];
	} else {
		$nickname = "";
	}
	$connection_tw = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, sanitize_text_field($_SESSION['access_token']['oauth_token']), sanitize_text_field($_SESSION['access_token']['oauth_token_secret'] ));
	$followers     = $connection_tw->get( 'followers/list', array( 'screen_name' => $nickname, "count" => 200 ) );
	if ( ! empty( $followers ) ) {
		foreach ( $followers->users as $key => $followers_val ) {
			$followersData = array(
				'user_id'               => $tw_userID,
				'followers_id'          => $followers_val->id,
				'followers_screen_name' => utf8_encode( $followers_val->screen_name ),
				'followers_name'        => utf8_encode( $followers_val->screen_name ),
				'followers_description' => utf8_encode( $followers_val->description ),
				'followers_count'       => $followers_val->followers_count,
				'friends_count'         => $followers_val->friends_count,
				'statuses_count'        => $followers_val->statuses_count,
				'location'              => utf8_encode( $followers_val->location ),
				'description'           => utf8_encode( $followers_val->description ),
				'profile_image_url'     => $followers_val->profile_image_url,
				'created_at'            => $followers_val->created_at
			);
			$followerID    = $followers_val->id;
			$follower      = $wpdb->get_results( " SELECT * FROM followers where user_id ='" . sanitize_text_field( $tw_userID ) . "' AND followers_id ='" . sanitize_text_field( $followerID ) . "'" );
			
			if ( empty( $follower ) ) { 
				$res = $wpdb->insert( 'followers', $followersData );
			}
		}
	}
}

function get_twitter_following() {
	global $wpdb;
	session_start();
	$tw_userID = $_SESSION['userInfo']->id;
	if ( ! empty( $_GET['nickname'] ) ) {
		$nickname = $_GET['nickname'];
	} else {
		$nickname = "";
	}
	if ( ! empty( $_GET['nickname'] ) ) {
		$nickname = $_GET['nickname'];
	} else {
		$nickname = "";
	}
	$connection_tw = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret'] );
	$friends       = $connection_tw->get( 'friends/list', array(
		'screen_name' => $nickname,
		"count"       => 200
	) );

	if ( ! empty( $friends ) ) {

		$_SESSION["tw_cursor"] = $friends->next_cursor;
		foreach ( $friends->users as $key => $friends_val ) {
			$friendsData = array(
				'user_id'             => $tw_userID,
				'friends_id'          => $friends_val->id,
				'friends_screen_name' => $friends_val->screen_name,
				'friends_name'        => utf8_encode( $friends_val->screen_name ),
				'friends_description' => utf8_encode( $friends_val->description ),
				'followers_count'     => $friends_val->followers_count,
				'friends_count'       => $friends_val->friends_count,
				'statuses_count'      => $friends_val->statuses_count,
				'location'            => utf8_encode( $friends_val->location ),
				'description'         => utf8_encode( $friends_val->description ),
				'profile_image_url'   => $friends_val->profile_image_url,
				'created_at'          => $friends_val->created_at
			);
			$friendsID   = $friends_val->id;

			$checkfriend = $wpdb->get_results( " SELECT * FROM friends where user_id ='" . sanitize_text_field( $tw_userID ) . "' AND friends_id ='" . sanitize_text_field( $friendsID ) . "'" );
			if ( empty( $checkfriend ) ) {
				$wpdb->insert( 'friends', $friendsData );
			}
		}
	}
}

if ( ! empty( $nickname ) ) {
	get_twitter_follower();
	get_twitter_following();
}
?>


<?php if ( empty( $_SESSION['access_token'] ) ) { ?>
<div class="ki_inbox_admin_settings twitter">
    <a href="<?php echo esc_url($tw_auth_url); ?>"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>twitter/twitter-login-blue.png" class="twitter_logo">
    </a>
	<?php } else { ?>
        <div class="ki_main">
			<?php
			include_once plugin_dir_path( __FILE__ ) . esc_attr( 'inc/ki_inbox-admin-header.php' );
			include_once plugin_dir_path( __FILE__ ) . esc_attr( 'inc/ki_inbox-admin-com-listing.php' ); ?>

        </div>
	<?php } ?>

