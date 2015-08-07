<?php
function cpm_wp_poll_enqueue_scripts() {
	$deps = array( 'jquery', 'jquery-ui-core' );
	if( !wp_script_is( 'cpm_chart', 'registered' ) ) {
		wp_enqueue_script( 'cpm_chart', CPM_POLL_PLUGIN_URL . '/media/js/Chart.min.js', $deps, '', true );
	}
	if( !wp_script_is( 'cpm_wp_poll_init', 'registered' ) ) {
		wp_enqueue_script( 'cpm_wp_poll_init', CPM_POLL_PLUGIN_URL . '/media/js/cpm-poll-init.js', $deps, '1.0.0', true );
	}
	wp_register_style( 'cpm_poll_wp_default_css', CPM_POLL_PLUGIN_URL . '/media/css/cpm-wp-default.css', false, '' );
    wp_enqueue_style( 'cpm_poll_wp_default_css' );
    wp_enqueue_script( 'cpm_poll_frontend_init', CPM_POLL_PLUGIN_URL . '/media/js/cpm-poll-frontend-init.js' , array('jquery'), '', false );
    wp_localize_script('cpm_poll_frontend_init', 'proMessage', array( 'message' => __('Multiple Polls are supported only on the Premium version. Buy it from <a href="">here</a>', '_cpmpoll') ) );

    wp_enqueue_script( 'cpm_poll_chart_maker', CPM_POLL_PLUGIN_URL . '/media/js/chart-maker.js' , array('jquery'), '', true );
    wp_enqueue_script( 'cpm_poll_ajax_script', CPM_POLL_PLUGIN_URL . '/media/js/cpm_wp_poll_ajax_call.js' , array('jquery'), '', true );

    // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script( 'cpm_poll_ajax_script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_register_style( 'cpm_poll_progress_bar', CPM_POLL_PLUGIN_URL . '/media/css/progressbar.css', false, '' );
    wp_enqueue_style( 'cpm_poll_progress_bar' );
}
add_action( 'wp_enqueue_scripts', 'cpm_wp_poll_enqueue_scripts' );

function cpm_poll_add_admin_stylesheet() {
    global $post_type;
    wp_register_style( 'cpm_poll_color_picker_style', CPM_POLL_PLUGIN_URL . '/media/css/colorpicker.css', false, '' );
    wp_enqueue_style( 'cpm_poll_color_picker_style' );
    wp_register_style( 'cpm_poll_color_picker_layout', CPM_POLL_PLUGIN_URL . '/media/css/layout.css', false, '' );
    wp_enqueue_style( 'cpm_poll_color_picker_layout' );
	if ( !wp_script_is('cpm_color_picker', 'registered') ) {
        wp_enqueue_script( 'cpm_color_picker', CPM_POLL_PLUGIN_URL . '/media/js/colorpicker.js', array('jquery'), '', false );
    }
    wp_enqueue_style( 'jquery-ui-datepicker' );
    wp_register_style( 'cpm_poll_wp_default_admin_css', CPM_POLL_PLUGIN_URL . '/media/css/cpm-poll-admin.css', false, '' );
    wp_enqueue_style( 'cpm_poll_wp_default_admin_css' );
    if( !wp_script_is('cpm_poll_frontend_init', 'registered') && $post_type === 'cpm_wp_poll' ) {
        wp_enqueue_script( 'cpm_poll_admin_init', CPM_POLL_PLUGIN_URL . '/media/js/cpm-poll-init.js' , array('jquery'), '', true );
        wp_localize_script('cpm_poll_admin_init', 'poll_post_type', array( 'post_type' => $post_type ) );
    }
    if ( !wp_script_is('cpm_jquery_validation', 'registered') ) {
		wp_enqueue_script( 'cpm_jquery_validation', CPM_POLL_PLUGIN_URL . '/media/js/jquery.validate.min.js', array('jquery'), '', false );
    }

    wp_register_style( 'cpm-admin-css-setting', CPM_POLL_PLUGIN_URL . '/media/css/cpm-admin.css', false, '' );
    wp_enqueue_style( 'cpm-admin-css-setting' );

}
add_action( 'admin_enqueue_scripts', 'cpm_poll_add_admin_stylesheet' );

add_action( 'admin_enqueue_scripts', 'cpm_wp_poll_pointer_load');
function cpm_wp_poll_pointer_load( $hook_suffix ) {
    // Helper function goes here
    // Don't run on WP < 3.3
    if ( get_bloginfo( 'version' ) < '3.3' )
        return;

    // Get the screen ID
    $screen = get_current_screen();
    $screen_id = $screen->id;

    // Get pointers for this screen
    $pointers = apply_filters( 'cpm_wp_poll_pointers-' . $screen_id, array() );

    // No pointers? Then we stop.
    if ( ! $pointers || ! is_array( $pointers ) )
        return;
    $valid_pointers = array();

    // Check pointers and remove dismissed ones.
    foreach ( $pointers as $pointer_id => $pointer ) {
        $pointer['pointer_id'] = $pointer_id;

        // Add the pointer to $valid_pointers array
        $valid_pointers['pointers'][] = $pointer;
    }

    // No valid pointers? Stop here.
    if ( empty( $valid_pointers ) )
        return;
     // Add pointers style to queue.
    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'cpm-poll-pointer', CPM_POLL_PLUGIN_URL . '/media/js/cpm-poll-pointer.js', array( 'wp-pointer' ) );
    wp_localize_script( 'cpm-poll-pointer', 'cpmpollPointer', $valid_pointers );

}

/**
 * For allow multiple tooltip
 */

add_filter( 'cpm_wp_poll_pointers-cpm_wp_poll', 'cpm_wp_poll_register_pointer_allow_multiple' );
function cpm_wp_poll_register_pointer_allow_multiple( $p ) {
    $p['xyz140'] = array(
        'target' => '#cpm-poll-allow-multiple',
        'options' => array(
            'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
                __( 'Go Pro' ,'_cpmpoll'),
                __( 'Switch to premium version to allow user to vote for multiple options.','_cpmpoll')
            ),
            'position' => array( 'edge' => 'left', 'align' => 'middle' )
        )
    );
    return $p;
}

/**
 * For Go Pro tooltip
 */

add_filter( 'cpm_wp_poll_pointers-cpm_wp_poll', 'cpm_wp_poll_register_pointer_chart_type' );
function cpm_wp_poll_register_pointer_chart_type( $p ) {
    $p['cpm_poll_chart_type_pointer'] = array(
        'target' => '#cpm-wp-chart-type',
        'options' => array(
            'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
                __( 'Go Pro' ,'_cpmpoll'),
                __( 'Switch to premium version to use other chart types.','_cpmpoll')
            ),
            'position' => array( 'edge' => 'right', 'align' => 'middle' )
        )
    );
    return $p;
}


