<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://waelhassan.com
 * @since      1.0.0
 *
 * @package    Ki_inbox
 * @subpackage Ki_inbox/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ki_inbox
 * @subpackage Ki_inbox/admin
 * @author     Wael Hassan wael.hassan@gmail.com 
 */

//namespace KiTwitterAnalytics;

require plugin_dir_path( __FILE__ ) . 'partials/twitter/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

class Ki_inbox_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		session_start();
		$this->plugin_name = sanitize_text_field( $plugin_name );
		$this->version     = sanitize_text_field( $version );  
		if ( isset( $_SESSION['access_token'] ) ) {
			$this->useranalytics(); 
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ki_inbox_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ki_inbox_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        $arrPages=Array(
            'ki_twitter_analytics',
            'inbox',
            'stream',
            'network',
            'followers',
            'following',
            'switch_account',
        );
        $page='';
        if(!empty($_GET['page']))$page=$_GET['page'];
        if(array_search($page,$arrPages)!==false) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ki_inbox-admin.css', array(), $this->version, 'all');
            wp_enqueue_style("ki-font-awesome", plugin_dir_url(__FILE__) . 'css/font-awesome.min.css', array(), $this->version, 'all');
            wp_enqueue_style("ki-font-boostrap", plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        }

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ki_inbox_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ki_inbox_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script('ki-twitter-analytics-chart', plugin_dir_url( __FILE__ ) . 'js/Chart.min.js', array(
			'jquery'
		), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ki', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	public function register_ki_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}
	
	public function ki_include_menu() {

		$ki_publish = is_plugin_active( 'ki-publish/sm-publish.php' );
		
		$opt_ki_publish=get_option( 'pl_ki_publish_active', false );

		
		if ( $ki_publish==true AND  $opt_ki_publish!=='active' ) {
            update_option( 'pl_ki_publish_active', 'active');
            wp_redirect( $_SERVER['REQUEST_URI'] );

        }
		
        if($ki_publish==false AND $opt_ki_publish==='active' ){
            update_option( 'pl_ki_publish_active', '');
            wp_redirect( $_SERVER['REQUEST_URI'] );

        }

    }

	public function ki_settings_page() {
		
		$opt_ki_publish=get_option( 'pl_ki_publish_active', false );
		$menu_slug='ki_twitter_analytics';
		$menu_position=1;
		if($opt_ki_publish!=='active'){
			add_menu_page( esc_html__( ' Ki twitter analytics', 'ki-twitter-analytics' ), esc_html__( 'Ki twitter analytics', 'ki-twitter-analytics' ), 'manage_options', $this->plugin_name, array(
				$this,
				'load_admin_page_content'
			), // Calls function to require the partial
				KI_TWITTER_ANALYTICS_BASE_URL.'admin/images/icon.png' , 6 );
		}else{
			$menu_slug='ki_options_page';
			$menu_position=20;
			add_submenu_page( $menu_slug, esc_html__( 'Ki twitter analytics', 'ki-twitter-analytics' ), esc_html__( 'Ki twitter analytics', 'ki-twitter-analytics' ), 'manage_options', $this->plugin_name, array(
				$this,
				'load_admin_page_content'
			),1);
		}


		add_submenu_page( $menu_slug, esc_html__( 'Inbox', 'ki-twitter-analytics' ), esc_html__( 'Inbox', 'ki-twitter-analytics' ), 'manage_options', 'inbox', array(
			$this,
			'inbox_content'
		),$menu_position++ );
		add_submenu_page( $menu_slug, esc_html__( 'Stream', 'ki-twitter-analytics' ), esc_html__( 'Stream', 'ki-twitter-analytics' ), 'manage_options', 'stream', array(
			$this,
			'stream_content'
		),$menu_position++ );

		add_submenu_page( $menu_slug, esc_html__( 'Network', 'ki-twitter-analytics' ), esc_html__( 'Network', 'ki-twitter-analytics' ), 'manage_options', 'network', array(
			$this,
			'inbox_competition'
		),$menu_position++ );

		add_submenu_page( $menu_slug, esc_html__( 'Followers', 'ki-twitter-analytics' ), esc_html__( 'Followers', 'ki-twitter-analytics' ), 'manage_options', 'followers', array(
			$this,
			'inbox_followers'
		),$menu_position++ );

		add_submenu_page( $menu_slug, esc_html__( 'Following', 'ki-twitter-analytics' ), esc_html__( 'Following', 'ki-twitter-analytics' ), 'manage_options', 'following', array(
			$this,
			'inbox_following'
		),$menu_position++ );

		add_submenu_page( $menu_slug, esc_html__( 'Switch Account', 'ki-twitter-analytics' ), esc_html__( 'Switch account', 'ki-twitter-analytics' ), 'manage_options', 'switch_account', array(
			$this,
			'swtich_account_content'
		),$menu_position++ );

	}

	// Load the plugin admin settings page partial.
	public function load_admin_page_content() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-display.php' );
	}

	// Load the plugin admin inbox page partial.
	public function inbox_content() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-inbox.php' );
	}

	// Load the plugin admin inbox page partial.
	public function inbox_stats() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-stats.php' );
	}

	// Load the plugin admin inbox page partial.
	public function inbox_competition() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-competition.php' );
	}

	// Load the plugin admin inbox page partial.
	public function inbox_followers() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-followers.php' );
	}

	// Load the plugin admin inbox page partial.
	public function inbox_following() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-following.php' );
	}

	// Load the plugin admin stream page partial.
	public function stream_content() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-stream.php' );;
	}


	// Load the plugin admin switch account page partial.
	public function swtich_account_content() {
		require_once plugin_dir_path( __FILE__ ) . esc_attr( 'partials/ki_inbox-admin-switch-account.php' );
	}

	public function twitter_login() {
		$consumer_key    = get_option( "ki_twitter_consumer_key" );
		$consumer_secret = get_option( "ki_twitter_consumer_secret" );
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', $consumer_key ); // add your app consumer key between single quotes
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', $consumer_secret ); // add your app consumer
		if ( isset( $_REQUEST['oauth_verifier'], $_REQUEST['oauth_token'] ) && $_REQUEST['oauth_token'] == $_SESSION['oauth_token'] ) { //In project use this session to change login header after successful login
			$request_token                       = [];
			$request_token['oauth_token']        = sanitize_text_field( $_SESSION['oauth_token'] );
			$request_token['oauth_token_secret'] = sanitize_text_field( $_SESSION['oauth_token_secret'] );
			$connection                          = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret'] );
			$access_token                        = $connection->oauth( "oauth/access_token", array(
				"oauth_verifier" => $_REQUEST['oauth_verifier']
			) );
			$_SESSION['access_token']            = $access_token;
			// redirect user back to index page
			if ( isset( $_SESSION['access_token'] ) ) {

				$page = "/wp-admin/admin.php?page=" . sanitize_text_field( $_SESSION['page'] );
				wp_redirect( $page );
				exit();
			}
		}
	}

	public function get_twitter_data() {
		global $wpdb;
		$twitter_msg = "twitter_messages";
		$tw_posts    = "tw_posts";
		$tw_retweets = "tw_retweets";
		$tw_mentions = "tw_mentions";
		$userID      = sanitize_text_field($_SESSION['access_token']['user_id']);
		if ( sanitize_text_field($_REQUEST['type']) == "message" ) {
			$twitter_api_data = $wpdb->get_results( "SELECT recipient,sender,created_timestamp,userId FROM " . sanitize_text_field( $twitter_msg ) . " WHERE userId=" . sanitize_text_field( $userID ) );
			echo json_encode( array(
				'userId' => $userID,
				'data'   => $twitter_api_data,
				'type'   => 'message'
			) );
			exit;
		} else {
			$finalData             = array();
			$posts_data            = $wpdb->get_results( "SELECT created_at FROM " . sanitize_text_field( $tw_posts ) . " WHERE userId=" . sanitize_text_field( $userID ) );
			$retweets_data         = $wpdb->get_results( "SELECT created_at FROM " . sanitize_text_field( $tw_retweets ) . " WHERE userId=" . sanitize_text_field( $userID ) );
			$mentions_data         = $wpdb->get_results( "SELECT created_at FROM " . sanitize_text_field( $tw_mentions ) . " WHERE userId=" . sanitize_text_field( $userID ) );
			$finalData['posts']    = $posts_data;
			$finalData['retweets'] = $retweets_data;
			$finalData['mentions'] = $mentions_data;
			$finalData['userId']   = $userID;
			if ( ! isset( $_SESSION['twPostStatus'] ) ) {
				$this->twitterApi( 'statuses/user_timeline', array(
					'user_id' => $userID
				), 'tw_posts' );
				$this->twitterApi( 'statuses/retweets_of_me', array(
					'count' => 100
				), 'tw_retweets' );
				$this->twitterApi( 'statuses/mentions_timeline', array(
					'count' => 200
				), 'tw_mentions' );
				$_SESSION['twPostStatus'] = true;
			}
			echo json_encode( $finalData );
			exit;
		}

	}

	public function twitterApi( $req_url, $params, $table ) {
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', get_option( "ki_twitter_consumer_key" ) ); // add your app consumer key between single quotes
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', get_option( "ki_twitter_consumer_secret" ) ); // add your app consumer
		$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret'] );
		$res        = $connection->get( $req_url, $params );
		$status     = $this->insertData( $table, $res );
		if ( $res ) {
			$max_id = $res[ count( $res ) - 1 ]->id;
		} else {
			return true;
		}
		if ( $status ) {
			return true;
		}
		$params['max_id'] = $max_id;
	}

	public function insertData( $table, $res ) {
		global $wpdb;
		$userID = sanitize_text_field($_SESSION['access_token']['user_id']);
		$key    = ( $table == 'tw_posts' ) ? 'postId' : ( ( $table == 'tw_retweets' ) ? 'rtId' : 'mentionId' );

		$last = $wpdb->get_results( "SELECT created_at FROM " . sanitize_text_field( $table ) . " WHERE (userId=" . sanitize_text_field( $userID ) . ") limit 1" );
		$last = $last ? $last->created_at : $last;
		foreach ( $res as $value ) {
			if ( (int) $last < (int) strtotime( $value->created_at ) ) {
				$posts = [
					$key         => sanitize_text_field( $value->id ),
					'text'       => sanitize_text_field( $value->text ),
					'userId'     => sanitize_text_field( $userID ),
					'created_at' => sanitize_text_field( strtotime( $value->created_at ) )
				];
				$wpdb->insert( $table, $posts );
			} else {
				return true;
			}
		}
	}

	public function useranalytics() {
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', get_option( "ki_twitter_consumer_key" ) ); // add your app consumer key between single quotes
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', get_option( "ki_twitter_consumer_secret" ) ); // add your app consumer
		$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, sanitize_text_field($_SESSION['access_token']['oauth_token']), sanitize_text_field($_SESSION['access_token']['oauth_token_secret'] ));
		$res = $connection->get( 'direct_messages/events/list' );
		return $this->checkNext( $res );
	}

	public function checkNext( $res ) {
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', get_option( "ki_twitter_consumer_key" ) ); // add your app consumer key between single quotes
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', get_option( "ki_twitter_consumer_secret" ) ); // add your app consumer
		$connection = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, sanitize_text_field($_SESSION['access_token']['oauth_token']), sanitize_text_field($_SESSION['access_token']['oauth_token_secret']) );
		if ( isset( $res->events ) ) {
			foreach ( $res->events as $value ) {
				$message = [
					'messageId'         => sanitize_text_field( $value->id ),
					'message'           => $value
						->message_create
						->message_data->text,
					'recipient'         => $value
						->message_create
						->target->recipient_id,
					'sender'            => $value
						->message_create->sender_id,
					'userId'            => sanitize_text_field( $_SESSION['access_token']['user_id'] ),
					'created_timestamp' => $value->created_timestamp
				];

				if ( $this->addMessage( $message ) ) {
					return redirect( 'home' );
				}
			}

			if ( isset( $res->next_cursor ) ) {
				$res = $connection->get( 'direct_messages/events/list', [ 'cursor' => $res->next_cursor ] );
				$this->checkNext( $res );
			}
		}
	}

	public function addMessage( $message ) {
		global $wpdb;
		$MessageID = $message['messageId'];
		$data      = $wpdb->get_results( "SELECT created_timestamp FROM twitter_messages WHERE (messageId=" . sanitize_text_field( $MessageID ) . ") limit 1" );
		if ( ! $data ) {
			$wpdb->insert( "twitter_messages", $message );
		}
	}

	public function add_follower_data() {
		$action      = sanitize_text_field($_REQUEST['action']);
		$id          = sanitize_text_field($_REQUEST['id']);
		$screen_name = sanitize_text_field($_REQUEST['screen_name']);


	}

	public function follow_user_ajax() {
		$data = Array();
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_KEY', get_option( "ki_twitter_consumer_key" ) ); // add your app consumer key between single quotes
		define( 'KI_TWITTER_ANALYTICS_CONSUMER_SECRET', get_option( "ki_twitter_consumer_secret" ) ); // add your app consumer
		$connection  = new TwitterOAuth( KI_TWITTER_ANALYTICS_CONSUMER_KEY, KI_TWITTER_ANALYTICS_CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret'] );
		$action      = sanitize_text_field($_REQUEST['action']);
		$id          = sanitize_text_field($_REQUEST['id']);
		$screen_name = sanitize_text_field($_REQUEST['screen_name']);
		if ( ! empty( $id ) ) {
			$res = $connection->post( 'friendships/create', array( 'user_id' => $id ) );

			if ( empty( $res->errors ) && $res->following == false ) {

				echo "You just followed " . $data['screen_name'];

				$dataAction = array(
					'action'      => 'follow',
					'data_action' => $screen_name,
					'comments'    => 'UI ACTION'
				);
				//saveUserAction($dataAction);

			} else if ( $res->following == true ) {

				echo "You already following " . esc_attr( $data['screen_name'] );

			} else {

				echo esc_attr( $res->errors[0]->message );
				exit;

			}
		}

		exit;

	}
}

