jQuery(document).ready(function($){
    $('#bes-fields-container').sortable({ placeholder: "ui-state-highlight" });

    // Logo upload
    var file_frame;
    $('#bes_upload_logo').on('click', function(e){
        e.preventDefault();
        if(file_frame){ file_frame.open(); return; }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload Logo',
            button: { text: 'Use this logo' },
            multiple: false
        });
        file_frame.on('select', function(){
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#bes_package_logo').val(attachment.url);
            $('#bes_logo_preview').attr('src', attachment.url);
        });
        file_frame.open();
    });

    // Color Picker
    $('.bes-color-picker').wpColorPicker();

    // Live Preview
    $('#bes-fields-container input, #bes-fields-container textarea, #bes-fields-container select').on('input change', function(){
        var logo = $('#bes_package_logo').val();
        $('#bes-preview').find('img').attr('src', logo);
        $('#bes-preview h2').text($('input[name="bes_package_settings[company_name]"]').val());
        $('#bes-preview p').first().html($('textarea[name="bes_package_settings[address]"]').val().replace(/\n/g,'<br>'));
        $('#bes-preview p').eq(1).text('Phone: '+$('input[name="bes_package_settings[phone]"]').val()+' | Email: '+$('input[name="bes_package_settings[email]"]').val());
        $('#bes-preview hr').next('p').text($('textarea[name="bes_package_settings[footer_text]"]').val());
    });
});
