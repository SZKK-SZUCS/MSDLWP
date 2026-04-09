<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Control_Picker extends \Elementor\Base_Data_Control {

    public function get_type() { return 'msdl_picker'; }

    public function enqueue() {
        wp_enqueue_script( 'msdl-picker-js', plugins_url( '../../assets/js/msdl-picker.js', __FILE__ ), ['jquery'], '1.1', true );
        wp_localize_script( 'msdl-picker-js', 'msdlPickerData', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'msdl_picker_nonce' )
        ]);
    }

    protected function get_default_settings() {
        return [ 'item_type' => 'file' ];
    }

    public function content_template() {
        ?>
        <div class="msdl-picker-control-wrapper" style="margin-bottom: 15px;">
            <div class="elementor-control-field">
                <label class="elementor-control-title">{{{ data.label }}}</label>
                <div class="elementor-control-input-wrapper" style="width:100%;">
                    <input type="hidden" class="msdl-picker-hidden-input" data-setting="{{ data.name }}" />
                    <button type="button" class="elementor-button elementor-button-default msdl-open-picker-btn" data-item-type="{{ data.item_type }}" style="width:100%;">
                        <i class="fa fa-folder-open"></i> Tallózás...
                    </button>
                </div>
            </div>

            <div class="msdl-selected-card" style="display:none; width:100%; margin-top:10px; background:#f0f6fc; padding:12px; border-radius:4px; border:1px solid #8c8f94; box-sizing:border-box;">
                <strong class="msdl-sc-name" style="color:#2271b1; display:block; margin-bottom:8px; font-size:13px; word-break:break-all; line-height:1.2;"></strong>
                
                <div style="font-size:11px; color:#50575e; line-height:1.6;">
                    <div style="display:flex; justify-content:space-between;"><span>Méret:</span> <strong class="msdl-sc-size"></strong></div>
                    <div style="display:flex; justify-content:space-between;"><span>Láthatóság:</span> <strong class="msdl-sc-roles"></strong></div>
                    <div style="display:flex; justify-content:space-between;"><span>Módosítva:</span> <strong class="msdl-sc-date"></strong></div>
                </div>
                
                <div style="text-align:right; margin-top:10px; border-top:1px solid #dcdcde; padding-top:8px;">
                    <a href="#" class="msdl-clear-btn" style="color:#d63638; text-decoration:none; font-size:11px;"><i class="fa fa-trash"></i> Kijelölés törlése</a>
                </div>
            </div>
        </div>
        <?php
    }
}