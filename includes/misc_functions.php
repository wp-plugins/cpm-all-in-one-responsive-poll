<?php
/**
 * Get Ip function
 */

if( !function_exists('cpm_wp_poll_get_current_user_ip') ) :
    function cpm_wp_poll_get_current_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) :
            $cpm_wp_poll_user_ip = $_SERVER['HTTP_CLIENT_IP'];
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) :
            $cpm_wp_poll_user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else :
            $cpm_wp_poll_user_ip = $_SERVER['REMOTE_ADDR'];
        endif;
        return $cpm_wp_poll_user_ip;
    }
endif;

/**
 * Get Translated Poll ids when WPML exists
 */

if( !function_exists('cpm_wp_poll_get_traslated_languages') ) :
    function cpm_wp_poll_get_traslated_languages($cpm_wp_poll_id) {
        $all_translated_posts = array( $cpm_wp_poll_id );
        return $all_translated_posts;
    } //function end
endif; //function_exists

/**
 * Independent voting results from all translated ids
 * @ param
 * takes $all_translated_posts as array
 * returns poll meta for all translated posts
 */

if( !function_exists('cpm_wp_poll_get_independent_poll_meta') ) :
    function cpm_wp_poll_get_independent_poll_meta($all_translated_posts) {
        $original_voting_results = array();
        foreach ($all_translated_posts as $all_translated_post) {
            $indenpendent_voting_results = get_post_meta( $all_translated_post, 'cpm_wp_poll_votes', true );
            array_push($original_voting_results, $indenpendent_voting_results);
        }
        return $original_voting_results;
    } //end of function
endif; //end of function exists

/**
 * vote function for radio buttons
 * takes new combined array with post id key and its poll meta
 * returns null
 */

if( !function_exists('cpm_wp_poll_vote_radio') ) :
    function cpm_wp_poll_vote_radio($new_original_voting_results_with_id, $poll_option_voted) {
        foreach ($new_original_voting_results_with_id as $original_voting_result_post_id => $original_voting_result_value ) {
            //Gives voting result for each posts
            foreach ($original_voting_result_value as $original_voting_result_key => $original_voting_result_individual_value) {
                if( $original_voting_result_key == $poll_option_voted ) {
                    $old_voting_result_value = $original_voting_result_individual_value['total_votes'];
                    $new_voting_result_value = $old_voting_result_value + 1;
                    $original_voting_result_value[$original_voting_result_key]['total_votes'] = $new_voting_result_value;
                    update_post_meta( $original_voting_result_post_id, 'cpm_wp_poll_votes', $original_voting_result_value );
                }
            }
        }
    } //end of function
endif; //end of function exists




function cpm_wp_poll_question_generator($poll_id, $poll_options_array, $already_voted, $poll_option_allow_multiple, $from) {
    $chart_type = get_post_meta($poll_id,'cpm_wp_poll_chart_type', true);
    if( $chart_type == 'progress_bar' )  {
        $poll_chart_type = 'progress-bar';
    } elseif( $chart_type == 'pie' ) {
        $poll_chart_type = 'pie';
    }
        $checkbox_value = "false";
?>
     <div class="cpm-poll-shortcode-container voting-options" id="voting-options-<?php echo $poll_id.'-'.$from;?>" <?php if( $already_voted == true ) echo 'style="display:none"'; else echo 'style="display:block"';?> >
            <div class="cpm-poll-question"><?php echo get_the_title($poll_id); ?></div>
        <?php if( $poll_options_array ) : $first_option = true;
            foreach ($poll_options_array as $options => $votes) :
            ?>
                <div class="cpm-input-group">
                   <div class="cpm-input-group-addon" id="cpm-input-addon-<?php echo $poll_id.'-'.$from;?>">
                        <?php if( $poll_option_allow_multiple <= 0 ) : ?>
                            <input id="<?php echo strtolower(str_replace(' ', '-', $votes['poll_question']));?>" type="radio" name="cpm_poll_vote_up" poll-key="<?php echo $options;?>" >
                        <?php else: ?>
                            <input id="<?php echo strtolower(str_replace(' ', '-', $votes['poll_question']));?>" type="checkbox" name="cpm_poll_vote_up" poll-key="<?php echo $options;?>" >
                        <?php endif; ?>
                        <label  for="<?php echo strtolower(str_replace(' ', '-', $votes['poll_question']));?>"><?php echo $votes['poll_question']; ?></label>
                   </div>
                </div><!-- /input-group -->
        <?php
            $first_option = false;
            endforeach;  endif;
        ?>
            <a href="javascript:void(0);" class="cpm-wp-poll-vote-button" chart-type="<?php echo $poll_chart_type;?>" data-voteid="<?php echo $poll_id;?>" data-from="<?php echo $from;?>" data-allowmultiple="<?php echo $checkbox_value;?>"><?php _e('Vote', '_cpmpoll');?></a>
        </div>
    <div class="alert alert-danger" style="display:none;" id="alert-message-<?php echo $poll_id; ?>" ><?php _e('Please select atleast one option','_cpmpoll');?></div>
<?php
}

function cpm_wp_poll_user_already_voted($poll_id) {
    $user_ip_address = cpm_wp_poll_get_current_user_ip();
    $user_unique_name = str_replace('.', '_', $user_ip_address);
    $already_voted = false;
    $user_who_have_voted = get_option('cpm_wp_poll_voted_user_'.$user_unique_name);
    $show_result_only = get_post_meta($poll_id, 'cpm_wp_poll_show_result_only', true);
    $never_expire_value = get_post_meta($poll_id, 'cpm_wp_poll_never_expires', true);
    $expiry_date_value = get_post_meta($poll_id, 'cpm_poll_expires_on', true);
    if( $never_expire_value != 'never-expires' && !empty($expiry_date_value) ) {
        $current_date_value = current_time( 'Y-m-d' );
        $current_date_value_time = strtotime($current_date_value);
        $expiry_date_value_time = strtotime($expiry_date_value);
        if( $expiry_date_value_time < $current_date_value_time ) {
            $expired_poll_flag = true;
        } else {
            $expired_poll_flag = false;
        }
    } else {
        $expired_poll_flag = false;
    } //never_expires

    if( $show_result_only != 'show-result-only' ) {
        if( $expired_poll_flag != true ){
            if( empty($user_who_have_voted) ) {
              $already_voted = false;
            } else {
              if( in_array($poll_id, $user_who_have_voted) ) {
                $already_voted = true;
              } else {
                $already_voted = false;
              } //inarray
            } //empty_user_who_voted
        } else {
            $already_voted = true;
        }
    } else {
        $already_voted = true;
    } //show_result_only_end
    return $already_voted;
}


/**
 * Random rgbcode generator
 * Returns random rbgcode
 */

if( !function_exists('cpm_wp_poll_rgb_code_generator') ):
    function cpm_wp_poll_rgb_code_generator($id){
        return '#'.substr(md5($id), 0, 6);
    }
endif;