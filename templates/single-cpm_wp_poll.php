<?php get_header();
while( have_posts() ) : the_post();
	$poll_id = $post->ID;
	echo '<script> var cpmPollId = '. $poll_id . '</script>';
	$poll_options_array = get_post_meta($poll_id, 'cpm_wp_poll_votes', true);
	$poll_option_allow_multiple = get_post_meta($poll_id, 'cpm_poll_multiple_value', true);
	if( $poll_option_allow_multiple <= 0 ) :
		echo '<script> var checkBox = false; </script>';
	else:
		echo '<script> var checkBox = true; </script>';
	endif;
	$poll_data_for_chart = array();
	$user_ip_address = cpm_wp_poll_get_current_user_ip();
	$user_unique_name = str_replace('.', '_', $user_ip_address);
	$already_voted = false;
	$user_who_have_voted = get_option('cpm_wp_poll_voted_user_'.$user_unique_name);
	if( empty($user_who_have_voted) ) {
	  $already_voted = false;
	} else {
	  if( in_array($poll_id, $user_who_have_voted) ) {
	    $already_voted = true;
	  } else {
	    $already_voted = false;
	  }
	}
	?>
	<div class="cpm-poll-shortcode-container " id="voting-options" <?php if( $already_voted == true ) echo 'style="display:none"'; else echo 'style="display:block"';?> >
		<div class="cpm-poll-question"><?php echo get_the_title($poll_id); ?></div>
	<?php if( $poll_options_array ) : $first_option = true;
		foreach ($poll_options_array as $options => $votes) :
		?>
			<div class="cpm-input-group">
			   <div class="cpm-input-group-addon">
				   	<label><?php echo $votes['poll_question']; ?></label>
				   	<?php if( $poll_option_allow_multiple <= 0 ) : ?>
					    <input type="radio" name="cpm_poll_vote_up" poll-key="<?php echo $options;?>" <?php if( $first_option ) echo 'checked="checked"'; ?>>
					<?php else: ?>
					    <input type="checkbox" name="cpm_poll_vote_up" poll-key="<?php echo $options;?>" <?php if( $first_option ) echo 'checked="checked"'; ?>>
					<?php endif; ?>
			   </div>
			</div><!-- /input-group -->
	<?php
		$first_option = false;
		endforeach;  endif;
	?>
		<a href="javascript:void(0);" class="cpm-wp-poll-vote-button"><?php _e('Vote', '_cpm');?></a>
	</div>
	<!-- Result -->
		<?php
			foreach ($poll_options_array as $options => $votes) :
				//building json
				$poll_data_array = array( 'label' => $votes['poll_question'], 'value' => $votes['total_votes'], 'color' => $votes['vote_option_color'] );
				array_push($poll_data_for_chart, $poll_data_array);
			endforeach;
			$poll_data_for_chart_json = json_encode($poll_data_for_chart);
	  	?>
		<div class="cpm-poll-shortcode-container" id="voting-result" <?php if( $already_voted == false ) echo 'style="display:none"'; else echo 'style="display:block"';?>>
			<div id="canvas-holder" style="padding-top:20px;">
			  <canvas id="chart-area" ></canvas>
			</div>
			<div id="legend"></div>
		</div>
		<script  src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>
		<script type="text/javascript">
			<?php if( $poll_option_allow_multiple > 0 ) : ?>
				var max = <?php echo (int)$poll_option_allow_multiple; ?>;
				var checkboxes = jQuery('.cpm-input-group-addon input[type="checkbox"]');
				checkboxes.change(function(){
				    var current = checkboxes.filter(':checked').length;
				    checkboxes.filter(':not(:checked)').prop('disabled', current >= max);
				});
			<?php endif; ?>
			//Building Chart
		  	var doughnutData = <?php echo $poll_data_for_chart_json; ?>;
			window.onload = function(){
				var ctx = document.getElementById("chart-area").getContext("2d");
				var chartOption = {
									responsive : true,
									animation : true,
									animationEasing: "easeOutQuart",
									showScale: true,
									legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
				  					tooltipTemplate: "<%=label%>: <%= numeral(circumference / 6.283).format('(0[.][00]%)') %>"
								}
				window.cpmDoughnut = new Chart(ctx).Doughnut(doughnutData, chartOption);
				var legend = cpmDoughnut.generateLegend();
				jQuery("#legend").html(legend);
			};
		</script>
<?php endwhile; ?>
<?php get_footer(); ?>