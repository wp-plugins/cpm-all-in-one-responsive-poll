jQuery(document).ready( function($) {
    $(".poll-option-allow-multiple > div").click(function (evt) {
        cpm_poll_open_pointer(0);
        // console.log(cpmpollPointer);
        function cpm_poll_open_pointer(i) {
            pointer = cpmpollPointer.pointers[0];
            options = $.extend( pointer.options, {
                close: function() {
                    $.post( ajaxurl, {
                        pointer: pointer.pointer_id,
                        action: 'dismiss-wp-pointer'
                    });
                }
            });
            $(pointer.target).pointer( options ).pointer('open');
        }
    });
    $("#cpm-wp-chart-type").click(function(event) {
        /* Act on the event */
        cpm_poll_open_pointer(0);
        // console.log(cpmpollPointer);
        function cpm_poll_open_pointer(i) {
            pointer = cpmpollPointer.pointers[1];
            options = $.extend( pointer.options, {
                close: function() {
                    $.post( ajaxurl, {
                        pointer: pointer.pointer_id,
                        action: 'dismiss-wp-pointer'
                    });
                }
            });
            $(pointer.target).pointer( options ).pointer('open');
        }
    });
});