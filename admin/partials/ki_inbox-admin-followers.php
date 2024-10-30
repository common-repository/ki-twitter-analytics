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
define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', esc_html( $consumer_key ) ); // add your app consumer key between single quotes
define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', esc_html( $consumer_secret ) ); // add your app consumer
define( 'KI_TWITTER_ANALYTICS_OAUTH_CALLBACK', site_url() . '/wp-admin/admin-ajax.php?action=ki_twitter_analytics_login' ); // your app callback URL i.e.
$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET );
global $wpdb;
if ( empty( $_SESSION['access_token'] ) ) {
	// get a request token from twitter
	$request_token = $connection->oauth( 'oauth/request_token', array(
		'oauth_callback' => KI_TWITTER_ANALYTICS_OAUTH_CALLBACK
	) );
	// save twitter token info to the session
	$_SESSION['oauth_token']        = esc_html( $request_token['oauth_token'] );
	$_SESSION['oauth_token_secret'] = esc_html( $request_token['oauth_token_secret'] );

	// not logged in, get and display the login with twitter link
	$tw_auth_url = $connection->url( 'oauth/authorize', array(
		'oauth_token' => esc_html( $request_token['oauth_token'] )
	) );
}

$connection_tw        = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, sanitize_text_field($_SESSION['access_token']['oauth_token']), sanitize_text_field($_SESSION['access_token']['oauth_token_secret']) );
$userInfo             = $connection_tw->get( 'users/show', array( 'screen_name' => esc_html( $_SESSION['access_token']['screen_name'] ) ) );
$_SESSION['userInfo'] = $userInfo;
$logginID             = esc_html( $_SESSION['access_token']['user_id'] );
$logginScreenName     = esc_html( $_SESSION['access_token']['screen_name'] );
$myfollowers          = $connection_tw->get( 'followers/list', array(
	'screen_name' => $logginScreenName,
	"count"       => 200
) );
if ( ! empty( $myfollowers ) ) {
	$wpdb->get_results( " DELETE FROM followers WHERE user_id ='" . sanitize_text_field( $logginID ) . "'" );
	if ( is_array( $myfollowers->users ) ) {
		foreach ( $myfollowers->users as $key => $followers_val ) {
			$followersData = array(
				'user_id'               => esc_html( $logginID ),
				'followers_id'          => esc_html( $followers_val->id ),
				'followers_screen_name' => utf8_encode( $followers_val->screen_name ),
				'followers_name'        => utf8_encode( $followers_val->screen_name ),
				'followers_description' => utf8_encode( $followers_val->description ),
				'followers_count'       => esc_html( $followers_val->followers_count ),
				'friends_count'         => esc_html( $followers_val->friends_count ),
				'statuses_count'        => esc_html( $followers_val->statuses_count ),
				'location'              => utf8_encode( $followers_val->location ),
				'description'           => utf8_encode( $followers_val->description ),
				'profile_image_url'     => $followers_val->profile_image_url,
				'created_at'            => esc_html( $followers_val->created_at )
			);
			$followerID    = $followers_val->id;

			$getfollower = $wpdb->get_results( "SELECT * FROM followers where user_id ='" . sanitize_text_field( $logginID ) . "' AND followers_id='" . sanitize_text_field( $followerID ) . "'" );

			if ( empty( $getfollower ) ) {
				$wpdb->insert( 'followers', $followersData );
			}
		}
	}
}
function get_follower2( $logginID ) {
	global $wpdb;
	if ( ! empty( $_REQUEST['s'] ) ) {
		$follow_search = esc_html( $_REQUEST['s'] );
		$searchFollow  = "AND followers_screen_name='" . sanitize_text_field( $follow_search ) . "'";
	}
	$following = $wpdb->get_results( " SELECT * FROM followers where user_id ='" . sanitize_text_field( $logginID ) . "' " . sanitize_text_field( $searchFollow ) . " order by followers_count desc" );

	return $following;
}

$followerData     = get_follower2( $logginID );
$_SESSION['page'] = "followers";
if ( isset( $_POST['search'] ) ) {
	$search   = sanitize_text_field($_POST['s']);
	$redirect = site_url() . '/wp-admin/admin.php?page=followers&s=' . $search; ?>
    <script> var redirect = "<?php echo $redirect; ?>";
			window.location.href = redirect;</script> <?php } ?>

<?php if(empty($_SESSION['access_token'])) { ?>
		<div class="ki_inbox_admin_settings twitter"> <a href="<?php echo esc_html($tw_auth_url); ?>"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>twitter/twitter-login-blue.png" class="twitter_logo"> </a>
		<?php } else  { ?>
		<div class="twitter_user_head">
			<h1 class="tw_main_heading"><?php echo esc_html($_SESSION['userInfo']->name); ?> </h1>
			<div class="tw_left"> @<?php echo esc_html($_SESSION['userInfo']->screen_name); ?></div>
			<div class="tw_right"> <?php echo esc_html($_SESSION['userInfo']->description); ?></div>
		</div>
		<nav class="navbar navbar-light bg-faded align-content-center" style="background-color:#e3f2fd">
			<form class="form-inline mt-3 ml-5 mr-5" method="post">
			  <input id="s-follow" class="form-control mr-sm-0 col-md-4 ml-auto" type="text" name="s" value="<?php if(isset($_REQUEST['s'])){ echo esc_html($_REQUEST['s']); }?>" placeholder="<?php echo esc_html__( 'Search your follower', 'ki-twitter-analytics' ); ?>">
			  <button id="s-btn" class="button button-primary" type="submit" style="font-family: 'FontAwesome', 'Poppins', sans-serif;" name="search"><?php echo esc_html__( 'SEARCH', 'ki-twitter-analytics' ); ?></button>
			</form>
		</nav>
  <div class="row">
    <div class="col-md-12">
      <form id="unfollow" method="POST">

      <table class="tableFollowers mt-3">
        <tr id="header">
          <th><?php echo esc_html__( 'USERNAME', 'ki-twitter-analytics' ); ?></th>
          <th><?php echo esc_html__( 'RELATIONSHIP', 'ki-twitter-analytics' ); ?></th>
          <th><?php echo esc_html__( 'ACCOUNT_CREATED', 'ki-twitter-analytics' ); ?></th>
          <th><?php echo esc_html__( 'ACTIVE_LEVEL', 'ki-twitter-analytics' ); ?></th>
          <th><?php echo esc_html__( 'KI_SCORE', 'ki-twitter-analytics' ); ?></th>
        </tr>
        <?php
            foreach ($followerData as $my_follower) {

                ?>

                <tr id="underSpace">

                    <td id="Uname"><img src="<?php echo esc_attr($my_follower->profile_image_url); ?>" alt="p-img" class="mr-2"> <p class="tw_screen"><?php echo esc_html($my_follower->followers_screen_name)?></p></td>
                    <td><?php esc_html_e( 'Not following', 'ki-twitter-analytics' );?></td>
                    <!--<td>2 Weeks</td>-->
                    <td><?php echo esc_html(str_replace("+0000","",$my_follower->created_at)) ?></td>

                        <?php

                        if($my_follower->statuses_count <= 10000 ){echo '<td id="activeLevel"><i class="fa fa-arrow-down" aria-hidden="true"></i> '.esc_html__( 'Low', 'ki-twitter-analytics' ).'</td>';}
                        if($my_follower->statuses_count > 10000 && $my_follower->statuses_count < 30000 ){echo '<td id="activeLevel" style="color:#CE9350"><i class="fa fa-arrow-right" aria-hidden="true"></i> '.esc_html__( 'Normal', 'ki-twitter-analytics' ).'</td>';}
                        if($my_follower->statuses_count >= 30000 ){echo '<td id="activeLevel" style="color: red"><i class="fa fa-arrow-up" aria-hidden="true"></i> '.esc_html__( 'High', 'ki-twitter-analytics' ).'</td>';}

                        ?>
                    <td><?php echo esc_html($my_follower->followers_count); ?></td>
                  </tr>

                <?php
            }
            ?>

      </table>
      <input type="hidden" name="action" value="follow">
      <input type="hidden" name="return_url" value="<?php echo esc_html($_SERVER['REQUEST_URI']) ?>">
      </form>
    </div>
  </div>

		<?php } ?>
