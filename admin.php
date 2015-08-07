<?php

function cpm_wp_poll_admin_menu() {
	add_menu_page('CPM All in one poll Settings', 'CPM All in one poll Settings', 'manage_options', 'cpm-wp-simple-poll', 'cpm_wp_poll_options_page' );
}
add_action('admin_menu', 'cpm_wp_poll_admin_menu' );

add_action( 'admin_init', 'cpm_wp_poll_settings_init' );
function cpm_wp_poll_settings_init(  ) {

    register_setting( 'cpmPollPluginPage', 'cpm_wp_poll_settings' );

    add_settings_section(
        'cpm_wp_poll_pluginPage_section',
        __( 'Basic Options', '_cpmpoll' ),
        'cpm_wp_poll_settings_section_callback',
        'cpmPollPluginPage'
    );

    add_settings_field(
        'cpm_wp_poll_allow_max_option',
        __( 'Maximum poll option allowed for a user', '_cpmpoll' ),
        'cpm_wp_poll_allow_max_option_render',
        'cpmPollPluginPage',
        'cpm_wp_poll_pluginPage_section'
    );
}
function cpm_wp_poll_allow_max_option_render(  ) {
    $options = get_option( 'cpm_wp_poll_settings' );
    if( empty($options['cpm_wp_poll_allow_max_option']) )
        $options['cpm_wp_poll_allow_max_option'] = 0;
    ?>

    <input type='text' name='cpm_wp_poll_settings[cpm_wp_poll_allow_max_option]' value='<?php echo $options['cpm_wp_poll_allow_max_option']; ?>'>
    <!-- <button name="cpm" -->
    <?php
}
function cpm_wp_poll_settings_section_callback() {
}

function cpm_wp_poll_options_page() {
    ?>
    <div class="wrap">
        <div class="cpm-bckend" id="code-backend">
            <div class="cpm-bckend-header clear">
                <a class="cpm-bckend-logo fl" href="http://codepixelz.market" target="_blank" title="Code Pixelz Media">
                    <img src="<?php echo CPM_POLL_PLUGIN_URL . '/media/images/code-cube.png' ?>" alt="Code Pixelz Cube"/>
                </a>
                <div class="cpm-bckend-btn-save fl">
                    <h3 class="cpm-backend-title"><?php _e('CPM Poll Free', '_cpmpoll');?></h3>
                </div>
            </div>
            <!-- End Header -->
            <div class="cpm-bckend-body">
                <div class="cpm-bckend-main-content fl nosidebar">
                    <form action='options.php' method='post'>
                        <?php
                            settings_fields( 'cpmPollPluginPage' );
                            do_settings_sections( 'cpmPollPluginPage' );
                        ?>
                        <?php
                            submit_button();
                        ?>
                    </form>

                    <!-- <hr /> -->
                </div>


            </div>
            <!-- End Body -->
            <div class="cpm-bckend-footer">
                <p><?php _e('By', '_cpmpoll');?> <a href="http://codepixelz.market/" target="_blank">Code Pixelz Media</a>.</p>
            </div>
            <!-- End the footer -->
        </div>
        <!-- End cpm backend -->

        <div class="pro-features">
                <h3><?php _e('Why go Pro ?', '_cpmpoll');?></h3>
                <ol>
                    <li> <?php _e('Seven Different chart types.', '_cpmpoll');?></li>
                        <ul>
                           <img src="<?php echo CPM_POLL_PLUGIN_URL . '/media/images/chart.png' ?>" alt="charts">
                        </ul>
                    <li> <img src="<?php echo CPM_POLL_PLUGIN_URL . '/media/images/dropper.png' ?>" alt="dropper"> <?php _e('Color Picker for individual Poll Options.', '_cpmpoll');?></li>
                    <li> <?php _e('Mulitple Charts on single page.', '_cpmpoll');?></li>
                    <li> <?php _e('Poll Widget Support', '_cpmpoll');?></li>
                    <li> <img src="<?php echo CPM_POLL_PLUGIN_URL . '/media/images/wpml.png' ?>" alt="WPML"><?php _e('WPML Translateable including Poll Options.', '_cpmpoll');?></li>
                    <li> <?php _e('Multiple Choice option.', '_cpmpoll');?></li>
                    <li> <?php _e('Show result only option for individual poll.', '_cpmpoll');?></li>
                    <li> <?php _e('Poll Expiry Date.', '_cpmpoll');?></li>
                </ol>
                <a href="http://codepixelz.market/" class="btn pro-btn" target="_blank"><?php _e('Get Pro version', '_cpmpoll');?></a>
        </div><!--Pro features ends-->

    </div>
    <!-- End the wrap -->
    <?php
}

/**
 * Ajax body for poll vote
 */

add_action( 'wp_ajax_cpm_poll_vote_action', 'cpm_poll_vote_action_callback' );
add_action( 'wp_ajax_nopriv_cpm_poll_vote_action', 'cpm_poll_vote_action_callback' );

function cpm_poll_vote_action_callback() {
    $cpm_wp_poll_id = $_POST['cpm_poll_id'];
    $poll_option_voted = $_POST['poll_option_voted'];
    $user_ip_address = cpm_wp_poll_get_current_user_ip();
    $user_unique_name = str_replace('.', '_', $user_ip_address);
    $current_voting_result = get_post_meta( $cpm_wp_poll_id, 'cpm_wp_poll_votes', true );
    $poll_chart_type = get_post_meta( $cpm_wp_poll_id, 'cpm_wp_poll_chart_type', true );
    $user_who_have_voted = get_option('cpm_wp_poll_voted_user_'.$user_unique_name);
    if( class_exists('SitePress') ) {
        $wpml_activated = true;
        $all_translated_posts = cpm_wp_poll_get_traslated_languages( $cpm_wp_poll_id );
        $original_voting_results = cpm_wp_poll_get_independent_poll_meta( $all_translated_posts );
        $new_original_voting_results_with_id = array_combine($all_translated_posts, $original_voting_results);//gives array with key as post_id and its poll meta
    } else {
        $wpml_activated = false;
        $all_translated_posts = array( $cpm_wp_poll_id );
        $original_voting_results = array();
        foreach ($all_translated_posts as $all_translated_post) {
            $indenpendent_voting_results = get_post_meta( $all_translated_post, 'cpm_wp_poll_votes', true );
            array_push($original_voting_results, $indenpendent_voting_results);
        }
        $new_original_voting_results_with_id = array_combine($all_translated_posts, $original_voting_results);
    } //WPML False

    if( !$user_who_have_voted || is_array($user_who_have_voted) ) {
        /*** For Radio Button ***/
        if( !is_array($poll_option_voted) ) {
                cpm_wp_poll_vote_radio( $new_original_voting_results_with_id, $poll_option_voted );
        } else {  /*** For Radio Button End ***/
            /** For Checkbox **/
               cpm_poll_vote_checkbox( $new_original_voting_results_with_id, $poll_option_voted );
            /** For Checkbox end **/
        }
        if( !$user_who_have_voted ) {
            add_option('cpm_wp_poll_voted_user_'.$user_unique_name, array($cpm_wp_poll_id), false);
        } else {
            array_push($user_who_have_voted, $cpm_wp_poll_id);
            update_option('cpm_wp_poll_voted_user_'.$user_unique_name, $user_who_have_voted);
        }
    } elseif( is_array($user_who_have_voted) && in_array($cpm_wp_poll_id, $user_who_have_voted) ) {
        echo 'Already Voted';
    } //if not user_who_have_voted

    $new_update_poll_array = get_post_meta( $cpm_wp_poll_id, 'cpm_wp_poll_votes', true );
    $new_poll_data_for_chart = array();

    foreach ($new_update_poll_array as $options => $votes) :
        //building json
        $new_poll_data_array = array( 'label' => $votes['poll_question'], 'value' => $votes['total_votes'], 'color' => cpm_wp_poll_rgb_code_generator( $options ) );
        array_push($new_poll_data_for_chart, $new_poll_data_array);
    endforeach;
    if( $poll_chart_type == 'pie' ) {
        $new_poll_data_for_chart_json = json_encode($new_poll_data_for_chart);
        echo $new_poll_data_for_chart_json;
    } elseif( $poll_chart_type == 'progress_bar' ) {
        $grand_total_votes = 0;
        foreach ($new_update_poll_array as $options => $votes) {
            $grand_total_votes += $votes['total_votes'];
        }
        foreach ($new_update_poll_array as $options => $votes) :
            $vote_percentage = ($votes['total_votes']/$grand_total_votes) * 100;
        ?>
            <label class="progress-label"><?php echo $votes['poll_question'];?></label>
            <div class="progress">
              <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo round($vote_percentage, 2);?>%; background-color:<?php echo cpm_wp_poll_rgb_code_generator( $options );?>">
                <span class="sr-only"><?php echo round($vote_percentage, 2).'%';?></span>
              </div>
            </div>
        <?php endforeach;
    }
    die;
}

/**
 * Hide View poll link
 */

function cpm_wp_poll_posttype_admin_css() {
    global $post_type;
    $post_types = array(
                        /* set post types */
                        'cpm_wp_poll',
                  );
    if(in_array($post_type, $post_types))
    echo '<style type="text/css">#post-preview,  #edit-slug-box{display: none;}</style>';
}
add_action( 'admin_head-post-new.php', 'cpm_wp_poll_posttype_admin_css' );
add_action( 'admin_head-post.php', 'cpm_wp_poll_posttype_admin_css' );

/**
 * Hide permalink field
 */

add_filter( 'get_sample_permalink_html', 'cpm_wp_poll_sample_permalink' );
function cpm_wp_poll_sample_permalink( $return ) {
    global $post_type;
    if( $post_type == 'cpm_wp_poll' ) {
        $return = '';
    }
    return $return;
}

/**
 * Hide Preview and view button on save and save draft
 */

add_filter( 'page_row_actions', 'cpm_wp_poll_row_actions', 10, 2 );
function cpm_wp_poll_row_actions( $actions, $post ) {
    if( get_post_type() === 'cpm_wp_poll' ) {
        unset( $actions['inline hide-if-no-js'] );
        unset( $actions['view'] );
    }
    return $actions;
}
/**
 * Change Place Holder Text for New Poll
 */
function cpm_wp_poll_title_placeholder( $title ){
     $screen = get_current_screen();
     if  ( 'cpm_wp_poll' == $screen->post_type ) {
          $title = __('Enter Poll Question', '_cpmpoll');
     }
     return $title;
}
add_filter( 'enter_title_here', 'cpm_wp_poll_title_placeholder' );
/**
 * Remove View Poll Node From Title Bar
 */
add_action( 'admin_bar_menu', 'cpm_wp_poll_remove_view_poll_node', 999 );
function cpm_wp_poll_remove_view_poll_node( $wp_admin_bar ) {
    global $post_type;
    if( $post_type == 'cpm_wp_poll' ) {
        $wp_admin_bar->remove_node( 'view' );
    }
}

/**
 * Custom Message
 */

function cpm_wp_poll_set_messages($messages) {
global $post, $post_ID;
$post_type = get_post_type( $post_ID );

$obj = get_post_type_object($post_type);
$singular = $obj->labels->singular_name;
if( $post_type === 'cpm_wp_poll') :
    $messages[$post_type] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __($singular.' updated ') ),
    4 => __($singular.' updated.'),
    5 => isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __($singular.' published.' ) ),
    7 => __('Page saved.'),
    8 => sprintf( __($singular.' submitted.'  ) ),
    9 => sprintf( __($singular.' scheduled for: <strong>%1$s</strong>.Preview'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
    10 => sprintf( __($singular.' draft updated.' ) ),
    );
endif;
    return $messages;
}

add_filter('post_updated_messages', 'cpm_wp_poll_set_messages' );


/**
 * Shortcode column in manage post screen
 */
add_filter('manage_cpm_wp_poll_posts_columns', 'cpm_wp_poll_column_head', 10);
function cpm_wp_poll_column_head($defaults) {
    $defaults['cpm_wp_poll_shortcode'] = __('Shortcode', '_cpmpoll');
    return $defaults;
}

add_action('manage_cpm_wp_poll_posts_custom_column', 'cpm_wp_poll_columns_content', 10, 2);
function cpm_wp_poll_columns_content($column_name, $post_ID) {
    if ( $column_name == 'cpm_wp_poll_shortcode' && 'publish' === get_post_status( $post_ID ) ) {
        $generated_shortcode = '[cpm_wp_poll poll_id="' . $post_ID . '"]';
        if ($generated_shortcode) {
            echo $generated_shortcode;
        }
    }
}