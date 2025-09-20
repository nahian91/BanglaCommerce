jQuery(document).ready(function($){
    // Live Preview for Message Template
    $('textarea[name="bes_whatsapp_settings[message]"]').on('input', function(){
        var val = $(this).val();
        $('.bes-whatsapp-preview strong').text(val);
    });
});
