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

global $wpdb;
require plugin_dir_path( __FILE__ ) . 'twitter/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key    = get_option( "ki_twitter_consumer_key" );
$consumer_secret = get_option( "ki_twitter_consumer_secret" );
define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', esc_html( $consumer_key ) ); // add your app consumer key between single quotes
define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', esc_html( $consumer_secret ) ); // add your app consumer
define( 'KI_TWITTER_ANALYTICS_OAUTH_CALLBACK', site_url() . '/wp-admin/admin-ajax.php?action=ki_twitter_analytics_login' ); // your app callback URL i.e.
$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET );
global $wpdb;
// if(empty($_SESSION['access_token'])) {
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
// }
$_SESSION['page'] = "network";
$table_name       = $wpdb->prefix . "options";

$twitter_api_data = $wpdb->get_results( "SELECT * FROM " . sanitize_text_field( $table_name ) . " WHERE option_name IN('ki_twitter_consumer_key', 'ki_twitter_consumer_secret')" );

if ( ! empty( $twitter_api_data ) ) {
	$twitter_consumer_key    = esc_html( $twitter_api_data[0]->option_value );
	$twitter_consumer_secret = esc_html( $twitter_api_data[1]->option_value );
}



if ( ! empty( $_POST ) ) {
	ob_start();
    $form='';

    if(!empty($_POST['form']))$form=sanitize_text_field($_POST['form']);
	if ( sanitize_text_field($form) == 'twitter' ) {
		$twk = $ki_twitter_consumer_key = esc_html( $_POST['ki_twitter_consumer_key'] );
		$tws = $ki_twitter_consumer_secret = esc_html( $_POST['ki_twitter_consumer_secret'] );

		if ( ! empty( $_POST['ki_twitter_consumer_key'] ) && ! empty( $_POST['ki_twitter_consumer_secret'] ) ) {

			if ( empty( $twitter_api_data ) ) {
				$wpdb->query( "
					INSERT INTO " . sanitize_text_field( $table_name ) . " (option_name, option_value, autoload)
					VALUES ('ki_twitter_consumer_key', '" . sanitize_text_field( $ki_twitter_consumer_key ) . "', 'yes'),
						   ('ki_twitter_consumer_secret', '" . sanitize_text_field( $ki_twitter_consumer_secret ) . "', 'yes')
				" );
				ob_clean();
				ob_flush();
				echo "<script>location.reload();</script>";

			} else {
				$wpdb->query( "
					UPDATE " . sanitize_text_field( $table_name ) . "
					SET option_value='" . sanitize_text_field( $ki_twitter_consumer_key ) . "'
					WHERE option_name='" . sanitize_text_field( ki_twitter_consumer_key ) . "'
				" );
				$wpdb->query( "
					UPDATE " . sanitize_text_field( $table_name ) . " 
					SET option_value='" . sanitize_text_field( $ki_twitter_consumer_secret ) . "' 
					WHERE option_name='ki_twitter_consumer_secret'
				" );
				echo '<div class="notice notice-success is-dismissible">
	            	  	<p>' . esc_html__( 'Data successfully updated', 'ki-twitter-analytics' ) . '</p>
	         		  </div>';
				ob_clean();
				ob_flush();
				echo "<script>location.reload();</script>";

			}
		} else {
			echo '<div class="notice notice-error is-dismissible">
		  			<p>' . esc_html__( 'Empty data', 'ki-twitter-analytics' ) . '</p>
			  	  </div>';
			ob_clean();
			ob_flush();
			echo "<script>location.reload();</script>";
		}
		
		

	}
	
	if(!empty($_POST['ki_twitter_access_token'])){
		update_option('ki_twitter_access_token',sanitize_text_field($_POST['ki_twitter_access_token']));
	}
	if(!empty($_POST['ki_twitter_access_token_secret'])){
		update_option('ki_twitter_access_token_secret',sanitize_text_field($_POST['ki_twitter_access_token_secret']));
	}

	ob_clean();
	ob_flush();
	echo "<script>location.reload();</script>";
}

$twitter_access_token=get_option('ki_twitter_access_token',false);
$twitter_access_token_secret=get_option('ki_twitter_access_token_secret',false);

$tw_config='';
if(!empty($_GET['tw_config']))$tw_config=sanitize_text_field($_GET['tw_config']);
?>
<?php if ( sanitize_text_field($tw_config) != "yes" ) { ?>
    <div class="ki_inbox_admin_intro_page">
        <div class="intro_left">
            <h2 class="intro_head"> <?php esc_html_e( 'Easily analyze, benchmark & optimize Twitter', 'ki-twitter-analytics' ); ?></h2>
            <div class="into_text first">
                <div class="intro_thumb"><img
                            src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/flat-color-icons_conference-call.png"
                            class="intro_img"></div>
                <div class="into_inner_text">
                    <p class="in_head"><?php esc_html_e( 'Analyze your Twitter followers', 'ki-twitter-analytics' ); ?></p>
                    <p class="in_text"><?php esc_html_e( 'Understand who your content is (or is not) reaching with a', 'ki-twitter-analytics' ); ?></p>
                    <p class="in_text"> <?php esc_html_e( 'detailed demographic breakdown to help shape your', 'ki-twitter-analytics' ); ?></p>
                    <p class="in_text"> <?php esc_html_e( 'messaging and campaigns.', 'ki-twitter-analytics' ); ?></p>
                </div>
            </div>

            <div class="into_text">
                <div class="intro_thumb"><img
                            src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/flat-color-icons_combo-chart.png"
                            class="intro_img"></div>
                <div class="into_inner_text">
                    <p class="in_head"><?php esc_html_e( 'Analyze and share metrics that matter', 'ki-twitter-analytics' ); ?></p>
                    <p class="in_text"><?php esc_html_e( 'Understand who your content is (or is not) reaching with a', 'ki-twitter-analytics' ); ?></p>
                    <p class="in_text"> <?php esc_html_e( 'detailed demographic breakdown to help shape your', 'ki-twitter-analytics' ); ?></p>
                    <p class="in_text"> <?php esc_html_e( 'messaging and campaigns.', 'ki-twitter-analytics' ); ?></p>
                </div>
            </div>

        </div>
        <div class="intro_right">
            <img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/intro.png" class="intro_img">
        </div>
        <div class="into_next">
            <a href="<?php echo esc_url($tw_auth_url); ?>" class="button button-primary button-large" id="get_started"> <?php esc_html_e( 'Get Started', 'ki-twitter-analytics' ); ?></a>
            <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=ki_twitter_analytics&tw_config=yes"
               class="button button-primary button-large" id="tw_settings"> <?php esc_html_e( 'Settings', 'ki-twitter-analytics' ); ?></a>
        </div>
    </div>
<?php } else { ?>
    <div class="tw_tabs">
        <ul class="tw_tab">
            <li class="active"><a href="javaScript:void(0)" class="tw_settings_content"><?php esc_html_e( 'Settings', 'ki-twitter-analytics' ); ?></a></li>
            <li>
                <a href="javaScript:void(0)" class="tw_documentation_content"><?php esc_html_e( 'API Documentation', 'ki-twitter-analytics' ); ?></a>
            </li>
        </ul>
    </div>
    <div class="ki_inbox_admin_settings" id="tw_settings_content">
        <h1> KI Inbox </h1>
        <h2 class="sm-twitter-credentials"><?php esc_html_e( 'Twitter API credentials', 'ki-twitter-analytics' ); ?></h2>
        <p class="full">

			<?php esc_html_e( 'To start using Twitter Login Button you should get Consumer Key and Secret for your website.', 'ki-twitter-analytics' ); ?>
			<?php esc_html_e( 'Please follow to this instruction »', 'ki-twitter-analytics' ); ?>
			<?php esc_html_e( 'Callback URL :', 'ki-twitter-analytics' ); ?> <?php echo site_url(); ?>/wp-admin/admin-ajax.php</p>
        <form class="sm-menu-container" method="post" action>
            <p class="half"><input type="hidden" name="form" value="twitter">
                <label for="consumer_key"><?php esc_html_e( 'Consumer key:', 'ki-twitter-analytics' ); ?></label>
                <input type="text" id="consumer_key" name="ki_twitter_consumer_key"
                       value="<?php echo esc_attr($twitter_consumer_key); ?>"></p>
            <p class="half"><label for="consumer_secret"><?php esc_html_e( 'Consumer secret:', 'ki-twitter-analytics' ); ?></label>
                <input type="password" id="consumer_secret" name="ki_twitter_consumer_secret"
                       value="<?php echo esc_attr($twitter_consumer_secret); ?>">
            </p>
			
			<div class="settings-info"><?php esc_html_e( 'The access tokens will be used in the Video Twitter widget.', 'ki-twitter-analytics' ); ?></div>
			
			<p class="half"><label for="access_token"><?php esc_html_e( 'Access token:', 'ki-twitter-analytics' ); ?></label>
                <input type="text" id="access_token" name="ki_twitter_access_token"
                       value="<?php echo esc_attr($twitter_access_token); ?>">
            </p>
			
			<p class="half"><label for="access_token_secret"><?php esc_html_e( 'Access token secret:', 'ki-twitter-analytics' ); ?></label>
                <input type="password" id="access_token_secret" name="ki_twitter_access_token_secret"
                       value="<?php echo esc_attr($twitter_access_token_secret); ?>">
            </p>
			
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'ki-twitter-analytics' ); ?>">
            </p>
        </form>
    </div>


    <div class="ki_inbox_admin_settings" id="tw_documentation_content">
        <h1><?php esc_html_e( 'API Documentation', 'ki-twitter-analytics' ); ?></h1>
        <h2 class="sm-twitter-credentials"> <?php esc_html_e( 'How to get your Twitter API Keys', 'ki-twitter-analytics' ); ?></h2>
        <div class="ins_content">
            <p>
                <strong><?php esc_html_e( 'STEP 1: Please go to:', 'ki-twitter-analytics' ); ?></strong> <a href="https://twitter.com/">https://twitter.com/</a> and log in
				<?php esc_html_e( 'to your Twitter account.', 'ki-twitter-analytics' ); ?>

				<?php esc_html_e( 'Or sign up if you don\'t have an account yet.', 'ki-twitter-analytics' ); ?></p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw1.png"></p>
            <p><strong><?php esc_html_e( 'STEP 2: Creating a Twitter Application.', 'ki-twitter-analytics' ); ?></strong>
                <strong><?php esc_html_e( 'Go to:', 'ki-twitter-analytics' ); ?></strong> <a href="https://dev.twitter.com/apps/new">https://dev.twitter.com/apps/new</a>
				<?php esc_html_e( '(1) and click to the “Create an app” button (2).', 'ki-twitter-analytics' ); ?></p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw2.png"></p>

            <p> <?php esc_html_e( 'Enter your Application Name (1), Description(2) and your website address (3).', 'ki-twitter-analytics' ); ?> </p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw3.png"></p>
            <p><?php esc_html_e( 'You can leave the Callback, Service and Privacy policy URLs empty. Fill in the last field (4), write', 'ki-twitter-analytics' ); ?>
				<?php esc_html_e( 'about how your application will be used. And click Create button (5).', 'ki-twitter-analytics' ); ?></p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw4.png"></p>
            <p><?php esc_html_e( 'Review Developer Terms and click Create.', 'ki-twitter-analytics' ); ?></p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw5.png"></p>
            <p><?php esc_html_e( 'Your app is created. On this screen you can view the details of the app and open the Keys and tokens tab.', 'ki-twitter-analytics' ); ?>
            </p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw6.png"></p>
            <p><strong><?php esc_html_e( 'STEP 3:', 'ki-twitter-analytics' ); ?></strong>
				<?php esc_html_e( 'Copy the consumer key (API key) and consumer secret from the screen into your application.', 'ki-twitter-analytics' ); ?>

            </p>
            <p><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/images/tw7.png"></p>
        </div>
    </div>
<?php } ?>

