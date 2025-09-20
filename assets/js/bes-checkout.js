jQuery(document).ready(function($){
    // Expand/Collapse
    $('.bes-card-header').click(function(){
        $(this).next('.bes-card-body').slideToggle();
        $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
    });

    // Sortable Fields
    $('.bes-fields-group').sortable({ placeholder: "ui-state-highlight" });

    // Live Preview Titles
    $('.bes-title-input').on('input', function(){
        var preview_id = 'preview_'+$(this).data('preview');
        $('#'+preview_id).text($(this).val());
    });
});
