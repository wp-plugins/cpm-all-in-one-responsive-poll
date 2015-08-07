<?php
/**
 * Frontend function for shortcode and widget body
 * Returns either form or progress bar
 */

if( !function_exists('cpm_poll_the_poll_progress_bar') ) :
    function cpm_poll_the_poll_progress_bar( $poll_id, $from ) {
        $already_voted = cpm_wp_poll_user_already_voted($poll_id);
        $poll_options_array = get_post_meta($poll_id, 'cpm_wp_poll_votes', true);
        $poll_option_allow_multiple = get_post_meta($poll_id, 'cpm_poll_multiple_value', true);
        if( $poll_option_allow_multiple <= 0 ) :
            echo '<script> var checkBox = false; </script>';
        else:
            echo '<script> var checkBox = true; </script>';
        endif;
        $poll_data_for_chart = array();
        echo cpm_wp_poll_question_generator($poll_id, $poll_options_array, $already_voted, $poll_option_allow_multiple, $from);
        ?>

        <!-- Result -->
            <div class="cpm-poll-shortcode-container voting-result" id="voting-result-<?php echo $poll_id.'-'.$from;?>" <?php if( $already_voted == false ) echo 'style="display:none"'; else echo 'style="display:block"';?>>
                <div class="cpm-poll-question"><?php echo get_the_title($poll_id); ?></div>
                <div id="canvas-holder-<?php echo $poll_id.'-'.$from;?>" style="padding-top:20px; width:568px;" class="canvas-holder" >
                    <?php
                        $grand_total_votes = 0;
                        foreach ($poll_options_array as $options => $votes) {
                            $grand_total_votes += $votes['total_votes'];
                        }
                        if( $grand_total_votes <= 0 ) {
                            $grand_total_votes++;
                        }
                        foreach ($poll_options_array as $options => $votes) :
                            $vote_percentage = ($votes['total_votes']/$grand_total_votes) * 100;
                        ?>
                              <label class="progress-label"><?php echo $votes['poll_question'];?></label>
                            <div class="progress">
                              <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo round($vote_percentage, 2);?>%; background-color:<?php echo cpm_wp_poll_rgb_code_generator($options);?>">
                                <span class="sr-only"><?php echo round($vote_percentage, 2).'%';?></span>
                              </div>
                            </div>
                        <?php endforeach; ?>
                </div>
            </div>
        <script type="text/javascript">
            <?php if( $poll_option_allow_multiple > 0 ) : ?>
                var max = <?php echo (int)$poll_option_allow_multiple; ?>;
                var checkboxes = jQuery('#cpm-input-addon-<?php echo $poll_id.'-'.$from;?> input[type="checkbox"]');
                checkboxes.change(function() {
                    var current = checkboxes.filter(':checked').length;
                    checkboxes.filter(':not(:checked)').prop('disabled', current >= max);
                });
            <?php endif; ?>
        </script>
    <?php }
endif;
/**
 * Frontend function to show shortcode and widget body
 * Returns either form or Pie chart
 */
if( !function_exists('cpm_poll_the_poll_pie') ) :
    function cpm_poll_the_poll_pie( $poll_id, $from ) {
        $already_voted = cpm_wp_poll_user_already_voted($poll_id);
        $poll_options_array = get_post_meta($poll_id, 'cpm_wp_poll_votes', true);
        $poll_option_allow_multiple = get_post_meta($poll_id, 'cpm_poll_multiple_value', true);
        $poll_data_for_chart = array();
        echo cpm_wp_poll_question_generator($poll_id, $poll_options_array, $already_voted, $poll_option_allow_multiple, $from);
        ?>
        <!-- Result -->
            <?php
                foreach ($poll_options_array as $options => $votes) :
                    //building json
                    $poll_data_array = array( 'label' => $votes['poll_question'], 'value' => $votes['total_votes'], 'color' => cpm_wp_poll_rgb_code_generator($options) );
                    array_push($poll_data_for_chart, $poll_data_array);
                endforeach;
                $poll_data_for_chart_json = json_encode($poll_data_for_chart);
            ?>
            <div class="cpm-poll-shortcode-container voting-result" id="voting-result-<?php echo $poll_id.'-'.$from;?>" <?php if( $already_voted == false ) echo 'style="display:none"'; else echo 'style="display:block"';?>>
                <div class="cpm-poll-question"><?php echo get_the_title($poll_id); ?></div>
                <div id="canvas-holder-<?php echo $poll_id.'-'.$from;?>" style="padding-top:20px; width:568px;" class="canvas-holder">
                  <canvas id="chart-area-<?php echo $poll_id.'-'.$from;?>" ></canvas>
                </div>
                <div id="legend-<?php echo $poll_id.'-'.$from;?>" class="common-legend"></div>
            </div>
            <script  src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>
            <script type="text/javascript">
                <?php if( $poll_option_allow_multiple > 0 ) : ?>
                    var max = <?php echo (int)$poll_option_allow_multiple; ?>;
                    var checkboxes = jQuery('#cpm-input-addon-<?php echo $poll_id.'-'.$from;?> input[type="checkbox"]');
                    checkboxes.change(function(){
                        var current = checkboxes.filter(':checked').length;
                        checkboxes.filter(':not(:checked)').prop('disabled', current >= max);
                    });
                <?php endif; ?>
                //Building Chart
                var pieData = <?php echo $poll_data_for_chart_json; ?>;
                jQuery('#chart-area-<?php echo $poll_id.'-'.$from;?>').ready(function(){
                    var ctx = document.getElementById("chart-area-<?php echo $poll_id.'-'.$from;?>").getContext("2d");
                    var chartOption = {
                                    responsive : true,
                                    animation : true,
                                    animationEasing: "easeOutQuart",
                                    showScale: true,
                                    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
                                    tooltipTemplate: "<%=label%>: <%= numeral(circumference / 6.283).format('(0[.][00]%)') %>"
                                    }
                    window.cpmPie = new Chart(ctx).Pie(pieData, chartOption);
                    var legend = cpmPie.generateLegend();
                    jQuery("#legend-<?php echo $poll_id.'-'.$from;?>").html(legend);
                });
            </script>
<?php  }//end of function body
endif; //end of function exists

