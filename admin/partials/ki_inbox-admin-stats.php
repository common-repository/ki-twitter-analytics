<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://waelhassan.com
 * @since      1.0.0
 *
 * @package    Ki_inbox
 * @subpackage Ki_inbox/admin/partials
 */

//namespace KiTwitterAnalytics;

?>


<?php
$tab='';
if(!empty($_GET['tab']))$tab=sanitize_text_field($_GET['tab']);
if ( sanitize_text_field($tab) == "stream" ) {
	require_once plugin_dir_path( __FILE__ ) . '/ki_inbox-admin-stream.php';
} else {
	require_once plugin_dir_path( __FILE__ ) . '/ki_inbox-admin-inbox.php';
} ?>
