<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Control_Picker extends \Elementor\Base_Data_Control {

    public function get_type() { return 'msdl_picker'; }

    public function enqueue() {
        wp_enqueue_script( 'msdl-picker-js', plugins_url( '../../assets/js/msdl-picker.js', __FILE__ ), ['jquery'], '1.2', true );
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
        <div class="msdl-picker-control-wrapper" style="margin-bottom: 20px;">
            <div class="elementor-control-field">
                <label class="elementor-control-title">{{{ data.label }}}</label>
                <div class="elementor-control-input-wrapper" style="width:100%;">
                    <input type="hidden" class="msdl-picker-hidden-input" data-setting="{{ data.name }}" />
                    <button type="button" class="elementor-button elementor-button-default msdl-open-picker-btn" data-item-type="{{ data.item_type }}" style="width:100%; background:#2271b1; color:#fff; border-radius:4px; font-weight:500;">
                        <i class="fas fa-folder-open" style="margin-right:5px;"></i> Tallózás a SharePointban...
                    </button>
                </div>
            </div>

            <div class="msdl-selected-card" style="display:none; width:100%; margin-top:12px; background:#fff; padding:15px; border-radius:6px; border:1px solid #dcdcde; box-shadow: 0 2px 6px rgba(0,0,0,0.04); box-sizing:border-box;">
                
                <div style="display:flex; align-items:flex-start; gap:10px; margin-bottom:12px;">
                    <div style="background:#f0f6fc; padding:8px; border-radius:6px; color:#2271b1; flex-shrink:0;">
                        <i class="fas fa-file-alt" style="font-size:16px;"></i>
                    </div>
                    <strong class="msdl-sc-name" style="color:#1d2327; display:block; font-size:13px; word-break:break-all; line-height:1.4; padding-top:2px;"></strong>
                </div>
                
                <div style="font-size:12px; color:#50575e; line-height:1.8; background:#f6f7f7; padding:10px; border-radius:4px;">
                    <div style="display:flex; justify-content:space-between; border-bottom:1px solid #e2e4e7; padding-bottom:4px; margin-bottom:4px;">
                        <span><i class="fas fa-weight-hanging" style="width:14px; opacity:0.6;"></i> Méret:</span> 
                        <strong class="msdl-sc-size" style="color:#1d2327;"></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; border-bottom:1px solid #e2e4e7; padding-bottom:4px; margin-bottom:4px;">
                        <span><i class="fas fa-eye" style="width:14px; opacity:0.6;"></i> Jogosultság:</span> 
                        <strong class="msdl-sc-roles" style="color:#1d2327;"></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span><i class="far fa-calendar-alt" style="width:14px; opacity:0.6;"></i> Módosítva:</span> 
                        <strong class="msdl-sc-date" style="color:#1d2327;"></strong>
                    </div>
                </div>
                
                <div style="text-align:right; margin-top:12px; padding-top:10px;">
                    <a href="#" class="msdl-clear-btn" style="color:#d63638; text-decoration:none; font-size:12px; font-weight:500; transition:opacity 0.2s;">
                        <i class="fas fa-times-circle" style="margin-right:4px;"></i> Kijelölés eltávolítása
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
}