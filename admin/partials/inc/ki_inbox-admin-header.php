<?php ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
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

if (empty($followers_count)) $followers_count = '0';
if (empty($friends_count)) $friends_count = '0';
if (!empty($_GET['nickname'])) $nickname = $_GET['nickname'];
?>

<div class="row">
    <div class="col-md-12 mt-3 pl-3">
        <h2 class="com_heading">  <?php esc_html_e( "DEEP NETWORK ANALYZER", 'ki-twitter-analytics' );?></h2>
        <p class="com_sub_heading"> <?php esc_html_e( "Access information on influencers, competitors, partners and potential leads", 'ki-twitter-analytics' );?> </p>
    </div>

    <div class="col-md-12 mt-3 pl-3 row info_main">
        <h1 class="twitter_heading"><?php esc_html_e( "Competitive Intelligence", 'ki-twitter-analytics' );?></h1>
        <p class="info"><?php esc_html_e( "KI Competitive Marketing intelligence enables you to capture, analyze, and take action on your
            competitors. Learn your competitors social media strategy, content strategy, digital advertising tactics,
            and more.

            KI Social competitive intelligence follow a variety of sources to track your competitors’ complete digital
            footprint, including but not limited to product pricing, product reviews, news and events, product
            positioning, and content and social media strategy.

            Using this tool you can get a sample report on your competition approach.


            Assist brands in collecting and analyzing competitive intelligence about one or more of the following:
            products, website changes, customers, marketing strategies, and strategic investment(s)

            Provide a centralized platform for all competitive intelligence for a company to collaborate on and
            analyze", 'ki-twitter-analytics' );?></p>
    </div>

    <div class="col-md-12 mt-3 pl-3" style="background-color: #ceeffa; border:1px solid black;">
        <div class="row compbar">

            <div class="col-md-12 align-content-end">
                <div>
                    <form method="POST" action="#">
                        <p class="s_text"> <?php esc_html_e( "Submit Account of interest", 'ki-twitter-analytics' );?> </p><input id="search_twitter_ac" type="text"
                                                                                 value="<?php echo esc_attr($nickname); ?> "
                                                                                 name="nickname"
                                                                                 placeholder="e.g WikiTribune"
                                                                                 class="form-control-input-sm"
                                                                                 style="min-width: 550px;">
                        <button id="genGraph1" class="btn btn-md" type="submit" name="search"><?php esc_html_e( "Submit", 'ki-twitter-analytics' );?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (empty($_REQUEST['nickname'])) { ?> <h2 class="sample_report_heading"><?php esc_html_e( "Sample Report", 'ki-twitter-analytics' );?></h2><?php } ?>
<div class="row detailHeader">

    <div class="col-md-2 pl-4" id="profile-details">
        <p><strong> <?php esc_html_e( "Profile Demographics", 'ki-twitter-analytics' );?></strong></p>
        <p> <?php esc_html_e( "Discover of interest profile,", 'ki-twitter-analytics' );?></p>
        <p> <?php esc_html_e( "language, location, number of followers", 'ki-twitter-analytics' );?></p>
        <p> <?php esc_html_e( "number of following", 'ki-twitter-analytics' );?></p>
    </div>

    <div class="col-md-2 pl-4" id="profile-details">
        <p><span style="color: #1790cc"><?php if (!empty($_SESSION['userInfo']->profile_image_url)) {
                echo "<img src='" . esc_html($_SESSION['userInfo']->profile_image_url) . "' class='twitter_thumb'>";
            } else {
                echo "<strong> ".esc_html__('Profile Image','ki-twitter-analytics')."</strong>";
            } ?></p>
    </div>
    <div class="col-md-2 pl-4" id="profile-details">
        <p class="ml-5"><?php esc_html_e( "Language:", 'ki-twitter-analytics' );?> <span style="font-weight: bold"><?php esc_html_e( "English", 'ki-twitter-analytics' );?></span></p>
        <p><span style="color: #1790cc"><?php if(!empty($_SESSION['userInfo']->description)) { echo $_SESSION['userInfo']->description; } ?></p>
    </div>
    <div class="col-md-3 pl-4" id="comp-details">
        <p><span style="color: #1790cc"> <?php if (!empty($url)) {
                    echo esc_attr($url);
                } else {
                    esc_html_e( "Location", 'ki-twitter-analytics' );
                }; ?></span></p>
        <p><span style="color: #1790cc"> <?php if (!empty($location)) {
                    echo esc_attr($location);
                } else {
                    esc_html_e( "URL", 'ki-twitter-analytics' );
                }; ?></span></p>
    </div>
    <div class="col-md-3 pl-4">
        <p>
            <span style="color: #1790cc"><i class="fa fa-users" aria-hidden="true"></i></span>
            <span style="font-weight: bold"><?php echo esc_html($followers_count); ?></span> <?php esc_html_e( "# of Followers", 'ki-twitter-analytics' );?>
            <!--(2302 more than you)-->
        </p>
        <p>
            <span style="color: #1790cc"><i class="fas fa-user-plus"></i></span>
            <span style="font-weight: bold"><?php echo esc_html($friends_count); ?></span> <?php esc_html_e( "# of Following", 'ki-twitter-analytics' );?>
            <!--(2684 less than you)-->
        </p>
    </div>
</div>

