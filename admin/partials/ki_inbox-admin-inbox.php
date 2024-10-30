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

// get a request token from twitter
$request_token = $connection->oauth( 'oauth/request_token', array(
	'oauth_callback' => KI_TWITTER_ANALYTICS_OAUTH_CALLBACK
) );

// save twitter token info to the session
$_SESSION['oauth_token']        = esc_html( $request_token['oauth_token'] );
$_SESSION['oauth_token_secret'] = esc_html( $request_token['oauth_token_secret'] );

// not logged in, get and display the login with twitter link
$url              = $connection->url( 'oauth/authorize', array(
	'oauth_token' => esc_attr( $request_token['oauth_token'] )
) );
$_SESSION['page'] = "inbox";
?>
<?php if ( empty( $_SESSION['access_token'] ) ) { ?>
<div class="ki_inbox_admin_settings twitter">
    <a href="<?php echo esc_attr( $url ); ?>"><img src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) ); ?>twitter/twitter-login-blue.png" class="twitter_logo">
    </a>
	<?php } else { ?>
        <div class="graphics">
            <div style="margin-top:60px;" class="graph-container">
                <div class="tabs">
                    <span class="graph-type" id="activeGraphTw" type='hour' social='tw'><?php esc_html_e( 'Hour', 'ki-twitter-analytics' ); ?></span>
                    <span class="graph-type" type='week' social='tw'><?php esc_html_e( 'Week', 'ki-twitter-analytics' ); ?></span>
                    <span class="graph-type" type='month' social='tw'><?php esc_html_e( 'Month', 'ki-twitter-analytics' ); ?></span>
                </div>
                <div class="statistic" data-type="graph-type" style="display: none; justify-content: space-around; margin-top: 15px;">
                    <div class="tops" style="background: #1e9ef2;flex-direction: column;"><?php esc_html_e( 'Max post of an hour', 'ki-twitter-analytics' ); ?>
                        <p class="message-type-hour"></p></div>
                    <div class="tops" style="background: #1e9ef2;flex-direction: column;"><?php esc_html_e( 'Max post of the week day', 'ki-twitter-analytics' ); ?>
                        <p class="message-type-day"></div>
                    <div class="tops" style="background: #1e9ef2;flex-direction: column;"><?php esc_html_e( 'Max post of the month', 'ki-twitter-analytics' ); ?>
                        <p class="message-type-month"></div>
                </div>
                <canvas id="twContainer" width="100%" class="exported-canvas chartjs-render-monitor" height="500" style="display: block; width: auto; max-height: 400px;"></canvas>
            </div>
        </div>
	<?php } ?>
