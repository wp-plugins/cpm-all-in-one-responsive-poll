jQuery(window).load(function(){
    var totalCanvas = jQuery('.voting-result').length;
    if( totalCanvas > 1 ) {
        jQuery('.voting-result').replaceWith('<div class="cpm-poll-shortcode-container voting-result">'+proMessage.message+'</div>');
        jQuery('.voting-result').not(jQuery('.voting-result').eq(0)).remove();
    }
})