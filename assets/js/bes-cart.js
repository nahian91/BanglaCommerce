jQuery(document).ready(function($){
    // Live preview of checkout button
    $('#bes_checkout_button_text').on('input', function(){
        $('#preview_checkout_button').text($(this).val());
    });
});
