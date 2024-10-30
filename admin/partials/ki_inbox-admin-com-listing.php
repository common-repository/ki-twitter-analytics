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

function get_follower( $tw_userID ) {
	global $wpdb;
	$follower = $wpdb->get_results( " SELECT * FROM followers where user_id ='" . sanitize_text_field( $tw_userID ) . "' order by followers_count desc" );

	return $follower;
}

function get_following( $tw_userID ) {
	global $wpdb;
	$following = $wpdb->get_results( " SELECT * FROM friends where user_id ='" . sanitize_text_field( $tw_userID ) . "' order by followers_count desc" );

	return $following;
}

if ( empty( $tw_userID ) ) {
	$tw_userID = '';
}
$followerData  = get_follower( $tw_userID );
$followingData = get_following( $tw_userID );
if (empty($nickname)) $nickname = '-';
?>
<div class="tw_main_listing">
    <div class="boxhead mt-5">
        <h1 class="twitter_heading"><?php esc_html_e( "Top 20", 'ki-twitter-analytics' ); ?> </h1>
        <ul class="nav justify-content-center" id="myNav">
            <li class="nav-item first"> <?php esc_html_e( "Tweeps", 'ki-twitter-analytics' ); ?></li>
            <li class="nav-item"><?php esc_html_e( "Following", 'ki-twitter-analytics' ); ?></li>
        </ul>
    </div>
    <div class="wrapper">
        <div class="row">
            <div class="col-md-2">
                <p><strong> <?php esc_html_e( "Audience", 'ki-twitter-analytics' ); ?></strong></p>
                <p> <?php esc_html_e( "Discover of top influencers on the,", 'ki-twitter-analytics' ); ?></p>
                <p> <?php esc_html_e( "account of interest follower/following", 'ki-twitter-analytics' ); ?> </p>
            </div>
            <div class="col-md-10">
                <div class="box1 tab-content secondary">
                    <div id="follower20" class="tab-pane fade show active">
                        <div class="nested1">
                            <div id="sec1">
								<?php $i = 0;
								if ( ! empty( $followerData ) ) {
									foreach ( $followerData as $key => $val ) { //if($i == 0){echo '<div id="sec1">';}
										if ( $i == 19 ) {
											echo ' </div><div id="sec$i">';
											break;
										}
										?>
                                        <div class="inCard test<?= $i ?>">
                                            <div class="topRow">
                                                <div>
                                                    <img class="pl-3" src="<?php echo esc_html( $val->profile_image_url ); ?>"
                                                         alt="p-img" height="40px" width="auto">
                                                </div>
                                                <div>
                                                    <p class="name mr-2"><?php echo esc_html( $val->followers_name ); ?></p>
                                                    <p id="handle">@<?php echo esc_html($val->followers_screen_name); ?></p>
                                                </div>
                                                <div id="inline">
                                                    <p id="city"><?php echo utf8_decode( $val->location ); ?></p>
                                                    <p id="folwin"><?php esc_html_e( "Following:", 'ki-twitter-analytics' ); ?><?php echo esc_html( $val->friends_count ); ?> </p>
                                                    <p id="folwer"><?php esc_html_e( "Followers:", 'ki-twitter-analytics' ); ?><?php echo esc_html( $val->followers_count ); ?> </p>
                                                    <p id="tweets"><?php esc_html_e( "Tweets:", 'ki-twitter-analytics' ); ?><?php echo esc_html( $val->statuses_count ); ?> </p>
                                                </div>
                                                <div>
                                                    <button id="card-follow_<?php echo esc_html( $val->followers_id ); ?>"
                                                            data-name="galapatha"
                                                            data-id="<?php echo esc_html( $val->followers_id ); ?>"
                                                            class="btn btn-sm btn-outline-secondary followbtn"><?php esc_html_e( "Follow", 'ki-twitter-analytics' ); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="bottomRow mt-1">
                                                <p id="t-body"
                                                   class="small"><?php echo utf8_decode( esc_html( $val->description ) ); ?> </p>
                                            </div>
                                        </div>
										<?php $i ++;
									}
								} ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="box1 tab-content">

                    <div id="following20" class="tab-pane show active tweeps">

                        <div class="nested1">
                            <div id="sec1">
								<?php $i = 0;
								if ( ! empty( $followingData ) ) {
									foreach ( $followingData as $key => $val ) { //if($i == 0){echo '<div id="sec1">';}
										if ( $i == 19 ) {
											echo ' </div><div id="sec$i">';
											break;
										}
										?>
                                        <div class="inCard test<?= $i ?>">
                                            <div class="topRow">
                                                <div>
                                                    <img class="pl-3" src="<?php echo esc_html( $val->profile_image_url ); ?>"
                                                         alt="p-img" height="40px" width="auto">
                                                </div>
                                                <div>
                                                    <p class="name mr-2"><?php echo esc_html( $val->friends_name ); ?></p>
                                                    <p id="handle">@<?php echo esc_html( $val->friends_screen_name ); ?></p>
                                                </div>
                                                <div id="inline">
                                                    <p id="city"><?php echo utf8_decode( $val->location ); ?></p>
                                                    <p id="folwin"><?php esc_html_e( "Following:", 'ki-twitter-analytics' ); ?><?php echo esc_html( $val->friends_count ); ?> </p>
                                                    <p id="folwer"><?php esc_html_e( "Followers:", 'ki-twitter-analytics' ); ?><?php echo esc_html( $val->followers_count ); ?> </p>
                                                    <p id="tweets"><?php esc_html_e( "Tweets:", 'ki-twitter-analytics' ); ?><?php echo esc_html( $val->statuses_count ); ?> </p>
                                                </div>
                                                <div>
                                                    <button id="card-follow_<?php echo esc_html( $val->friends_id ); ?>"
                                                            data-name="galapatha"
                                                            data-id="<?php echo esc_html( $val->friends_id ); ?>"
                                                            class="btn btn-sm btn-outline-secondary followbtn"><?php esc_html_e( "Follow", 'ki-twitter-analytics' ); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="bottomRow mt-1">
                                                <p id="t-body"
                                                   class="small"><?php echo utf8_decode( $val->description ); ?> </p>
                                            </div>
                                        </div>
										<?php $i ++;
									}
								} ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
<div class="tw_keyword_main">
    <div class="row">
        <div class="col-md-2">
            <p><strong> <?php esc_html_e( "Audience", 'ki-twitter-analytics' ); ?></strong></p>
            <p> <?php esc_html_e( "Discover of top influencers on the,", 'ki-twitter-analytics' ); ?></p>
            <p> <?php esc_html_e( "account of interest follower/following ", 'ki-twitter-analytics' ); ?></p>
        </div>
        <div class="col-md-10">
            <div class="box2">
                <h4 class="pl-3 pb-1"> <?php echo "Hashtags List"; ?></h4>
                <div class="nested2">
					<?php $my_tweets = $connection_tw->get( 'statuses/user_timeline', array(
						'screen_name' => $nickname,
						'count'       => 200
					) );

					$hashtags = array();
                    if(!empty($my_tweets)){
					foreach ( $my_tweets as $my_tweet ) {
						foreach ( $my_tweet->entities->hashtags as $hashtag ) {

							$hashtags[] = $hashtag->text;
						}
					}
					}

					$user_hashtags = array_unique( $hashtags ); ?>
					<?php if ( ! empty( $_REQUEST['nickname'] ) ) {
						foreach ( $user_hashtags as $user_hashtag ) { ?>
                            <button id="lorem" type="button" class="btn btn-secondary btn-sm">
                                #<?php echo esc_html( $user_hashtag ); ?></button>
						<?php }
					} else { ?>
                        <button type="button" class="btn btn-secondary btn-sm"><?php esc_html_e( "#hashtag1", 'ki-twitter-analytics' ); ?></button>
                        <button type="button" class="btn btn-secondary btn-sm"><?php esc_html_e( "#hashtag2", 'ki-twitter-analytics' ); ?></button>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


