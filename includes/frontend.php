<?php
/**
 * Loading Templates
 */

add_action( 'template_redirect', 'cpm_wp_poll_redirect_post' );

function cpm_wp_poll_redirect_post() {
  $queried_post_type = get_query_var('post_type');
  if ( is_single() && 'cpm_wp_poll' ==  $queried_post_type ) {
    wp_redirect( home_url(), 301 );
    exit;
  }
}
/**
 * Displaying ShortCode
 */

function cpm_wp_poll_shortcode($atts) {
	extract(shortcode_atts(array(
      					'poll_id'   => '',
   					), $atts)
	);
  $poll_status = get_post_status( $poll_id );
  if( $poll_status === 'publish' ) :
    ob_start();
      $selected_chart_type = get_post_meta( $poll_id, 'cpm_wp_poll_chart_type', true );
      if( $selected_chart_type == 'pie' ) :
    	  cpm_poll_the_poll_pie($poll_id, 'shortcode'); //function to display either poll result or poll questions
      elseif( $selected_chart_type == 'progress_bar' ):
        cpm_poll_the_poll_progress_bar($poll_id, 'shortcode');
      endif;
      $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
  endif;
}
add_shortcode('cpm_wp_poll', 'cpm_wp_poll_shortcode');
