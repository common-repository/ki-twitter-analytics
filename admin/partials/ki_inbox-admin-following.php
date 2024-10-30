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

$connection_tw        = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret'] );
$userInfo             = $connection_tw->get( 'users/show', array( 'screen_name' => esc_html( $_SESSION['access_token']['screen_name'] ) ) );
$_SESSION['userInfo'] = $userInfo;
$logginID             = $_SESSION['access_token']['user_id'];
$logginScreenName     = $_SESSION['access_token']['screen_name'];

$myfollowing = $connection_tw->get( 'friends/list', array( 'screen_name' => $logginScreenName, "count" => 200 ) );
if ( ! empty( $myfollowing ) ) {
	if(empty($myfollowing->errors)) { $wpdb->get_results( " DELETE FROM friends WHERE user_id ='" . $logginID. "'" ); }
	if ( is_array( $myfollowing->users ) ) {
		foreach ( $myfollowing->users as $key => $friends_val ) {
			$friendsData = array( 'user_id'             => esc_html( $logginID ),
			                      'friends_id'          => esc_html( $friends_val->id ),
			                      'friends_screen_name' => esc_html( $friends_val->screen_name ),
			                      'friends_name'        => utf8_encode( $friends_val->screen_name ),
			                      'friends_description' => utf8_encode( $friends_val->description ),
			                      'followers_count'     => esc_html( $friends_val->followers_count ),
			                      'friends_count'       => esc_html( $friends_val->friends_count ),
			                      'statuses_count'      => esc_html( $friends_val->statuses_count ),
			                      'location'            => utf8_encode( $friends_val->location ),
			                      'description'         => utf8_encode( $friends_val->description ),
			                      'profile_image_url'   => $friends_val->profile_image_url,
			                      'created_at'          => esc_html( $friends_val->created_at )
			);
			$friendsID   = $friends_val->id;

			$checkfriend = $wpdb->get_results( "SELECT * FROM friends where user_id ='" . sanitize_text_field( $logginID ) . "' AND friends_id ='" . sanitize_text_field( $friendsID ) . "'" );
			if ( empty( $checkfriend ) ) {
				$wpdb->insert( 'friends', $friendsData );
			}
		}
	}
}
function get_following2( $logginID ) {
	global $wpdb;
	if ( ! empty( $logginID ) ) {
		if ( ! empty( $_GET['s'] ) ) {
			$follow_search = $_GET['s'];
			$searchFollow  = "AND friends_screen_name='" . $follow_search . "'";
		}
		$following = $wpdb->get_results( " SELECT * FROM friends where user_id ='" . sanitize_text_field( $logginID ) . "' " . sanitize_text_field( $searchFollow ) . " order by followers_count desc" );
		return $following;
	}
}

$followingData = get_following2( $logginID );

$_SESSION['page'] = "following";
if ( isset( $_POST['search'] ) ) {
	$search   = esc_html( $_POST['s'] );
	$redirect = site_url() . '/wp-admin/admin.php?page=following&s='.$search; ?>
    <script> var redirect = "<?php echo $redirect; ?>";
			window.location.href = redirect;</script> <?php }

if ( isset( $_POST['unfollow'] ) ) {
	if ( ! empty( $_POST['user_id'] ) ) {
        $arrUserId=$_POST['user_id'];
        if ( is_array( $arrUserId ) ) {
            foreach ( $arrUserId as $uKey => $uVal ) {
                $res = $connection_tw->post( 'friendships/destroy', array( 'user_id' => sanitize_text_field($uVal) ) );
            }
        }
	}
	$redirect2 = site_url() . '/wp-admin/admin.php?page=following'; ?>
    <script> var redirect = "<?php echo $redirect2; ?>";
			window.location.href = redirect;</script>
<?php } ?>

<?php if ( empty( $_SESSION['access_token'] ) ) { ?>
<div class="ki_inbox_admin_settings twitter"><a href="<?php echo esc_html( $tw_auth_url ); ?>"><img
                src="<?php echo plugin_dir_url( __FILE__ ); ?>twitter/twitter-login-blue.png" class="twitter_logo"> </a>
	<?php } else { ?>
        <div class="twitter_user_head">
            <h1 class="tw_main_heading"><?php echo esc_html( $_SESSION['userInfo']->name ); ?> </h1>
            <div class="tw_left"> @<?php echo esc_html( $_SESSION['userInfo']->screen_name ); ?></div>
            <div class="tw_right"> <?php echo esc_html( $_SESSION['userInfo']->description ); ?></div>
        </div>
        <nav class="navbar navbar-light bg-faded align-content-center" style="background-color:#e3f2fd">
            <form class="form-inline mt-3 ml-5 mr-5" method="post">
                <input id="s-follow" class="form-control mr-sm-0 col-md-4 ml-auto" type="text" name="s"
                       value="<?php if ( isset( $_REQUEST['s'] ) ) {
					       echo esc_attr( $_REQUEST['s'] );
				       } ?>" placeholder="Search your following">
                <button id="s-btn" class="button button-primary" type="submit"
                        style="font-family: 'FontAwesome', 'Poppins', sans-serif;"
                        name="search"><?php echo esc_html__( 'SEARCH', 'ki-twitter-analytics' ); ?></button>
            </form>
        </nav>


        <div class="row">
            <div class="col-md-12">

                <form class="form-inline mt-3 ml-5 mr-5" method="post" id="unfollow">
                    <input type="checkbox" name="user_id[]" class="uncheckAll">
                    <button id="s-btn" class="button button-primary" type="submit"
                            style="font-family: 'FontAwesome', 'Poppins', sans-serif;"
                            name="unfollow"><?php echo esc_html__( "UNFOLLOW", 'ki-twitter-analytics' ); ?></button>

                    <table class="tableFollowers mt-3">
                        <tr id="header">
                            <th class="pr-2 pl-2"></th>
                            <th><?php echo esc_html__( 'USERNAME', 'ki-twitter-analytics' ); ?></th>
                            <th><?php echo esc_html__( 'RELATIONSHIP', 'ki-twitter-analytics' ); ?></th>
                            <th><?php echo esc_html__( 'ACCOUNT_CREATED', 'ki-twitter-analytics' ); ?></th>
                            <th><?php echo esc_html__( 'ACTIVE_LEVEL', 'ki-twitter-analytics' ); ?></th>
                            <th><?php echo esc_html__( 'KI_SCORE', 'ki-twitter-analytics' ); ?></th>
                        </tr>
						<?php
						foreach ( $followingData as $my_friend ) {

							?>

                            <tr id="underSpace">
                                <td class="f_checkbox">
                                    <input type="checkbox" class="pr-2 pl-2 user_id_checkbox" name="user_id[]"
                                           value="<?php echo esc_html( $my_friend->friends_id ) ?>" />
                                </td>
                                <td id="Uname">
                                    <img src="<?php echo esc_html( $my_friend->profile_image_url ) ?>" alt="p-img"
                                         class="mr-2">
                                    <p class="tw_screen"><?php echo esc_html( $my_friend->friends_screen_name ) ?></p>
                                </td>
                                <td><?php esc_html_e( 'FOLLOWING', 'ki-twitter-analytics' ); ?></td>
                                <!--<td>2 Weeks</td>-->
                                <td><?php echo str_replace( "+0000", "", $my_friend->created_at ) ?></td>
								<?php

								if ( $my_friend->statuses_count <= 10000 ) {
									echo '<td id="activeLevel"><i class="fa fa-arrow-down" aria-hidden="true"></i> ' . esc_html__( 'Low', 'ki-twitter-analytics' ) . '</td>';
								}
								if ( $my_friend->statuses_count > 10000 && $my_friend->statuses_count < 30000 ) {
									echo '<td id="activeLevel" style="color:#CE9350"><i class="fa fa-arrow-right" aria-hidden="true"></i> ' . esc_html__( 'Normal', 'ki-twitter-analytics' ) . '</td>';
								}
								if ( $my_friend->statuses_count >= 30000 ) {
									echo '<td id="activeLevel" style="color: red"><i class="fa fa-arrow-up" aria-hidden="true"></i> ' . esc_html__( 'High', 'ki-twitter-analytics' ) . '</td>';
								}

								?>

                                <td><?php echo esc_html( $my_friend->followers_count ); ?></td>
                            </tr>

							<?php
						}
						?>

                    </table>
                </form>
            </div>
        </div>
	<?php } ?>
