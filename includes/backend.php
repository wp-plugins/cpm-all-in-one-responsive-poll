<?php
/**
 * Register Post Type Poll
 */

function cpm_wp_poll_register_post_type() {
 	$labels = array(
		'name'               => __('Polls','_cpmpoll'),
	    'singular_name'      => __('Poll','_cpmpoll'),
	    'add_new'            => __('Add New','_cpmpoll'),
	    'add_new_item'       => __('Add New Poll','_cpmpoll'),
	    'edit_item'          => __('Edit Poll','_cpmpoll'),
	    'new_item'           => __('New Poll','_cpmpoll'),
	    'all_items'          => __('All Poll','_cpmpoll'),
	    'view_item'          => __('View Poll','_cpmpoll'),
	    'search_items'       => __('Search Poll','_cpmpoll'),
	    'not_found'          => __('No Poll found','_cpmpoll'),
	    'not_found_in_trash' => __('No Poll found in Trash','_cpmpoll'),
	    'parent_item_colon'  => '',
	    'menu_name'          => __('Polls','_cpmpoll')
		);
  	$args = array(
	    	'labels'             => $labels,
			'description'   => __('Holds all the Poll','_cpmpoll'),
			'public'        => true,
			'supports'      => array( 'title' ),
			'has_archive'   => true,
			'hierarchical'	=> true
			// 'menu_icon' => get_bloginfo('template_url').'/images/icons/interview-icon.png',
  	);
  	register_post_type( 'cpm_wp_poll', $args  );
}
add_action('init','cpm_wp_poll_register_post_type');

/**
 * Widget Init
 */
function cpm_wp_poll_register_widget() {
	register_widget( 'cpm_wp_poll_widget');
}
add_action( 'widgets_init', 'cpm_wp_poll_register_widget' );


class CPM_wp_poll_Widget extends WP_Widget {
	function cpm_wp_poll_widget() {
		// Instantiate the parent object
		parent::__construct(
	            'cpm_wp_poll_widget', // Base ID
        	    __('Simple WP Poll Widget', '_cpmpoll'), // Name
 	           array( 'description' => __( 'Simple WP Poll Widget', '_cpmpoll' ), ) // Args
		);
	} //init widget

	function widget( $args, $instance ) {
		// Widget Body
		if( !empty($instance) ) :
		 	$poll_id = $instance['cpm_poll_id'];
		 	$selected_chart_type = get_post_meta( $poll_id, 'cpm_wp_poll_chart_type', true );
		 	$poll_status = get_post_status( $poll_id );
			if( $poll_status === 'publish' ) :
				if( $selected_chart_type == 'pie' ) :
			  	  cpm_poll_the_poll_pie($poll_id, 'shortcode'); //function to display either poll result or poll questions
			    elseif( $selected_chart_type == 'progress_bar' ):
			      cpm_poll_the_poll_progress_bar($poll_id, 'shortcode');
			    endif;
			endif; //poll_status
		endif; //Empty Instance
	} //widget

	function update($new_instance, $old_instance) {
        $instance['cpm_poll_id'] = (int) $new_instance['cpm_poll_id'] ;
		return $instance;
	} //update

	// Widget form creation
	function form($instance) {
		if( $instance) {
			$cpm_poll_id = $instance['cpm_poll_id'];
		} else {
			$instance['cpm_poll_id'] = 0;
		}
	?>

		<p>
			<label for="<?php echo $this->get_field_id('cpm_poll_id'); ?>"><?php _e('Poll Title', '_cpmpoll');?>:</label>
			<select name="<?php echo $this->get_field_name('cpm_poll_id'); ?>" id="<?php echo $this->get_field_name('cpm_poll_id'); ?>" class="widefat">
			<option selected="selected" value="0"><?php _e('Select Option', '_cpmpoll');?></option>
				<?php
					global $post;
					$cpm_poll_args = array( 'post_type' => 'cpm_wp_poll', 'posts_per_page' => -1, 'post_status' => 'publish', 'suppress_filters' => 1 );
					$cpm_poll_query = new WP_Query($cpm_poll_args);
					while( $cpm_poll_query->have_posts() ) : $cpm_poll_query->the_post(); ?>
						<option <?php selected( $instance['cpm_poll_id'], $post->ID ); ?> value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
					<?php endwhile; wp_reset_query(); ?>
			</select>
		</p>
	<?php } //Form
} //class


/**
 * Adding Meta Boxes Poll Options
 */

add_action('add_meta_boxes', 'cpm_wp_poll_add_votes');
function cpm_wp_poll_add_votes() {
    // Vote boxes
    add_meta_box(
        'cpm-poll-wp-poll-add-votes',
        __('Response Options','_cpmpoll'),
        'cpm_wp_poll_options',
        'cpm_wp_poll',
        'normal'
    );
}

function cpm_wp_poll_options() { ?>
<?php
	global $post;
	$cp_poll_options_meta = get_post_meta($post->ID, 'cpm_wp_poll_votes', true);
	$cp_poll_allow_multiple = get_post_meta( $post->ID, 'cpm_poll_multiple_value', true);
	if( !$cp_poll_allow_multiple ) {
		$cp_poll_allow_multiple = 0;
	}
if( $cp_poll_options_meta ) :
	foreach ($cp_poll_options_meta as $options => $votes) : ?>
		<div class="cpm-option-vote-container" >
			<input type="hidden" class="cpm-poll-option-key" name="poll_vote_option_key[]" value="<?php echo $votes['vote_option_key'];?>" data-attr="">
			<input type="text" class="cpm-poll-option" placeholder="<?php _e('Poll Option', '_cpmpoll');?>" style="width:50%" name="poll_vote_options[]" value="<?php echo $votes['poll_question'];?>">
			<input type="hidden" class="cpm-poll-option-votes" placeholder="<?php _e('Votes', '_cpmpoll');?>"  name="poll_vote_option_votes[]" value="<?php echo $votes['total_votes']; ?>" >
			<span class="poll-result" style="display:none"><?php echo $votes['total_votes'];?></span>
			<button type="button" class="button cpm-option-vote-delete" style="cursor:pointer;"><span class="dashicons dashicons-no"></span>&nbsp;&nbsp;<?php _e('Remove Option', '_cpmpoll');?></span>
		</div>
<?php endforeach;
	else: ?>
	<div class="cpm-option-vote-container" >
		<input type="hidden" class="cpm-poll-option-key" name="poll_vote_option_key[]" value="1" >
		<input type="text" class="cpm-poll-option" placeholder="<?php _e('Poll Option', '_cpmpoll');?>" style="width:50%" name="poll_vote_options[]" value="">
		<input type="hidden" class="cpm-poll-option-votes" placeholder="<?php _e('Votes', '_cpmpoll');?>" style="width=30%" name="poll_vote_option_votes[]" value="0">
		<span class="poll-result" style="display:none">0</span>
		<button type="button" class="button cpm-option-vote-delete" style="cursor:pointer;"><span class="dashicons dashicons-no"></span>&nbsp;&nbsp;<?php _e('Remove Option', '_cpmpoll');?></span>
	</div>
<?php endif; ?>
	<div class="cpm-option-add" >
		<button id="add-poll-option" class="button" type="button" value="<?php _e('Add Option', '_cpmpoll');?>"><span class="dashicons dashicons-plus"></span>&nbsp;&nbsp;<?php _e('Add Option', '_cpmpoll');?></button>
	</div>
	<hr />
	<div class="poll-option-allow-multiple" style="position:relative;">
		<?php _e('Allow Multiple Votes', '_cpmpoll'); ?>: <input type="checkbox" id="cpm-poll-allow-multiple" <?php if( $cp_poll_allow_multiple> 0 ) echo 'checked="checked"'; ?> disabled>
		<div style="position:absolute; left:0; right:0; top:0; bottom:0;" id="multiple-pointer-trigger"></div>
		<input id="cpm-poll-allow-multiple-value" name="cpm_poll_multiple_value" size="2" value="<?php echo $cp_poll_allow_multiple;?>" <?php if( $cp_poll_allow_multiple <= 0 ) echo  'style="display:none"';?>>
	</div>
	<?php
		$maximum_allowed_add_option = get_option('cpm_wp_poll_settings');
	?>
	<script type="text/javascript">
		var maximumAllowedAddOption = "<?php echo $maximum_allowed_add_option['cpm_wp_poll_allow_max_option'];?>";
		var rows = jQuery('.cpm-option-vote-container').length+1;
		jQuery('#add-poll-option').on( 'click', function() {
			var rows = jQuery('.cpm-option-vote-container').length+1;
			if( rows >= maximumAllowedAddOption ){
				jQuery(this).attr('disabled','disabled');
			}
			var uniquePollOptionId = jQuery('.cpm-option-vote-container input.cpm-poll-option-key:last').attr('value');
			var newUniquePollId = parseInt(uniquePollOptionId)+1;
			var newClonedDiv = jQuery('div.cpm-option-vote-container').eq(0).clone();
			newClonedDiv.children('input.cpm-poll-option-votes').attr('value', 0);
			newClonedDiv.children('input.cpm-poll-option').attr('value', '');
			newClonedDiv.children('input.cpm-poll-color-selector').attr('value', '#fff').css({'background':'#fff', 'color' : 'black'});
			newClonedDiv.children('input.cpm-poll-option-key').attr( 'value', newUniquePollId);
			newClonedDiv.insertBefore(jQuery('.cpm-option-add').last());
		});
		jQuery('.cpm-option-vote-delete').live('click', function() {
			var parentsIndex = jQuery(this).parent().siblings('div.cpm-option-vote-container').length;
			if( parentsIndex != 0 ) {
				jQuery(this).parent().remove();
				if( rows <= maximumAllowedAddOption ) {
					jQuery('#add-poll-option').removeAttr('disabled');
				}
			}
		});
		jQuery('#cpm-poll-allow-multiple').on( 'click', function() {
			if( jQuery(this).is(':checked') ) {
				console.log('checked');
				jQuery('#cpm-poll-allow-multiple-value').show();
			} else {
				jQuery('#cpm-poll-allow-multiple-value').hide();
				jQuery('#cpm-poll-allow-multiple-value').attr('value', '0');
			}
		});
	</script>
<?php }

/**
 *  Save the Metabox Data if WPML not active
 */
add_action('save_post', 'cpm_wp_poll_save', 1, 2); // save the custom fields
function cpm_wp_poll_save($post_id, $post) {
	// Is the user allowed to edit the post or page?
	$post_type = get_post_type($post);
	if( $post_type === 'cpm_wp_poll' ) :
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;
		if( isset( $_POST['cpm_poll_multiple_value']) )
			update_post_meta( $post_id, 'cpm_poll_multiple_value', $_POST['cpm_poll_multiple_value'] );
		// OK, we're authenticated: we need to find and save the data
		if( !isset( $_POST['poll_vote_option_key'] ) ) : // To delete key if all the rows are removed.
			update_post_meta( $post_id, 'cpm_wp_poll_votes', '' );
		endif;
		// We'll put it into an array to make it easier to loop though.
		if( !empty( $_POST['poll_vote_option_key'] ) && !empty( $_POST['poll_vote_options'] ) && !empty( $_POST['poll_vote_option_votes'] ) ) :
			$poll_vote_options = $_POST['poll_vote_options'];
			$poll_vote_option_votes  = $_POST['poll_vote_option_votes'];
			$poll_vote_color_option = $_POST['poll_vote_color_option'];
			$poll_vote_option_key = $_POST['poll_vote_option_key'];
			$new_poll_array = array();
			// Preparing Array For Database
			foreach ($poll_vote_option_key as $id => $key) {
			    $new_poll_array[$key] = array(
			    	'poll_question' => $poll_vote_options[$id],
			        'total_votes'  => $poll_vote_option_votes[$id],
			        'vote_option_color' => $poll_vote_color_option[$id],
			        'vote_option_key' => $key
			    );
			}
			// To delete if a row is removed.
			foreach($new_poll_array as $key => $value) {
			    if(is_null($key) || $key == '')
			        unset($new_poll_array[$key]);
			}
			update_post_meta( $post_id, 'cpm_wp_poll_votes', $new_poll_array );
			if( !empty( $_POST['cpm_poll_multiple_value'] ) || !empty( $_POST['chart_type']) ) {
				$all_translated_posts = cpm_wp_poll_get_traslated_languages($post_id);
				foreach ($all_translated_posts as $translated_posts) {
					update_post_meta($translated_posts, 'cpm_poll_multiple_value', $_POST['cpm_poll_multiple_value']);
				}
				if( isset( $_POST['chart_type']) ) {
					foreach ($all_translated_posts as $translated_posts) {
						update_post_meta($translated_posts, 'cpm_wp_poll_chart_type', $_POST['chart_type']);
					}
				}//chart type
			} //multiple and chart type
		endif; //isset End
	endif;
}

/**
 * Showing Meta Box For Poll Shortcode
 */

add_action('add_meta_boxes', 'cp_wp_poll_shortcode_display_box');
function cp_wp_poll_shortcode_display_box() {
    add_meta_box(
        'cpm-wp-poll-shortcode',
        __('Shortcode Generator','_cpmpoll'),
        'cpm_wp_poll_shortcode_display',
        'cpm_wp_poll',
        'side'
    );
    add_meta_box(
    	'cpm_wp_poll_chart_type',
    	__('Chart Type:', '_cpmpoll'),
    	'cpm_wp_poll_chart_type_callback',
    	'cpm_wp_poll',
    	'side'
    );
    add_meta_box(
    	'cpm_wp_poll_chart_voting_overview',
    	__('Voting Overview', '_cpmpoll'),
    	'cpm_wp_poll_voting_overview_callback',
    	'cpm_wp_poll',
    	'side'
    );
}

function cpm_wp_poll_shortcode_display() {
	global $post;
	$shortcode_html = '<div id="misc-publishing-actions"><label>'.__('Use as shortcode', '_cpmpoll').'</label><span id="shortcopy">[cpm_wp_poll poll_id="'.$post->ID.'"]</span></div>';
	$shortcode_html .= "<div id='major-publishing-actions'><div id='delete-action'>
		<a href='javascript:void(0);' class='button button-primary button-large' id='short-copy'>".__('Copy Shortcode', '_cpmpoll')."</a></div></div>";
	echo $shortcode_html;
}

function cpm_wp_poll_chart_type_callback() {
	global $post;
	$selected_chart_type = get_post_meta($post->ID, 'cpm_wp_poll_chart_type', true);
	$select_html = '<select name="chart_type" id="cpm-wp-chart-type" style="width:100%">';
	$select_html .= '<option value="progress_bar"'. selected( $selected_chart_type, "progress_bar", false ). '>'.__('Progress Bar', '_cpmpoll').'</option>';
	$select_html .= '<option value="pie"'. selected( $selected_chart_type, "pie", false ). '>'.__('Pie', '_cpmpoll').'</option>';
	$select_html .= '<option value="polar"'. selected( $selected_chart_type, "polar", false ). ' disabled>'.__('Polar', '_cpmpoll').'</option>';
	$select_html .= '<option value="doughnut"'. selected( $selected_chart_type, "doughnut", false ). ' disabled>'.__('Doughnut', '_cpmpoll').'</option>';
	$select_html .= '<option value="bar"'. selected( $selected_chart_type, "bar", false ). ' disabled>'.__('Bar', '_cpmpoll').'</option>';
	$select_html .= '<option value="line"'. selected( $selected_chart_type, "line", false ). ' disabled>'.__('Line', '_cpmpoll').'</option>';
	$select_html .= '<option value="radar"'. selected( $selected_chart_type, "radar", false ). ' disabled>'.__('Radar', '_cpmpoll').'</option>';
	$select_html .= '</select>';
	echo $select_html;
}

function cpm_wp_poll_voting_overview_callback() {
	global $post;
	$cp_poll_options_meta = get_post_meta($post->ID, 'cpm_wp_poll_votes', true);
	$sum_total_votes = 0;
	if( !empty($cp_poll_options_meta)  ) :
		$grand_total_votes = 0;
		foreach ($cp_poll_options_meta as $options => $votes) {
            $grand_total_votes += $votes['total_votes'];
		}
		if( $grand_total_votes > 0 ) :
			foreach ($cp_poll_options_meta as $options => $votes) :
				$vote_percentage = ($votes['total_votes']/$grand_total_votes) * 100;
				$voting_overview_html = '<div class="voting-option">'.$votes['poll_question'].'</div><div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:'.round($vote_percentage, 2).'%; background-color:'.cpm_wp_poll_rgb_code_generator($options).'">'.$votes['total_votes'].'</div></div>';
				echo $voting_overview_html;
				$sum_total_votes += $votes['total_votes'];
			endforeach;
			echo '<div class="total-votes"><span>'.__('Total Votes', '_cpmpoll').':</span>'.$sum_total_votes.'</div>';
		endif;
	endif;
	if( empty($cp_poll_options_meta) || $grand_total_votes <= 0 )
		echo '<div class="total-votes">'.__('No Votes has been casted yet.', '_cpmpoll').'</div>';
}

