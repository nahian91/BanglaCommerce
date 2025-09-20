<?php
if (!defined('ABSPATH')) exit;

/**
 * ===============================
 * District, Upazilla & Thana Settings Tab
 * ===============================
 */
function bcaw_district_tab() {
    // Default districts
    $districts = [
        ['district'=>'Dhaka','upazillas'=>[
            ['name'=>'Dhaka Sadar','thanas'=>['Thana 1','Thana 2']],
            ['name'=>'Narayanganj Sadar','thanas'=>['Thana A','Thana B']],
        ],'postcode'=>'1000-2399'],
        ['district'=>'Mymensingh','upazillas'=>[
            ['name'=>'Jamalpur Sadar','thanas'=>['Thana 1']],
        ],'postcode'=>'2000-2499'],
        ['district'=>'Sylhet','upazillas'=>[
            ['name'=>'Sylhet Sadar','thanas'=>['Thana 1']],
        ],'postcode'=>'3000-3399'],
        ['district'=>'Chattogram','upazillas'=>[
            ['name'=>'Chattogram Sadar','thanas'=>['Thana 1']],
        ],'postcode'=>'4000-4799']
    ];

    $saved = get_option('bes_district_settings', ['enabled'=>1,'json'=>'']);
    $saved_json = !empty($saved['json']) ? json_decode($saved['json'], true) : $districts;
    $enabled = isset($saved['enabled']) ? (int)$saved['enabled'] : 1;
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('District, Upazilla & Thana Settings', 'bes-textdomain'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('bes_save_district_settings','bes_district_nonce'); ?>
            <p>
                <label>
                    <strong><?php esc_html_e('Enable District Feature:', 'bes-textdomain'); ?></strong>
                    <input type="checkbox" name="bes_enabled" value="1" <?php checked($enabled,1); ?>>
                </label>
            </p>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('District', 'bes-textdomain'); ?></th>
                        <th><?php esc_html_e('Upazilla', 'bes-textdomain'); ?></th>
                        <th><?php esc_html_e('Thana', 'bes-textdomain'); ?></th>
                        <th><?php esc_html_e('Postcode', 'bes-textdomain'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($saved_json as $d_index => $d_row): ?>
                        <?php foreach($d_row['upazillas'] as $u_index => $upazilla): ?>
                            <?php foreach($upazilla['thanas'] as $t_index => $thana): ?>
                                <tr>
                                    <?php if($u_index==0 && $t_index==0): ?>
                                        <td rowspan="<?php echo esc_attr(count($d_row['upazillas']) * max(1,count($upazilla['thanas']))); ?>">
                                            <input type="text" name="districts[<?php echo esc_attr($d_index); ?>][district]" value="<?php echo esc_attr($d_row['district']); ?>" style="width:100%;">
                                        </td>
                                    <?php endif; ?>

                                    <td>
                                        <input type="text" name="districts[<?php echo esc_attr($d_index); ?>][upazillas][<?php echo esc_attr($u_index); ?>][name]" value="<?php echo esc_attr($upazilla['name']); ?>" style="width:100%;">
                                    </td>

                                    <td>
                                        <input type="text" name="districts[<?php echo esc_attr($d_index); ?>][upazillas][<?php echo esc_attr($u_index); ?>][thanas][]" value="<?php echo esc_attr($thana); ?>" style="width:100%;">
                                    </td>

                                    <?php if($u_index==0 && $t_index==0): ?>
                                        <td rowspan="<?php echo esc_attr(count($d_row['upazillas']) * max(1,count($upazilla['thanas']))); ?>">
                                            <input type="text" name="districts[<?php echo esc_attr($d_index); ?>][postcode]" value="<?php echo esc_attr($d_row['postcode']); ?>" style="width:100%;">
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <br>
            <input type="submit" name="bes_save_districts" value="<?php esc_attr_e('Save Settings', 'bes-textdomain'); ?>" class="button button-primary">
        </form>
    </div>
    <?php
}

/**
 * ===============================
 * Save District Settings
 * ===============================
 */
add_action('admin_init', function(){
    if(isset($_POST['bes_save_districts']) && check_admin_referer('bes_save_district_settings','bes_district_nonce')){
        $enabled = isset($_POST['bes_enabled']) ? 1 : 0;
        $districts = $_POST['districts'] ?? [];

        $save_arr = [];
        foreach($districts as $d){
            $upazillas = [];
            if(isset($d['upazillas'])){
                foreach($d['upazillas'] as $u){
                    $upazillas[] = [
                        'name'=>sanitize_text_field($u['name']),
                        'thanas'=>array_map('sanitize_text_field',$u['thanas'] ?? [])
                    ];
                }
            }

            $save_arr[] = [
                'district'=>sanitize_text_field($d['district']),
                'upazillas'=>$upazillas,
                'postcode'=>sanitize_text_field($d['postcode'])
            ];
        }

        update_option('bes_district_settings',['enabled'=>$enabled,'json'=>wp_json_encode($save_arr)]);
    }
});

/**
 * ===============================
 * WooCommerce Checkout Fields (District + Upazilla + Thana inline)
 * ===============================
 */
add_filter('woocommerce_checkout_fields', function($fields){
    $saved = get_option('bes_district_settings', ['enabled'=>0,'json'=>'']);
    $enabled = isset($saved['enabled']) ? (int)$saved['enabled'] : 0;
    if(!$enabled) return $fields;

    $districts = json_decode($saved['json'], true);
    $district_options = [''=>esc_html__('Select District','bes-textdomain')];
    $DIST = [];

    if($districts){
        foreach($districts as $d){
            $district_options[esc_attr($d['district'])] = esc_html($d['district']);
            $DIST[$d['district']] = [
                'upazillas'=>array_map('esc_html', array_column($d['upazillas'],'name')),
                'thanas'=>array_map(function($t){ return array_map('esc_html',$t); }, array_column($d['upazillas'],'thanas')),
                'postcode'=>esc_attr($d['postcode'])
            ];
        }
    }

    $fields['billing']['billing_district'] = [
        'type'=>'select',
        'label'=>esc_html__('District','bes-textdomain'),
        'required'=>true,
        'class'=>['form-row-first'],
        'options'=>$district_options
    ];
    $fields['billing']['billing_upazilla'] = [
        'type'=>'select',
        'label'=>esc_html__('Upazilla','bes-textdomain'),
        'required'=>true,
        'class'=>['form-row-last'],
        'options'=>[''=>esc_html__('Select Upazilla','bes-textdomain')]
    ];
    $fields['billing']['billing_thana'] = [
        'type'=>'select',
        'label'=>esc_html__('Thana','bes-textdomain'),
        'required'=>true,
        'class'=>['form-row-last'],
        'options'=>[''=>esc_html__('Select Thana','bes-textdomain')]
    ];
    $fields['billing']['billing_postcode'] = [
        'type'=>'text',
        'label'=>esc_html__('Postcode','bes-textdomain'),
        'required'=>true,
        'class'=>['form-row-wide']
    ];

    return $fields;
});

/**
 * ===============================
 * Frontend JS: Auto-fill Upazilla, Thana & Postcode
 * ===============================
 */
add_action('wp_footer', function(){
    $saved = get_option('bes_district_settings', ['enabled'=>0,'json'=>'']);
    $enabled = isset($saved['enabled']) ? (int)$saved['enabled'] : 0;
    if(!$enabled) return;

    $districts = json_decode($saved['json'], true);
    if(!$districts) return;

    $DIST = [];
    foreach($districts as $d){
        $DIST[esc_js($d['district'])] = [
            'upazillas'=>array_map('esc_js', array_column($d['upazillas'],'name')),
            'thanas'=>array_map(function($t){ return array_map('esc_js',$t); }, array_column($d['upazillas'],'thanas')),
            'postcode'=>esc_js($d['postcode'])
        ];
    }
    ?>
    <script>
    jQuery(document).ready(function($){
        const DISTRICTS = <?php echo wp_json_encode($DIST, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

        function populateUpazilla(district){
            const $up = $('#billing_upazilla').empty().append('<option value=""><?php echo esc_js('Select Upazilla'); ?></option>');
            const $th = $('#billing_thana').empty().append('<option value=""><?php echo esc_js('Select Thana'); ?></option>');

            if(DISTRICTS[district]){
                DISTRICTS[district].upazillas.forEach((u,i)=>{
                    $up.append(`<option value="${u}">${u}</option>`);
                });
                $('#billing_postcode').val(DISTRICTS[district].postcode);
            } else {
                $('#billing_postcode').val('');
            }
        }

        function populateThana(upazilla){
            const district = $('#billing_district').val();
            const $th = $('#billing_thana').empty().append('<option value=""><?php echo esc_js('Select Thana'); ?></option>');
            if(DISTRICTS[district]){
                const idx = DISTRICTS[district].upazillas.indexOf(upazilla);
                if(idx >= 0){
                    DISTRICTS[district].thanas[idx].forEach(t=>{
                        $th.append(`<option value="${t}">${t}</option>`);
                    });
                }
            }
        }

        populateUpazilla($('#billing_district').val());
        populateThana($('#billing_upazilla').val());

        $(document).on('change','#billing_district',function(){ populateUpazilla($(this).val()); });
        $(document).on('change','#billing_upazilla',function(){ populateThana($(this).val()); });
        $('body').on('updated_checkout',function(){
            populateUpazilla($('#billing_district').val());
            populateThana($('#billing_upazilla').val());
        });
    });
    </script>
    <?php
});
