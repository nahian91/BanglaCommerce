jQuery(document).ready(function($){
    var rowIndex = $('#bes-district-repeater .bes-district-row').length;

    $('#add-district-row').click(function(e){
        e.preventDefault();
        var html = '<div class="bes-district-row">' +
                   '<input type="text" name="bes_district_settings['+rowIndex+'][district]" placeholder="District">' +
                   '<input type="text" name="bes_district_settings['+rowIndex+'][upazillas]" placeholder="Upazillas (comma-separated)">' +
                   '<input type="text" name="bes_district_settings['+rowIndex+'][postcode]" placeholder="Postcode">' +
                   '<button class="button remove-row">Remove</button>' +
                   '</div>';
        $('#bes-district-repeater').append(html);
        rowIndex++;
    });

    $(document).on('click', '.remove-row', function(e){
        e.preventDefault();
        $(this).closest('.bes-district-row').remove();
    });
});
