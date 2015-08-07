jQuery('a.cpm-wp-poll-vote-button').on('click', function() {
    var voteFrom = jQuery(this).data('from');
    var cpmPollId = jQuery(this).data('voteid');
    var checkboxValue = jQuery(this).data('allowmultiple');
    /* Act on the event */
    if( checkboxValue === false ) {
        var pollOptionValue = jQuery('#cpm-input-addon-'+cpmPollId+'-'+voteFrom+' input[type=radio]:checked').attr('poll-key');
    } else if(  checkboxValue === true  ) {
        var pollOptionValue = [];
        jQuery('#cpm-input-addon-'+cpmPollId+'-'+voteFrom+' input[type=checkbox]:checked').each(function() {
            pollOptionValue.push(jQuery(this).attr('poll-key'));
        });
    }

    if( typeof pollOptionValue === "undefined" || pollOptionValue.length <= 0 ) {
        jQuery('#alert-message-'+cpmPollId).show();
        return true;
    }
    var chartType = jQuery(this).attr('chart-type');
    var data = {
        'action': 'cpm_poll_vote_action',
        'poll_option_voted': pollOptionValue,
        'cpm_poll_id' : cpmPollId,
        'dataType' : 'json'
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    jQuery.post(ajax_object.ajax_url, data, function(response) {
        if( chartType == 'pie' || chartType == 'doughnut' || chartType == 'polar' || chartType == 'bar' || chartType == 'line' || chartType == 'radar') {
            var newData = jQuery.parseJSON(response);
        }
        jQuery('#voting-options-'+cpmPollId+'-'+voteFrom).fadeOut(function(){
            jQuery('#voting-result-'+cpmPollId+'-'+voteFrom).fadeIn(function(){
                if( chartType == 'doughnut' ) {
                    cpm_poll_ajax_doughnut_maker(newData, voteFrom, cpmPollId);
                } else if( chartType == 'pie' ) {
                    cpm_poll_ajax_pie_maker(newData, voteFrom, cpmPollId);
                } else if( chartType == 'progress-bar' ) {
                    jQuery('.progress').remove(function() {
                        jQuery('#canvas-holder-'+cpmPollId+'-'+voteFrom).append(response);
                    });
                } else if( chartType == 'polar' ) {
                    cpm_poll_ajax_polar_maker(newData, voteFrom, cpmPollId);
                } else if( chartType == 'bar' ) {
                    cpm_poll_ajax_bar_maker(newData, voteFrom, cpmPollId);
                } else if( chartType == 'line' ) {
                    cpm_poll_ajax_line_maker(newData, voteFrom, cpmPollId)
                } else if( chartType == 'radar' ) {
                    cpm_poll_ajax_radar_maker(newData, voteFrom, cpmPollId)
                }
            });
        });
    });
});

