<?php
if (!defined('ABSPATH')) exit;

/**
 * Media Manager Admin Page
 */
function bcaw_media_check_tab() {
    $uploads = get_posts([
        'post_type'      => 'attachment',
        'posts_per_page' => 50,
        'post_status'    => 'inherit',
    ]);
    ?>

    <h2><?php echo esc_html__('Media Manager', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
    <p><?php echo esc_html__('Edit media information directly from this table (auto-save via AJAX).', 'banglacommerce-all-in-one-woocommerce'); ?></p>

    <!-- Filter by type -->
    <label for="media-type-filter"><?php echo esc_html__('Filter by Type:', 'banglacommerce-all-in-one-woocommerce'); ?></label>
    <select id="media-type-filter">
        <option value=""><?php echo esc_html__('All', 'banglacommerce-all-in-one-woocommerce'); ?></option>
        <option value="image"><?php echo esc_html__('Image', 'banglacommerce-all-in-one-woocommerce'); ?></option>
        <option value="video"><?php echo esc_html__('Video', 'banglacommerce-all-in-one-woocommerce'); ?></option>
    </select>

    <table id="media-check-table" class="widefat striped">
        <thead>
            <tr>
                <th><?php echo esc_html__('Preview', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('File Name', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Type', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Width', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Height', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Size (KB)', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Title', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Description', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                <th><?php echo esc_html__('Alt Text', 'banglacommerce-all-in-one-woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($uploads)) : ?>
                <?php foreach ($uploads as $upload) : 
                    $file_path = get_attached_file($upload->ID);
                    $url       = wp_get_attachment_url($upload->ID);
                    $filetype  = wp_check_filetype($url)['ext'] ?? '';
                    $filesize  = file_exists($file_path) ? filesize($file_path)/1024 : 0; // KB
                    $width     = $height = '-';
                    $type_class = '';

                    if (in_array(strtolower($filetype), ['jpg','jpeg','png','gif'])) {
                        $type_class = 'image';
                        if (file_exists($file_path)) {
                            $size = @getimagesize($file_path);
                            if ($size) {
                                $width = $size[0];
                                $height = $size[1];
                            }
                        }
                    } elseif (in_array(strtolower($filetype), ['mp4','mov','avi','webm'])) {
                        $type_class = 'video';
                    }

                    $title = get_the_title($upload->ID);
                    $desc  = get_post_field('post_content', $upload->ID);
                    $alt   = get_post_meta($upload->ID, '_wp_attachment_image_alt', true);
                ?>
                    <tr class="<?php echo esc_attr($type_class); ?>" data-id="<?php echo esc_attr($upload->ID); ?>">
                        <td>
                            <?php if ($type_class === 'image') : ?>
                                <img src="<?php echo esc_url($url); ?>" class="media-preview" data-src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>">
                            <?php elseif ($type_class === 'video') : ?>
                                <span class="media-video-icon">🎬</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($upload->post_title); ?></td>
                        <td><?php echo esc_html($filetype); ?></td>
                        <td><?php echo esc_html($width); ?></td>
                        <td><?php echo esc_html($height); ?></td>
                        <td><?php echo esc_html(round($filesize, 2)); ?></td>
                        <td><input type="text" class="media-edit" name="title" value="<?php echo esc_attr($title); ?>" placeholder="<?php esc_attr_e('Enter Title', 'banglacommerce-all-in-one-woocommerce'); ?>"></td>
                        <td><input type="text" class="media-edit" name="desc" value="<?php echo esc_attr($desc); ?>" placeholder="<?php esc_attr_e('Enter Description', 'banglacommerce-all-in-one-woocommerce'); ?>"></td>
                        <td><input type="text" class="media-edit" name="alt" value="<?php echo esc_attr($alt); ?>" placeholder="<?php esc_attr_e('Enter Alt Text', 'banglacommerce-all-in-one-woocommerce'); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="9"><?php echo esc_html__('No recent media found.', 'banglacommerce-all-in-one-woocommerce'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal for preview (JS handles showing/hiding) -->
    <div id="media-preview-modal">
        <span id="modal-close">&times;</span>
        <img id="modal-image" src="">
    </div>

<?php
}

// -------------------- AJAX Handler --------------------
add_action('wp_ajax_bcaw_update_media', function(){
    check_ajax_referer('bcaw_media_nonce');

    $id    = intval($_POST['id'] ?? 0);
    $field = sanitize_text_field($_POST['field'] ?? '');
    $value = sanitize_text_field($_POST['value'] ?? '');

    if (!$id || !$field) {
        wp_send_json_error(__('Invalid data', 'banglacommerce-all-in-one-woocommerce'));
    }

    if ($field === 'title') {
        wp_update_post(['ID' => $id, 'post_title' => $value]);
    } elseif ($field === 'desc') {
        wp_update_post(['ID' => $id, 'post_content' => $value]);
    } elseif ($field === 'alt') {
        update_post_meta($id, '_wp_attachment_image_alt', $value);
    }

    wp_send_json_success(__('Updated!', 'banglacommerce-all-in-one-woocommerce'));
});
