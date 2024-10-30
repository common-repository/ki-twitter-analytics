<?php

/**
 * Fired during plugin activation
 *
 * @link       https://waelhassan.com
 * @since      1.0.0
 *
 * @package    Ki_inbox
 * @subpackage Ki_inbox/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ki_inbox
 * @subpackage Ki_inbox/includes
 * @author     Wael Hassan wael.hassan@gmail.com    
 */
//namespace KiTwitterAnalytics;
class Ki_inbox_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
   global $wpdb; 
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  $twitter_messages = 'twitter_messages';  // table name
  $charset_collate = $wpdb->get_charset_collate();
	 //Check to see if the table exists already, if not, then create it
	if($wpdb->get_var( "show tables like '$twitter_messages'" ) != $twitter_messages ) 
	 {
		   $sql = "CREATE TABLE $twitter_messages (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `messageId` varchar(255) NOT NULL,
				  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
				  `recipient` varchar(255) DEFAULT NULL,
				  `sender` varchar(255) DEFAULT NULL,
				  `created_timestamp` varchar(255) NOT NULL,
				  `userId` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`) USING BTREE
			) $charset_collate;";

	   
	   dbDelta( $sql );
	   }

     $tw_posts = 'tw_posts';  // table name
	 if($wpdb->get_var( "show tables like '$tw_posts'" ) != $tw_posts ) 
	  {
		   $sql2 = "CREATE TABLE $tw_posts (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postId` varchar(255) DEFAULT NULL,
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `userId` varchar(255) NOT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	   dbDelta( $sql2 );
	 }	
	 
    $tw_mentions = 'tw_mentions';  // table name
	if($wpdb->get_var( "show tables like '$tw_mentions'" ) != $tw_mentions ) 
	 {
		   $sql3 = "CREATE TABLE $tw_mentions (`id` int(11) NOT NULL AUTO_INCREMENT,
`mentionId` varchar(255) DEFAULT NULL,
`text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
`userId` varchar(255) NOT NULL,
`created_at` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`id`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

	   dbDelta( $sql3 );
	 }	

    $tw_retweets = 'tw_retweets';  // table name
	if($wpdb->get_var( "show tables like '$tw_retweets'" ) != $tw_retweets ) 
	 {
		   $sql4 = "CREATE TABLE $tw_retweets (`id` int(11) NOT NULL AUTO_INCREMENT,
`rtId` varchar(255) DEFAULT NULL,
`text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
`userId` varchar(255) NOT NULL,
`created_at` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

	   
	   dbDelta( $sql4 );
	 }

    $followers = 'followers';  // table name
	if($wpdb->get_var( "show tables like '$followers'" ) != $followers ) 
	 {
		   $sql5 = "CREATE TABLE `followers`( `id` bigint(20) NOT NULL AUTO_INCREMENT, `user_id` bigint(20) NOT NULL, `followers_id` bigint(20) NOT NULL, `followers_screen_name` varchar(100) NOT NULL, `followers_name` varchar(100) NOT NULL, `followers_description` varchar(255) NOT NULL, `followers_count` int(11) NOT NULL, `friends_count` int(11) NOT NULL, `statuses_count` varchar(100) NOT NULL, `location` varchar(255) NOT NULL, `description` varchar(255) NOT NULL, `profile_image_url` varchar(255) NOT NULL, `created_at` varchar(255) NOT NULL, `followers_full_info` text NOT NULL, `direct_notify_sent` int(1) NOT NULL DEFAULT '0', `fetched_to_notify` int(1) NOT NULL DEFAULT '0', `enable_notifications` int(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	   
	   dbDelta( $sql5 );
	 }	

    $friends = 'friends';  // table name
	if($wpdb->get_var( "show tables like '$friends'" ) != $friends ) 
	 {
		   $sql6 = "CREATE TABLE `friends`( `id` int(255) NOT NULL AUTO_INCREMENT, `user_id` longtext NOT NULL, `friends_id` longtext NOT NULL, `friends_screen_name` varchar(255) NOT NULL, `friends_name` varchar(255) NOT NULL, `friends_description` longtext NOT NULL, `followers_count` int(255) NOT NULL, `friends_count` int(255) NOT NULL, `statuses_count` varchar(255) NOT NULL, `location` varchar(255) NOT NULL, `description` varchar(255) NOT NULL, `profile_image_url` varchar(255) NOT NULL, `created_at` varchar(255) NOT NULL, `friends_full_info` longtext NOT NULL, `direct_notify_sent` int(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	   
	   dbDelta( $sql6 );
	 }	 
}
}