<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Widget_File_Manager extends \Elementor\Widget_Base {

    public function get_name() { return 'msdl_file_manager'; }
    public function get_title() { return 'MSDL Fájlkezelő (Pro)'; }
    public function get_icon() { return 'eicon-archive'; }
    public function get_categories() { return [ 'msdl-widgets' ]; }

    public function get_style_depends() { return [ 'elementor-icons-fa-solid' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_query', [ 'label' => 'Navigáció és Bázis', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'start_folder_id', [ 'label' => 'Bázis Mappa (Opcionális)', 'type' => 'msdl_picker', 'item_type' => 'folder' ]);
        $this->add_control( 'allow_navigate_up', [ 'label' => 'Kifelé navigálás', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '', 'separator' => 'before' ]);
        $this->add_control( 'allow_navigate_down', [ 'label' => 'Befelé navigálás', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_header_elements', [ 'label' => 'Fejléc Elemek', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_header', [ 'label' => 'Teljes Fejléc', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_breadcrumbs', [ 'label' => 'Útvonal', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_header' => 'yes' ] ] );
        $this->add_control( 'enable_search', [ 'label' => 'Keresősáv', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_header' => 'yes' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_table_elements', [ 'label' => 'Táblázat Elemek', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_table_header', [ 'label' => 'Fejléc Sor', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_table_icon', [ 'label' => 'Ikonok', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_1', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'show_size', [ 'label' => 'Méret', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_date', [ 'label' => 'Dátum', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_fv_elements', [ 'label' => 'Fájl Adatlap', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'fv_show_icon', [ 'label' => 'Nagy Ikon', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_title', [ 'label' => 'Fájlnév', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_desc', [ 'label' => 'Leírás', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_2', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'show_meta_version', [ 'label' => 'Verzió Mutatása (Adatlapon)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_ext', [ 'label' => 'Kiterjesztés Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_size', [ 'label' => 'Méret Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_date', [ 'label' => 'Dátum Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_3', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'fv_show_download', [ 'label' => 'Letöltés Gomb', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_copy', [ 'label' => 'Másolás Gomb', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_global', [ 'label' => 'Globális Stílus', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'bg_color', [ 'label' => 'Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-wrapper' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'wrapper_border', 'selector' => '{{WRAPPER}} .msdl-fm-wrapper' ] );
        $this->add_responsive_control( 'wrapper_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .msdl-fm-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_control( 'text_color', [ 'label' => 'Szövegszín', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-table td, {{WRAPPER}} .msdl-fm-item-name, {{WRAPPER}} .msdl-fm-fv-title' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_header', [ 'label' => 'Fejléc', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'header_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-header' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'bc_link_color', [ 'label' => 'Navigáció', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-breadcrumbs a' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_table', [ 'label' => 'Táblázat', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'th_color', [ 'label' => 'Fejléc Szöveg', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-table th' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'row_border', [ 'label' => 'Sor Elválasztó', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-table td' => 'border-bottom-color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_icons', [ 'label' => 'Ikonok', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'folder_icon_color', [ 'label' => 'Mappa Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-item-folder .msdl-fm-icon svg' => 'fill: {{VALUE}} !important;', '{{WRAPPER}} .msdl-fm-item-folder .msdl-fm-icon i' => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'file_icon_color', [ 'label' => 'Fájl Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-item-file .msdl-fm-icon svg' => 'fill: {{VALUE}};', '{{WRAPPER}} .msdl-fm-item-file .msdl-fm-icon i' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_section();
        
        $this->start_controls_section( 'section_version_style', [ 'label' => 'Verzió Jelzés', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'version_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'version_text_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'version_icon_color', [ 'label' => 'Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge i' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'version_typo', 'selector' => '{{WRAPPER}} .msdl-version-badge' ] );
        $this->add_responsive_control( 'version_padding', [ 'label' => 'Belső Margó', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'version_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'style_buttons', [ 'label' => 'Gombok', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'btn_bg', [ 'label' => 'Fő (Letöltés) Gomb Háttere', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn' => 'background-color: {{VALUE}} !important; border-color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'btn_color', [ 'label' => 'Fő (Letöltés) Szövege/Ikonja', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn' => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'outline_color', [ 'label' => 'Másodlagos (Részletek/Mappa) Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn-outline' => 'color: {{VALUE}}; border-color: {{VALUE}};' ] ] );
        $this->end_controls_section();
    }

    protected function render() {
        if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) wp_enqueue_style( 'elementor-icons-fa-solid' );

        $settings = $this->get_settings_for_display();
        $uid = 'msdl-fm-' . $this->get_id();
        $base_folder = isset($settings['start_folder_id']) && !empty($settings['start_folder_id']) ? $settings['start_folder_id'] : 'root';
        $nonce = wp_create_nonce( 'msdl_frontend_nonce' );
        $ajax_url = admin_url( 'admin-ajax.php' );

        $allow_up = $settings['allow_navigate_up'] === 'yes' ? 'true' : 'false';
        $allow_down = $settings['allow_navigate_down'] === 'yes' ? 'true' : 'false';
        $show_size = $settings['show_size'] === 'yes' ? 'true' : 'false';
        $show_date = $settings['show_date'] === 'yes' ? 'true' : 'false';
        $show_table_icon = $settings['show_table_icon'] === 'yes' ? 'true' : 'false';
        
        $fv_show_icon = $settings['fv_show_icon'] === 'yes' ? 'true' : 'false';
        $fv_show_title = $settings['fv_show_title'] === 'yes' ? 'true' : 'false';
        $fv_show_desc = $settings['fv_show_desc'] === 'yes' ? 'true' : 'false';
        $fv_show_ext = $settings['fv_show_ext'] === 'yes' ? 'true' : 'false';
        $fv_show_size = $settings['fv_show_size'] === 'yes' ? 'true' : 'false';
        $fv_show_date = $settings['fv_show_date'] === 'yes' ? 'true' : 'false';
        $fv_show_download = $settings['fv_show_download'] === 'yes' ? 'true' : 'false';
        $fv_show_copy = $settings['fv_show_copy'] === 'yes' ? 'true' : 'false';
        
        $show_version = $settings['show_meta_version'] === 'yes';

        ?>
        <style>
            /* JAVÍTÁS: Nincs fix szélesség. Teljesen reszponzív lett! */
            .msdl-fm-wrapper { font-family: inherit; border: 1px solid #eaeaea; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 30px rgba(0,0,0,0.05); background: #fff; color: #242943; width: 100%; box-sizing: border-box;}
            .msdl-fm-header { padding: 20px; border-bottom: 1px solid #eaeaea; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; background: #fcfcfc; }
            .msdl-fm-breadcrumbs { font-size: 15px; font-weight: 600; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
            .msdl-fm-breadcrumbs a { text-decoration: none; cursor: pointer; transition: all 0.2s; padding: 6px 12px; border-radius: 6px; background: rgba(0,0,0,0.04); color: #2271b1;}
            .msdl-fm-breadcrumbs a:hover { background: rgba(0,0,0,0.08); color: #135e96; }
            .msdl-fm-breadcrumbs span.msdl-fm-sep { color: #a0a6b5; margin: 0 4px; font-size: 13px;}
            .msdl-fm-breadcrumbs span.msdl-fm-current { font-weight: 700; padding: 6px 12px; }
            .msdl-fm-search { position: relative; width: 300px; max-width: 100%; margin-left: auto; flex-grow: 1; }
            .msdl-fm-search input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #dcdcde; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.2s; background: #fff; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);}
            .msdl-fm-search input:focus { outline: none; border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
            .msdl-fm-search i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #a0a6b5; font-size: 14px; }
            
            .msdl-fm-body { position: relative; width: 100%; overflow-x: hidden; min-height: 250px; padding: 10px;}
            .msdl-fm-loader { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.85); display: flex; flex-direction: column; gap: 15px; align-items: center; justify-content: center; font-weight: 600; font-size: 15px; z-index: 10; display: none; backdrop-filter: blur(2px); border-radius: 0 0 12px 12px;}
            .msdl-fm-spinner { border: 3px solid rgba(0,0,0,0.1); width: 40px; height: 40px; border-radius: 50%; border-left-color: #2271b1; animation: msdl-spin 0.8s linear infinite; }
            @keyframes msdl-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            
            /* JAVÍTÁS: Eltávolítva a min-width: 600px! */
            .msdl-fm-table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: left; font-size: 14px; margin: 0; padding: 0; table-layout: auto;}
            <?php if ( $settings['show_table_header'] !== 'yes' ) : ?>.msdl-fm-table thead { display: none; }<?php endif; ?>
            .msdl-fm-table th { padding: 15px; border-bottom: 1px solid #eaeaea; color: #787c82; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; white-space: nowrap; }
            .msdl-fm-table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f6f7f7; transition: background 0.2s;}
            .msdl-fm-table tr:last-child td { border-bottom: none; }
            .msdl-fm-table tr:hover td { background: #f8f9fa; }
            .msdl-fm-table tr:hover td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
            .msdl-fm-table tr:hover td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
            
            /* JAVÍTÁS: Flexibilis oszlopok */
            .msdl-col-name { width: auto; word-break: break-word; white-space: normal; }
            .msdl-col-meta { white-space: nowrap; width: auto; }
            .msdl-col-action { white-space: normal; width: auto; text-align: right; }
            
            .msdl-action-buttons { display: flex; justify-content: flex-end; gap: 8px; align-items: center; flex-wrap: wrap; }
            .msdl-fm-item-name { display: flex; align-items: center; gap: 12px; font-weight: 600; cursor: pointer; text-decoration: none !important; transition: all 0.2s; line-height: 1.4; color: #242943; word-break: break-word; white-space: normal;}
            .msdl-fm-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; flex-shrink: 0; }
            .msdl-fm-icon svg { width: 24px; height: 24px; }
            .msdl-fm-icon i { font-size: 24px; }
            
            .msdl-fm-btn { padding: 8px 14px; border-radius: 6px; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: 1px solid transparent; cursor: pointer; font-weight: 600; font-size: 13px; color: #fff !important; background-color: #2271b1 !important;}
            .msdl-fm-btn:hover { opacity: 0.9; transform: translateY(-1px); }
            
            .msdl-fm-btn-outline { padding: 8px 14px; background: transparent; border-radius: 6px; border: 1px solid #2271b1; color: #2271b1; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; font-family: inherit; font-weight: 600; font-size: 13px; text-decoration: none !important;}
            .msdl-fm-btn-outline:hover { background: rgba(34, 113, 177, 0.05); }
            
            .msdl-fm-file-view { display: none; padding: 30px; text-align: center; animation: msdl-fade-in 0.3s ease; }
            @keyframes msdl-fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .msdl-fm-fv-header { text-align: left; margin-bottom: 20px; display:flex; flex-wrap:wrap; gap:10px;}
            .msdl-fm-back-btn { background: none; border: none; font-size: 15px; font-weight: 700; color: #787c82; cursor: pointer; transition: color 0.2s; padding: 0; display: flex; align-items: center; gap: 8px; }
            .msdl-fm-back-btn:hover { color: #242943; }
            .msdl-fm-fv-card { max-width: 100%; margin: 0 auto; padding: 30px; background: #fff; border: 1px solid #f0f2f5; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
            .msdl-fm-fv-icon { width: 70px; height: 70px; border-radius: 50%; background: #2271b1; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(34, 113, 177, 0.2);}
            .msdl-fm-fv-icon svg { width: 35px; height: 35px; fill: #fff; }
            .msdl-fm-fv-icon i { font-size: 32px; color: #fff; }
            .msdl-fm-fv-title { font-size: 22px; font-weight: 700; margin: 0 0 15px 0; word-wrap: break-word; line-height: 1.3; color: #242943;}
            .msdl-fm-fv-desc { font-size: 14px; line-height: 1.6; margin-bottom: 25px; padding: 15px; background: rgba(0,0,0,0.02); border-radius: 8px; border-left: 3px solid #dcdcde; text-align: left; color: #50575e; word-wrap: break-word;}
            
            .msdl-fm-fv-meta { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; font-size: 13px; color: #787c82; font-weight: 600; align-items: center;}
            .msdl-fm-fv-meta span { display: inline-flex; align-items: center; }
            
            .msdl-version-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 6px; background: rgba(34,113,177,0.08); color: #2271b1; cursor: help; transition: all 0.2s; font-weight: 600; font-size: 12px;}
            .msdl-version-badge i { color: inherit; }
            .msdl-version-badge:hover { background: rgba(34,113,177,0.15); }
            
            .msdl-fm-fv-actions { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 10px; }
            .msdl-toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #242943; color: #fff; padding: 10px 20px; border-radius: 50px; font-size: 13px; font-weight: 600; opacity: 0; pointer-events: none; transition: all 0.3s ease; z-index: 9999; box-shadow: 0 5px 15px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 8px; }
            .msdl-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

            @media (max-width: 768px) {
                .msdl-fm-table th:nth-child(2), .msdl-fm-table td:nth-child(2), .msdl-fm-table th:nth-child(3), .msdl-fm-table td:nth-child(3) { display: none; }
                .msdl-fm-header { flex-direction: column; align-items: stretch; padding: 15px;}
                .msdl-fm-search { width: 100%; margin-top: 10px; }
                .msdl-fm-fv-card { padding: 20px; border: none; box-shadow: none;}
                .msdl-fm-fv-actions { flex-direction: column; width: 100%; }
                .msdl-fm-fv-actions .msdl-fm-btn, .msdl-fm-fv-actions .msdl-fm-btn-outline { width: 100%; justify-content: center; margin: 0;}
            }
        </style>

        <div class="msdl-toast" id="msdl-toast-<?php echo $uid; ?>">
            <svg style="width:14px;height:14px;fill:#4caf50;" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg> 
            Link másolva!
        </div>

        <div id="<?php echo $uid; ?>" class="msdl-fm-wrapper">
            <?php if ( $settings['show_header'] === 'yes' ) : ?>
            <div class="msdl-fm-header">
                <?php if ( $settings['show_breadcrumbs'] === 'yes' ) : ?><div class="msdl-fm-breadcrumbs"></div><?php else: ?><div></div><?php endif; ?>
                <?php if ( $settings['enable_search'] === 'yes' ) : ?>
                    <div class="msdl-fm-search"><i class="fas fa-search"></i><input type="text" id="msdl-fm-search-input" placeholder="Keresés fájlok és mappák között..."></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="msdl-fm-body">
                <div class="msdl-fm-loader"><div class="msdl-fm-spinner"></div><span class="msdl-loader-text">Lekérés folyamatban...</span></div>
                
                <div class="msdl-fm-view-table" style="overflow-x:auto;">
                    <table class="msdl-fm-table">
                        <thead><tr><th class="msdl-col-name">Név</th><?php if ( $settings['show_size'] === 'yes' ) echo '<th class="msdl-col-meta">Méret</th>'; ?><?php if ( $settings['show_date'] === 'yes' ) echo '<th class="msdl-col-meta">Módosítva</th>'; ?><th class="msdl-col-action">Művelet</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="msdl-fm-file-view">
                    <div class="msdl-fm-fv-header">
                        <button class="msdl-fm-back-btn" data-folder="root"><svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 448 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg> Vissza a mappába</button>
                    </div>
                    <div class="msdl-fm-fv-card">
                        <div class="msdl-fm-fv-icon"></div><h3 class="msdl-fm-fv-title"></h3><div class="msdl-fm-fv-desc"></div><div class="msdl-fm-fv-meta"></div><div class="msdl-fm-fv-actions"></div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var uid = "<?php echo $uid; ?>"; var $wrapper = $('#' + uid); var $toast = $('#msdl-toast-' + uid); var ajaxUrl = "<?php echo esc_url($ajax_url); ?>"; var nonce = "<?php echo $nonce; ?>";
            var baseFolder = "<?php echo $base_folder; ?>"; var allowUp = <?php echo $allow_up; ?>; var allowDown = <?php echo $allow_down; ?>;
            var showSize = <?php echo $show_size; ?>; var showDate = <?php echo $show_date; ?>; var showTableIcon = <?php echo $show_table_icon; ?>;
            var fvShowIcon = <?php echo $fv_show_icon; ?>; var fvShowTitle = <?php echo $fv_show_title; ?>; var fvShowDesc = <?php echo $fv_show_desc; ?>;
            var fvShowExt = <?php echo $fv_show_ext; ?>; var fvShowSize = <?php echo $fv_show_size; ?>; var fvShowDate = <?php echo $fv_show_date; ?>;
            var fvShowDownload = <?php echo $fv_show_download; ?>; var fvShowCopy = <?php echo $fv_show_copy; ?>;
            
            var showVersion = <?php echo $show_version ? 'true' : 'false'; ?>;
            
            var siteUrl = "<?php echo site_url('/?msdl_download='); ?>"; var isEditor = <?php echo \Elementor\Plugin::$instance->editor->is_edit_mode() ? 'true' : 'false'; ?>;
            var urlParams = new URLSearchParams(window.location.search);
            var currentFolder = urlParams.get('msdl_folder') || baseFolder; var currentSearch = urlParams.get('msdl_search') || ''; var currentFile = urlParams.get('msdl_file') || '';

            if (currentSearch !== '' && $wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val(currentSearch);

            function updateURL(type, id, searchQ) {
                if (isEditor) return;
                var newUrl = new URL(window.location);
                newUrl.searchParams.delete('msdl_search'); newUrl.searchParams.delete('msdl_folder'); newUrl.searchParams.delete('msdl_file');
                if (type === 'file') newUrl.searchParams.set('msdl_file', id);
                else if (type === 'search') newUrl.searchParams.set('msdl_search', searchQ);
                else if (type === 'folder' && id !== baseFolder && id !== 'root' && id !== '0') newUrl.searchParams.set('msdl_folder', id);
                window.history.pushState({type: type, id: id, searchQ: searchQ}, '', newUrl);
            }

            function loadVersions(container) {
                var $badges = container.find('.msdl-version-badge:not(.loaded)');
                if ($badges.length > 0) {
                    $badges.each(function() {
                        var $b = $(this);
                        var fId = $b.data('file-id');
                        $b.addClass('loaded');
                        fetch('<?php echo esc_url( rest_url('msdl-child/v1/public-file-versions?id=') ); ?>' + fId)
                        .then(function(r){ return r.json(); })
                        .then(function(vData){
                            if(vData && vData.total) {
                                var title = "Aktuális verzió: V" + vData.total + "\nKorábbi változatok: " + vData.previous + "\nUtolsó módosítás: " + vData.last_modified;
                                $b.attr('title', title).find('.v-text').text("Verzió: V" + vData.total);
                            } else {
                                $b.hide();
                            }
                        }).catch(function(){ $b.hide(); });
                    });
                }
            }

            function loadFolder(folderId, searchQ = '') {
                $wrapper.find('.msdl-fm-file-view').hide(); $wrapper.find('.msdl-fm-view-table').show();
                $wrapper.find('.msdl-loader-text').text('Mappatartalom lekérése...'); $wrapper.find('.msdl-fm-loader').css('display', 'flex');
                
                $.post(ajaxUrl, { action: 'msdl_frontend_get_folder', nonce: nonce, folder_id: folderId, search: searchQ }, function(response) {
                    $wrapper.find('.msdl-fm-loader').hide();
                    if (response.success) {
                        currentFolder = folderId; currentSearch = searchQ; currentFile = '';
                        updateURL(searchQ !== '' ? 'search' : 'folder', folderId, searchQ);
                        
                        if ($wrapper.find('.msdl-fm-breadcrumbs').length) {
                            var bcHtml = '';
                            if (searchQ !== '') bcHtml = '<span>Keresési eredmények: "'+searchQ+'"</span> <a class="msdl-fm-bc-link" data-id="'+currentFolder+'" style="margin-left:10px; font-size:13px;"><i class="fas fa-times"></i> Bezárás</a>';
                            else {
                                var displayCrumbs = [];
                                if (allowUp || baseFolder === 'root' || baseFolder === '0') displayCrumbs = response.data.breadcrumbs;
                                else {
                                    var startAdding = false;
                                    response.data.breadcrumbs.forEach(function(bc) { if (bc.id == baseFolder) startAdding = true; if (startAdding) displayCrumbs.push(bc); });
                                    if (displayCrumbs.length === 0) displayCrumbs = response.data.breadcrumbs;
                                }
                                displayCrumbs.forEach(function(bc, i) {
                                    if (i === displayCrumbs.length - 1) bcHtml += '<span class="msdl-fm-current">' + bc.name + '</span>';
                                    else bcHtml += '<a class="msdl-fm-bc-link" data-id="' + bc.id + '">' + bc.name + '</a> <span class="msdl-fm-sep">/</span> ';
                                });
                            }
                            $wrapper.find('.msdl-fm-breadcrumbs').html(bcHtml);
                        }

                        var $tbody = $wrapper.find('tbody'); $tbody.empty();
                        var itemsToRender = response.data.items;
                        if (!allowDown) itemsToRender = itemsToRender.filter(function(item) { return item.type !== 'folder'; });

                        if (itemsToRender.length === 0) {
                            var colSpan = 1; if(showSize) colSpan++; if(showDate) colSpan++;
                            $tbody.append('<tr><td colspan="'+(colSpan+1)+'" style="text-align:center; padding:60px 20px; color:#a0a6b5; font-size:15px;"><i class="fas fa-folder-open" style="font-size:40px; display:block; margin-bottom:15px; opacity:0.3;"></i> A mappa üres, vagy nincs találat.</td></tr>');
                            return;
                        }

                        itemsToRender.forEach(function(item) {
                            var rowClass = item.type === 'folder' ? 'msdl-fm-item-folder' : 'msdl-fm-item-file';
                            var actionHtml = ''; var nameHtml = ''; var iconHtml = showTableIcon ? '<span class="msdl-fm-icon">'+item.icon_html+'</span> ' : '';

                            if (item.type === 'folder') {
                                nameHtml = '<a class="msdl-fm-item-name msdl-fm-dir-link '+rowClass+'" data-id="'+item.id+'">' + iconHtml + '<span>'+item.name+'</span></a>';
                                actionHtml = '<div class="msdl-action-buttons"><a class="msdl-fm-btn-outline msdl-fm-dir-link" data-id="'+item.id+'">Megnyitás</a></div>';
                            } else {
                                nameHtml = '<a class="msdl-fm-item-name msdl-fm-file-link '+rowClass+'" data-id="'+item.id+'">' + iconHtml + '<span>'+item.name+'</span></a>';
                                var dUrl = siteUrl + item.id;
                                actionHtml = '<div class="msdl-action-buttons"><a class="msdl-fm-btn-outline msdl-fm-file-link" data-id="'+item.id+'" title="Részletek">Részletek</a><a href="'+dUrl+'" target="_blank" class="msdl-fm-btn" title="Gyors letöltés"><i class="fas fa-download"></i></a></div>';
                            }

                            var trHtml = '<tr><td class="msdl-col-name">' + nameHtml + '</td>';
                            if(showSize) trHtml += '<td class="msdl-col-meta" style="color:#787c82;">' + item.size + '</td>';
                            if(showDate) trHtml += '<td class="msdl-col-meta" style="color:#787c82;">' + item.date + '</td>';
                            trHtml += '<td class="msdl-col-action">' + actionHtml + '</td></tr>';
                            $tbody.append(trHtml);
                        });
                    }
                });
            }

            function loadFile(fileId) {
                $wrapper.find('.msdl-fm-view-table').hide(); $wrapper.find('.msdl-fm-file-view').hide();
                if ($wrapper.find('.msdl-fm-breadcrumbs').length) $wrapper.find('.msdl-fm-breadcrumbs').html('<span class="msdl-fm-current">Fájl Adatlap</span>');
                $wrapper.find('.msdl-loader-text').text('Fájl adatlap lekérése...'); $wrapper.find('.msdl-fm-loader').css('display', 'flex');

                $.post(ajaxUrl, { action: 'msdl_frontend_get_file', nonce: nonce, file_id: fileId }, function(response) {
                    $wrapper.find('.msdl-fm-loader').hide();
                    if (response.success) {
                        currentFile = fileId; currentSearch = ''; updateURL('file', fileId, '');
                        var data = response.data; var $fv = $wrapper.find('.msdl-fm-file-view');
                        $fv.find('.msdl-fm-back-btn').attr('data-folder', data.parent_id);
                        if (fvShowIcon) $fv.find('.msdl-fm-fv-icon').html(data.icon_html).show(); else $fv.find('.msdl-fm-fv-icon').hide();
                        if (fvShowTitle) $fv.find('.msdl-fm-fv-title').text(data.name).show(); else $fv.find('.msdl-fm-fv-title').hide();
                        if (fvShowDesc && data.description && data.description.trim() !== '') $fv.find('.msdl-fm-fv-desc').html(data.description).show(); else $fv.find('.msdl-fm-fv-desc').hide();
                        
                        var metaHtml = '';
                        if (showVersion) {
                            metaHtml += '<span class="msdl-version-badge" data-file-id="'+data.id+'" title="Verzió betöltése..."><i class="fas fa-info-circle"></i> <span class="v-text">Verzió infó...</span></span>';
                        }
                        if (fvShowExt) metaHtml += '<span>' + data.ext + '</span>';
                        if (fvShowSize) metaHtml += '<span>' + data.size + '</span>';
                        if (fvShowDate) metaHtml += '<span><i class="far fa-calendar-alt"></i> ' + data.date + '</span>';
                        $fv.find('.msdl-fm-fv-meta').html(metaHtml);

                        var absUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?msdl_file=" + data.id;
                        var actionsHtml = '';
                        if (fvShowDownload) actionsHtml += '<a href="'+data.download_url+'" target="_blank" class="msdl-fm-btn"><svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 512 512"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg> Letöltés</a>';
                        if (fvShowCopy) actionsHtml += '<button class="msdl-fm-btn-outline msdl-copy-link" data-url="'+absUrl+'" title="Link másolása"><svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 640 512"><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg> Link másolása</button>';
                        $fv.find('.msdl-fm-fv-actions').html(actionsHtml);

                        $fv.fadeIn(300);
                        loadVersions($wrapper);
                    } else { alert(response.data || "Hiba történt a fájl betöltésekor."); loadFolder(baseFolder, ''); }
                });
            }

            if (currentFile !== '') loadFile(currentFile); else loadFolder(currentFolder, currentSearch);
            $wrapper.on('click', '.msdl-fm-dir-link, .msdl-fm-bc-link', function(e) { e.preventDefault(); if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val(''); loadFolder($(this).data('id'), ''); });
            $wrapper.on('click', '.msdl-fm-file-link', function(e) { e.preventDefault(); loadFile($(this).data('id')); });
            $wrapper.on('click', '.msdl-fm-back-btn', function(e) { e.preventDefault(); var pId = $(this).attr('data-folder'); if (!allowUp && pId === 'root' && baseFolder !== 'root' && baseFolder !== '0') pId = baseFolder; loadFolder(pId, ''); });
            $wrapper.on('click', '.msdl-copy-link', function(e) { e.preventDefault(); var text = $(this).data('url'); var tempInput = document.createElement("input"); tempInput.value = text; document.body.appendChild(tempInput); tempInput.select(); document.execCommand("copy"); document.body.removeChild(tempInput); $toast.addClass('show'); setTimeout(function() { $toast.removeClass('show'); }, 2500); });
            var searchTimer;
            $wrapper.on('input', '#msdl-fm-search-input', function() { clearTimeout(searchTimer); var q = $(this).val(); searchTimer = setTimeout(function() { loadFolder(currentFolder, q); }, 500); });
            window.addEventListener('popstate', function(e) { if (e.state) { if (e.state.type === 'file') loadFile(e.state.id); else if (e.state.type === 'search') { if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val(e.state.searchQ); loadFolder(e.state.id, e.state.searchQ); } else { if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val(''); loadFolder(e.state.id, ''); } } else { if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val(''); loadFolder(baseFolder, ''); } });
        });
        </script>
        <?php
    }
}