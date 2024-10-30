<?php
//namespace KiTwitterAnalytics;

require plugin_dir_path( __FILE__ ) . 'twitter/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key    = get_option( "ki_twitter_consumer_key" );
$consumer_secret = get_option( "ki_twitter_consumer_secret" );
define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', esc_html__( $consumer_key ) ); // add your app consumer key between single quotes
define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', esc_html__( $consumer_secret ) ); // add your app consumer
define( 'KI_TWITTER_ANALYTICS_OAUTH_CALLBACK', site_url() . '/wp-admin/admin-ajax.php?action=ki_twitter_analytics_login' ); // your app callback URL i.e.
$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET );

// get a request token from twitter
$request_token = $connection->oauth( 'oauth/request_token', array(
	'oauth_callback' => KI_TWITTER_ANALYTICS_OAUTH_CALLBACK
) );

// save twitter token info to the session
$_SESSION['oauth_token']        = esc_attr( $request_token['oauth_token'] );
$_SESSION['oauth_token_secret'] = esc_attr( $request_token['oauth_token_secret'] );

// not logged in, get and display the login with twitter link
$url              = $connection->url( 'oauth/authorize', array(
	'oauth_token' => esc_attr( $request_token['oauth_token'] )
) );
$_SESSION['page'] = "stream";

?>
<?php if ( empty( $_SESSION['access_token'] ) ) { ?>
<div class="ki_inbox_admin_settings twitter">
    <a href="<?php echo esc_html__( $url ); ?>"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>twitter/twitter-login-blue.png" class="twitter_logo">
    </a>
	<?php } else { ?>
        <div class="graphics">
            <div style="margin-top:60px;" class="graph-container">
                <div class="tabs">
                    <span class="graph-post-type" id="activeGraphTwPost" type='hour' social='tw'>Hour</span>
                    <span class="graph-post-type" type='week' social='tw'>Week</span>
                    <span class="graph-post-type" type='month' social='tw'>Month</span>
                </div>
                <div class="statistic" data-type="graph-post-type" style="display: none; justify-content: space-around; margin-top: 15px;">
                    <div class="tops" style="background: #1e9ef2;flex-direction: column;"><?php esc_html_e( 'Max post of an hour', 'ki-twitter-analytics' ); ?>
                        <p class="post-type-hour"></p></div>
                    <div class="tops" style="background: #1e9ef2;flex-direction: column;"><?php esc_html_e( 'Max post of the week day', 'ki-twitter-analytics' ); ?>
                        <p class="post-type-day"></p></div>
                    <div class="tops" style="background: #1e9ef2;flex-direction: column;"><?php esc_html_e( 'Max post of the month', 'ki-twitter-analytics' ); ?>
                        <p class="post-type-month"></p></div>
                </div>
                <canvas id="twPostContainer" class="exported-canvas chartjs-render-monitor" height="500" style="display: block; max-height: 400px; width: 1314px;" width="1642"></canvas>
            </div>
        </div>
	<?php } ?>

