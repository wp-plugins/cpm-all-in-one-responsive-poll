jQuery('#short-copy').on('click', function(){
    var text = jQuery('#shortcopy').text();
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
});

jQuery(document).ready(function($){
    var cpmPollType = poll_post_type.post_type;
    if( cpmPollType === 'cpm_wp_poll' ) {
        var form = $("form[name='post']");
        $(form).find("#publish").click(function(e){
            $(form).validate({
                rules: {
                    'poll_vote_options[]': "required",
                },
                // Specify the validation error messages
                messages: {
                    'poll_vote_options[]': "Please enter Poll Option.",
                },
                 errorPlacement: function(error, element) {
                    if (element.attr("name") == "poll_vote_options[]" ) {
                      error.insertBefore(".cpm-option-add");
                    } else {
                      error.insertAfter(element);
                    }
                  }
            });

            if($(form).valid())
            {
                $("#ajax-loading").show();
                return true;
                $(this).submit();
                $('#publish').removeClass('disabled');
            }else{
                $("#publish").removeClass('disabled');
                $("#ajax-loading").hide();
                $('.spinner').hide();
            }
        });
        jQuery('.cpm-poll-option.valid').live('blur',function(event) {
            $("#publish").removeClass('disabled');
        });
    } //post type
});
