document.addEventListener('DOMContentLoaded', () => {
    // -----------------------
    // Tabs
    // -----------------------
    document.querySelectorAll('.bpsm-tabs-container').forEach(container => {
        const tabs = container.querySelectorAll('.bpsm-tab');
        const contents = container.querySelectorAll('.bpsm-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active from all tabs
                tabs.forEach(t => t.classList.remove('bpsm-tab-active'));
                tab.classList.add('bpsm-tab-active');

                // Hide all contents
                contents.forEach(c => c.style.display = 'none');

                // Show target content
                const targetId = tab.getAttribute('data-target');
                const targetContent = container.querySelector(`#${targetId}`);
                if (targetContent) targetContent.style.display = 'block';
            });
        });
    });

    // -----------------------
    // Collapsible Cards
    // -----------------------
    document.querySelectorAll('.bpsm-collapse-header').forEach(header => {
        header.addEventListener('click', () => {
            const body = header.nextElementSibling;
            if (!body) return;
            body.style.display = body.style.display === 'block' ? 'none' : 'block';
        });
    });
});

// -----------------------
// jQuery Section
// -----------------------
jQuery(document).ready(function($){
    // -----------------------
    // DataTable
    // -----------------------
    const table = $('#media-check-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        order: [[1,'asc']]
    });

    // -----------------------
    // Filter by media type
    // -----------------------
    $('#media-type-filter').on('change', function(){
        const val = $(this).val();
        table.rows().every(function(){
            const row = this.node();
            if(!val || $(row).hasClass(val)) {
                $(row).show();
            } else {
                $(row).hide();
            }
        });
    });

    // -----------------------
    // Image preview modal
    // -----------------------
    $('.media-preview').on('click', function(){
        const src = $(this).data('src');
        if(src) {
            $('#modal-image').attr('src', src);
            $('#media-preview-modal').fadeIn();
        }
    });

    $('#modal-close, #media-preview-modal').on('click', function(e){
        if(e.target.id === 'modal-close' || e.target.id === 'media-preview-modal'){
            $('#media-preview-modal').fadeOut();
        }
    });

    // -----------------------
    // AJAX save on blur
    // -----------------------
    $('.media-edit').on('blur', function(){
        const row = $(this).closest('tr');
        const id = parseInt(row.data('id'), 10);
        const field = $(this).attr('name');
        const value = $(this).val();

        if(!id || !field) return;

        $.ajax({
            url: bcawSettings.ajax_url,
            type: 'POST',
            data: {
                action: 'bcaw_update_media',
                id: id,
                field: field,
                value: value,
                _wpnonce: bcawSettings.nonce
            },
            success: function(resp){
                if(resp.success){
                    alert(bcawSettings.success);
                }
            },
            error: function(){
                alert(bcawSettings.error);
            }
        });
    });
});


jQuery(document).ready(function($){

    // Initialize WP Color Picker
    $('.bcaw-color-picker').wpColorPicker();

    const preview = $('#bcaw-live-preview');
    const messagePreview = $('#bcaw-live-message');

    function updatePreview() {
        const number = $('[name="bes_whatsapp_settings[number]"]').val().replace(/\D/g,'');
        const text = $('[name="bes_whatsapp_settings[button_text]"]').val() || 'Order via WhatsApp';
        const color = $('[name="bes_whatsapp_settings[color]"]').val() || '#25D366';
        const message = $('[name="bes_whatsapp_settings[message]"]').val() || '';
        const messageWithSample = message.replace('{product}', 'Sample Product');

        preview.text(text).css('background', color);
        preview.attr('href', `https://wa.me/${number}?text=${encodeURIComponent(messageWithSample)}`);
        messagePreview.text(messageWithSample);
    }

    $('.bcaw-live').on('input change', updatePreview);
    updatePreview();

    $('#bcaw-copy-link').on('click', function() {
        navigator.clipboard.writeText(preview.attr('href')).then(() => {
            alert('WhatsApp link copied to clipboard!');
        });
    });
});


