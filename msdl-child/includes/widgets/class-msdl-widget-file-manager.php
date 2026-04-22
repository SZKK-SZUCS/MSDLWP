<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Widget_File_Manager extends \Elementor\Widget_Base {

    public function get_name() { return 'msdl_file_manager'; }
    public function get_title() { return 'MSDL Fájlkezelő'; }
    public function get_icon() { return 'eicon-archive'; }
    public function get_categories() { return [ 'msdl-widgets' ]; }

    public function get_style_depends() { return [ 'elementor-icons-fa-solid' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_query', [ 'label' => 'Navigáció és Bázis', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'start_folder_id', [
            'label' => 'Bázis Mappa (Opcionális)',
            'description' => 'Ha megadsz egy mappát, a felhasználó ez alá nem tud visszalépni.',
            'type' => 'msdl_picker',
            'item_type' => 'folder',
        ]);
        $this->add_control( 'allow_navigate_up', [
            'label' => 'Kifelé navigálás engedélyezése',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'separator' => 'before',
        ]);
        $this->add_control( 'allow_navigate_down', [
            'label' => 'Befelé (Almappákba) navigálás',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_header_elements', [ 'label' => 'Fejléc Elemek', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_header', [ 'label' => 'Teljes Fejléc mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_breadcrumbs', [ 'label' => 'Útvonal (Breadcrumb)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_header' => 'yes' ] ] );
        $this->add_control( 'enable_search', [ 'label' => 'Keresősáv', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_header' => 'yes' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_table_elements', [ 'label' => 'Táblázat Elemek', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_table_header', [ 'label' => 'Táblázat Fejléc Sor', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_table_icon', [ 'label' => 'Fájl/Mappa Ikonok mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_1', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'show_size', [ 'label' => 'Méret Oszlop', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_date', [ 'label' => 'Dátum Oszlop', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_fv_elements', [ 'label' => 'Fájl Részletek', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'fv_show_icon', [ 'label' => 'Nagy Ikon mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_title', [ 'label' => 'Fájlnév mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_desc', [ 'label' => 'Fájl Leírása (ha van)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_2', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'fv_show_ext', [ 'label' => 'Kiterjesztés (MIME) Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_size', [ 'label' => 'Méret Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_date', [ 'label' => 'Dátum Meta', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_3', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'fv_show_download', [ 'label' => 'Letöltés Gomb', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'fv_show_copy', [ 'label' => 'Link Másolása Gomb', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->end_controls_section();

        // 1. Globális és Hátterek
        $this->start_controls_section( 'style_global', [ 'label' => 'Globális Stílus', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'bg_color', [ 'label' => 'Külső Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-wrapper' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'wrapper_border', 'selector' => '{{WRAPPER}} .msdl-fm-wrapper' ] );
        $this->add_responsive_control( 'wrapper_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .msdl-fm-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'wrapper_shadow', 'selector' => '{{WRAPPER}} .msdl-fm-wrapper' ] );
        $this->add_control( 'text_color', [ 'label' => 'Általános Szövegszín', 'type' => \Elementor\Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .msdl-fm-table td' => 'color: {{VALUE}};', '{{WRAPPER}} .msdl-fm-item-name' => 'color: {{VALUE}};', '{{WRAPPER}} .msdl-fm-fv-title' => 'color: {{VALUE}};', '{{WRAPPER}} .msdl-fm-fv-desc' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'text_typo', 'selector' => '{{WRAPPER}} .msdl-fm-wrapper' ] );
        $this->end_controls_section();

        // 2. Fejléc és Kereső
        $this->start_controls_section( 'style_header', [ 'label' => 'Fejléc és Navigáció', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'header_bg_color', [ 'label' => 'Fejléc Háttere', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-header' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'bc_link_color', [ 'label' => 'Útvonal Linkek (Navigáció)', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-breadcrumbs a' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'bc_active_color', [ 'label' => 'Aktuális Mappa Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-breadcrumbs span.msdl-fm-current' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'search_focus_color', [ 'label' => 'Kereső Kerete (Fókusz)', 'type' => \Elementor\Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .msdl-fm-search input:focus' => 'border-color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        // 3. Táblázat
        $this->start_controls_section( 'style_table', [ 'label' => 'Táblázat', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'th_color', [ 'label' => 'Oszlop Fejléc Szövege', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-table th' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'row_border', [ 'label' => 'Sor Elválasztó Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-table td' => 'border-bottom-color: {{VALUE}};' ] ] );
        $this->add_control( 'row_hover', [ 'label' => 'Sor Hover Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-table tr:hover td' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        // 4. Ikonok
        $this->start_controls_section( 'style_icons', [ 'label' => 'Ikonok', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'folder_icon_color', [ 'label' => 'Mappa Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-item-folder .msdl-fm-icon svg' => 'fill: {{VALUE}} !important;', '{{WRAPPER}} .msdl-fm-item-folder .msdl-fm-icon i' => 'color: {{VALUE}} !important;' ] ] );
        $this->add_control( 'file_icon_color', [ 'label' => 'Fájl Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-item-file .msdl-fm-icon svg' => 'fill: {{VALUE}};', '{{WRAPPER}} .msdl-fm-item-file .msdl-fm-icon i' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'fv_icon_bg', [ 'label' => 'Adatlap Nagy Ikon Háttere', 'type' => \Elementor\Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .msdl-fm-fv-icon' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        // 5. Akció Gombok (Letöltés, Megnyitás)
        $this->start_controls_section( 'style_buttons', [ 'label' => 'Fő Gombok', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'btn_typo', 'selector' => '{{WRAPPER}} .msdl-fm-btn, {{WRAPPER}} .msdl-fm-btn-outline' ] );
        
        $this->start_controls_tabs( 'tabs_btn' );
        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'btn_bg', [ 'label' => 'Gomb Háttere', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_color', [ 'label' => 'Gomb Szövege', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'btn_hover_bg', [ 'label' => 'Hover Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_hover_color', [ 'label' => 'Hover Szöveg', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn:hover' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'outline_color', [ 'label' => 'Másolás (Másodlagos) Gomb Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .msdl-fm-btn-outline' => 'color: {{VALUE}}; border-color: {{VALUE}};' ] ] );
        $this->end_controls_section();

        // 6. Vissza a mappába gomb
        $this->start_controls_section( 'style_back_btn', [ 'label' => 'Vissza Gomb', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'back_btn_typo', 'selector' => '{{WRAPPER}} .msdl-fm-back-btn' ] );
        $this->add_control( 'back_btn_color', [ 'label' => 'Normál Szín', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-back-btn' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'back_btn_hover', [ 'label' => 'Hover Szín', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fm-back-btn:hover' => 'color: {{VALUE}};' ] ] );
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

        ?>
        <style>
            /* --- JAVÍTOTT, PRÉMIUM ALAPÉRTELMEZETT CSS --- */
            .msdl-fm-wrapper { font-family: inherit; border: 1px solid #eaeaea; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 30px rgba(0,0,0,0.05); background: #fff; color: #242943;}
            .msdl-fm-header { padding: 25px 30px; border-bottom: 1px solid #eaeaea; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; background: #fcfcfc; }
            
            .msdl-fm-breadcrumbs { font-size: 15px; font-weight: 600; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
            .msdl-fm-breadcrumbs a { text-decoration: none; cursor: pointer; transition: all 0.2s; padding: 6px 12px; border-radius: 6px; background: rgba(0,0,0,0.04); color: #2271b1;}
            .msdl-fm-breadcrumbs a:hover { background: rgba(0,0,0,0.08); color: #135e96; }
            .msdl-fm-breadcrumbs span.msdl-fm-sep { color: #a0a6b5; margin: 0 4px; font-size: 13px;}
            .msdl-fm-breadcrumbs span.msdl-fm-current { font-weight: 700; padding: 6px 12px; }
            
            .msdl-fm-search { position: relative; width: 300px; max-width: 100%; margin-left: auto; }
            .msdl-fm-search input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #dcdcde; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.2s; background: #fff; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);}
            .msdl-fm-search input:focus { outline: none; border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
            .msdl-fm-search i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #a0a6b5; font-size: 14px; }
            
            .msdl-fm-body { position: relative; width: 100%; overflow-x: auto; min-height: 300px; padding: 10px;}
            .msdl-fm-loader { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.85); display: flex; flex-direction: column; gap: 15px; align-items: center; justify-content: center; font-weight: 600; font-size: 15px; z-index: 10; display: none; backdrop-filter: blur(2px); border-radius: 0 0 12px 12px;}
            .msdl-fm-spinner { border: 3px solid rgba(0,0,0,0.1); width: 40px; height: 40px; border-radius: 50%; border-left-color: #2271b1; animation: msdl-spin 0.8s linear infinite; }
            @keyframes msdl-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            
            .msdl-fm-table { width: 100%; min-width: 600px; border-collapse: separate; border-spacing: 0; text-align: left; font-size: 14px; margin: 0; padding: 0;}
            <?php if ( $settings['show_table_header'] !== 'yes' ) : ?>
            .msdl-fm-table thead { display: none; }
            <?php endif; ?>
            .msdl-fm-table th { padding: 15px 25px; border-bottom: 1px solid #eaeaea; color: #787c82; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; white-space: nowrap; }
            .msdl-fm-table td { padding: 16px 25px; vertical-align: middle; border-bottom: 1px solid #f6f7f7; transition: background 0.2s;}
            .msdl-fm-table tr:last-child td { border-bottom: none; }
            .msdl-fm-table tr:hover td { background: #f8f9fa; }
            .msdl-fm-table tr:hover td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
            .msdl-fm-table tr:hover td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
            
            .msdl-col-name { width: 100%; min-width: 250px; word-break: break-word; white-space: normal; }
            .msdl-col-meta { white-space: nowrap; width: 120px; }
            .msdl-col-action { white-space: nowrap; width: 130px; text-align: right; }

            .msdl-fm-item-name { display: flex; align-items: center; gap: 14px; font-weight: 600; cursor: pointer; text-decoration: none !important; transition: all 0.2s; line-height: 1.4; color: #242943;}
            .msdl-fm-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; flex-shrink: 0; }
            .msdl-fm-icon svg { width: 24px; height: 24px; }
            .msdl-fm-icon i { font-size: 24px; }
            
            /* Gombok szebb kinézete */
            .msdl-fm-btn { padding: 8px 18px; border-radius: 6px; text-decoration: none !important; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; border: 1px solid; text-align: center; cursor: pointer; white-space: nowrap; font-weight: 600; font-size: 13px;}
            .msdl-fm-btn:hover { opacity: 0.9; transform: translateY(-1px); }
            
            .msdl-fm-dir-link.msdl-fm-btn { background: #f6f7f7; border: 1px solid #dcdcde; color: #242943; }
            .msdl-fm-dir-link.msdl-fm-btn:hover { background: #eaeaea; color: #242943; border-color: #dcdcde;}
            
            .msdl-fm-file-link.msdl-fm-btn { background: rgba(34, 113, 177, 0.1); border: 1px solid transparent; color: #2271b1; }
            .msdl-fm-file-link.msdl-fm-btn:hover { background: #2271b1; color: #ffffff; }

            .msdl-fm-btn-outline { padding: 8px 18px; background: transparent; border-radius: 6px; border: 1px solid #2271b1; color: #2271b1; cursor: pointer; transition: all 0.2s; margin-left: 10px; display: inline-flex; align-items: center; gap: 8px; font-family: inherit; font-weight: 600; font-size: 13px;}
            .msdl-fm-btn-outline:hover { background: rgba(34, 113, 177, 0.05); }

            /* FÁJL RÉSZLETEK */
            .msdl-fm-file-view { display: none; padding: 40px 30px; text-align: center; animation: msdl-fade-in 0.3s ease; }
            @keyframes msdl-fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .msdl-fm-fv-header { text-align: left; margin-bottom: 30px; }
            
            .msdl-fm-back-btn { background: none; border: none; font-size: 15px; font-weight: 700; color: #787c82; cursor: pointer; transition: color 0.2s; padding: 0; display: flex; align-items: center; gap: 8px; }
            .msdl-fm-back-btn:hover { color: #242943; }

            .msdl-fm-fv-card { max-width: 500px; margin: 0 auto; padding: 40px; background: #fff; border: 1px solid #f0f2f5; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
            .msdl-fm-fv-icon { width: 80px; height: 80px; border-radius: 50%; background: #2271b1; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(34, 113, 177, 0.2);}
            .msdl-fm-fv-icon svg { width: 40px; height: 40px; fill: #fff; }
            .msdl-fm-fv-icon i { font-size: 36px; color: #fff; }
            .msdl-fm-fv-title { font-size: 24px; font-weight: 700; margin: 0 0 15px 0; word-wrap: break-word; line-height: 1.3; color: #242943;}
            .msdl-fm-fv-desc { font-size: 14px; line-height: 1.6; margin-bottom: 25px; padding: 15px; background: rgba(0,0,0,0.02); border-radius: 8px; border-left: 3px solid #dcdcde; text-align: left; color: #50575e;}
            .msdl-fm-fv-meta { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-bottom: 35px; font-size: 13px; color: #787c82; font-weight: 600;}
            .msdl-fm-fv-meta span { background: #f6f7f7; padding: 6px 14px; border-radius: 20px; border: 1px solid #eaeaea;}
            .msdl-fm-fv-actions { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 15px; }
            
            .msdl-toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #242943; color: #fff; padding: 10px 20px; border-radius: 50px; font-size: 13px; font-weight: 600; opacity: 0; pointer-events: none; transition: all 0.3s ease; z-index: 9999; box-shadow: 0 5px 15px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 8px; }
            .msdl-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

            @media (max-width: 768px) {
                .msdl-fm-table th:nth-child(2), .msdl-fm-table td:nth-child(2),
                .msdl-fm-table th:nth-child(3), .msdl-fm-table td:nth-child(3) { display: none; }
                .msdl-fm-header { flex-direction: column; align-items: flex-start; }
                .msdl-fm-search { width: 100%; margin-left: 0; margin-top: 15px; }
                .msdl-fm-fv-card { padding: 30px 20px; border: none; box-shadow: none;}
                .msdl-fm-fv-actions { flex-direction: column; width: 100%; }
                .msdl-fm-btn, .msdl-fm-btn-outline { width: 100%; justify-content: center; margin-left: 0;}
            }
        </style>

        <div class="msdl-toast" id="msdl-toast-<?php echo $uid; ?>">
            <svg style="width:14px;height:14px;fill:#4caf50;" viewBox="0 0 512 512"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg> 
            Link másolva!
        </div>

        <div id="<?php echo $uid; ?>" class="msdl-fm-wrapper">
            
            <?php if ( $settings['show_header'] === 'yes' ) : ?>
            <div class="msdl-fm-header">
                <?php if ( $settings['show_breadcrumbs'] === 'yes' ) : ?>
                    <div class="msdl-fm-breadcrumbs"></div>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                
                <?php if ( $settings['enable_search'] === 'yes' ) : ?>
                    <div class="msdl-fm-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="msdl-fm-search-input" placeholder="Keresés fájlok és mappák között...">
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="msdl-fm-body">
                <div class="msdl-fm-loader">
                    <div class="msdl-fm-spinner"></div>
                    <span class="msdl-loader-text">Lekérés folyamatban...</span>
                </div>
                
                <div class="msdl-fm-view-table">
                    <table class="msdl-fm-table">
                        <thead>
                            <tr>
                                <th class="msdl-col-name">Név</th>
                                <?php if ( $settings['show_size'] === 'yes' ) echo '<th class="msdl-col-meta">Méret</th>'; ?>
                                <?php if ( $settings['show_date'] === 'yes' ) echo '<th class="msdl-col-meta">Módosítva</th>'; ?>
                                <th class="msdl-col-action">Művelet</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="msdl-fm-file-view">
                    <div class="msdl-fm-fv-header">
                        <button class="msdl-fm-back-btn" data-folder="root">
                            <svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 448 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
                            Vissza a mappába
                        </button>
                    </div>
                    <div class="msdl-fm-fv-card">
                        <div class="msdl-fm-fv-icon"></div>
                        <h3 class="msdl-fm-fv-title"></h3>
                        <div class="msdl-fm-fv-desc"></div>
                        <div class="msdl-fm-fv-meta"></div>
                        <div class="msdl-fm-fv-actions"></div>
                    </div>
                </div>

            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var uid = "<?php echo $uid; ?>";
            var $wrapper = $('#' + uid);
            var $toast = $('#msdl-toast-' + uid);
            var ajaxUrl = "<?php echo esc_url($ajax_url); ?>";
            var nonce = "<?php echo $nonce; ?>";
            
            var baseFolder = "<?php echo $base_folder; ?>";
            var allowUp = <?php echo $allow_up; ?>;
            var allowDown = <?php echo $allow_down; ?>;
            
            var showSize = <?php echo $show_size; ?>;
            var showDate = <?php echo $show_date; ?>;
            var showTableIcon = <?php echo $show_table_icon; ?>;
            
            var fvShowIcon = <?php echo $fv_show_icon; ?>;
            var fvShowTitle = <?php echo $fv_show_title; ?>;
            var fvShowDesc = <?php echo $fv_show_desc; ?>;
            var fvShowExt = <?php echo $fv_show_ext; ?>;
            var fvShowSize = <?php echo $fv_show_size; ?>;
            var fvShowDate = <?php echo $fv_show_date; ?>;
            var fvShowDownload = <?php echo $fv_show_download; ?>;
            var fvShowCopy = <?php echo $fv_show_copy; ?>;
            
            var siteUrl = "<?php echo site_url('/?msdl_download='); ?>";
            var isEditor = <?php echo \Elementor\Plugin::$instance->editor->is_edit_mode() ? 'true' : 'false'; ?>;

            var urlParams = new URLSearchParams(window.location.search);
            var currentFolder = urlParams.get('msdl_folder') || baseFolder;
            var currentSearch = urlParams.get('msdl_search') || '';
            var currentFile = urlParams.get('msdl_file') || '';

            if (currentSearch !== '' && $wrapper.find('#msdl-fm-search-input').length) {
                $('#msdl-fm-search-input').val(currentSearch);
            }

            function updateURL(type, id, searchQ) {
                if (isEditor) return;
                var newUrl = new URL(window.location);
                
                newUrl.searchParams.delete('msdl_search');
                newUrl.searchParams.delete('msdl_folder');
                newUrl.searchParams.delete('msdl_file');

                if (type === 'file') {
                    newUrl.searchParams.set('msdl_file', id);
                } else if (type === 'search') {
                    newUrl.searchParams.set('msdl_search', searchQ);
                } else if (type === 'folder') {
                    if (id !== baseFolder && id !== 'root' && id !== '0') {
                        newUrl.searchParams.set('msdl_folder', id);
                    }
                }
                window.history.pushState({type: type, id: id, searchQ: searchQ}, '', newUrl);
            }

            function loadFolder(folderId, searchQ = '') {
                $wrapper.find('.msdl-fm-file-view').hide();
                $wrapper.find('.msdl-fm-view-table').show();
                $wrapper.find('.msdl-loader-text').text('Mappatartalom lekérése...');
                $wrapper.find('.msdl-fm-loader').css('display', 'flex');
                
                $.post(ajaxUrl, {
                    action: 'msdl_frontend_get_folder',
                    nonce: nonce,
                    folder_id: folderId,
                    search: searchQ
                }, function(response) {
                    $wrapper.find('.msdl-fm-loader').hide();
                    
                    if (response.success) {
                        currentFolder = folderId;
                        currentSearch = searchQ;
                        currentFile = '';
                        updateURL(searchQ !== '' ? 'search' : 'folder', folderId, searchQ);
                        
                        if ($wrapper.find('.msdl-fm-breadcrumbs').length) {
                            var bcHtml = '';
                            if (searchQ !== '') {
                                bcHtml = '<span>Keresési eredmények: "'+searchQ+'"</span> <a class="msdl-fm-bc-link" data-id="'+currentFolder+'" style="margin-left:10px; font-size:13px;"><i class="fas fa-times"></i> Bezárás</a>';
                            } else {
                                var displayCrumbs = [];
                                if (allowUp || baseFolder === 'root' || baseFolder === '0') {
                                    displayCrumbs = response.data.breadcrumbs;
                                } else {
                                    var startAdding = false;
                                    response.data.breadcrumbs.forEach(function(bc) {
                                        if (bc.id == baseFolder) startAdding = true;
                                        if (startAdding) displayCrumbs.push(bc);
                                    });
                                    if (displayCrumbs.length === 0) displayCrumbs = response.data.breadcrumbs;
                                }

                                displayCrumbs.forEach(function(bc, i) {
                                    if (i === displayCrumbs.length - 1) {
                                        bcHtml += '<span class="msdl-fm-current">' + bc.name + '</span>';
                                    } else {
                                        bcHtml += '<a class="msdl-fm-bc-link" data-id="' + bc.id + '">' + bc.name + '</a> <span class="msdl-fm-sep">/</span> ';
                                    }
                                });
                            }
                            $wrapper.find('.msdl-fm-breadcrumbs').html(bcHtml);
                        }

                        var $tbody = $wrapper.find('tbody');
                        $tbody.empty();

                        var itemsToRender = response.data.items;
                        if (!allowDown) {
                            itemsToRender = itemsToRender.filter(function(item) { return item.type !== 'folder'; });
                        }

                        if (itemsToRender.length === 0) {
                            var colSpan = 1; if(showSize) colSpan++; if(showDate) colSpan++;
                            $tbody.append('<tr><td colspan="'+(colSpan+1)+'" style="text-align:center; padding:60px 20px; color:#a0a6b5; font-size:15px;"><i class="fas fa-folder-open" style="font-size:40px; display:block; margin-bottom:15px; opacity:0.3;"></i> A mappa üres, vagy nincs találat.</td></tr>');
                            return;
                        }

                        itemsToRender.forEach(function(item) {
                            var rowClass = item.type === 'folder' ? 'msdl-fm-item-folder' : 'msdl-fm-item-file';
                            var actionHtml = '';
                            var nameHtml = '';
                            var iconHtml = showTableIcon ? '<span class="msdl-fm-icon">'+item.icon_html+'</span> ' : '';

                            if (item.type === 'folder') {
                                nameHtml = '<a class="msdl-fm-item-name msdl-fm-dir-link '+rowClass+'" data-id="'+item.id+'">' + iconHtml + '<span>'+item.name+'</span></a>';
                                actionHtml = '<a class="msdl-fm-btn msdl-fm-dir-link" data-id="'+item.id+'">Megnyitás</a>';
                            } else {
                                nameHtml = '<a class="msdl-fm-item-name msdl-fm-file-link '+rowClass+'" data-id="'+item.id+'">' + iconHtml + '<span>'+item.name+'</span></a>';
                                actionHtml = '<a class="msdl-fm-btn msdl-fm-file-link" style="color: #fff !important; background: #2271b1 !important;" data-id="'+item.id+'">Megtekintés</a>';
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
                $wrapper.find('.msdl-fm-view-table').hide();
                $wrapper.find('.msdl-fm-file-view').hide();
                if ($wrapper.find('.msdl-fm-breadcrumbs').length) $wrapper.find('.msdl-fm-breadcrumbs').html('<span class="msdl-fm-current">Fájl Részletei</span>');
                
                $wrapper.find('.msdl-loader-text').text('Fájl részleteinek lekérése...');
                $wrapper.find('.msdl-fm-loader').css('display', 'flex');

                $.post(ajaxUrl, {
                    action: 'msdl_frontend_get_file',
                    nonce: nonce,
                    file_id: fileId
                }, function(response) {
                    $wrapper.find('.msdl-fm-loader').hide();
                    
                    if (response.success) {
                        currentFile = fileId;
                        currentSearch = '';
                        updateURL('file', fileId, '');

                        var data = response.data;
                        var $fv = $wrapper.find('.msdl-fm-file-view');
                        
                        $fv.find('.msdl-fm-back-btn').attr('data-folder', data.parent_id);
                        
                        if (fvShowIcon) $fv.find('.msdl-fm-fv-icon').html(data.icon_html).show();
                        else $fv.find('.msdl-fm-fv-icon').hide();

                        if (fvShowTitle) $fv.find('.msdl-fm-fv-title').text(data.name).show();
                        else $fv.find('.msdl-fm-fv-title').hide();
                        
                        if (fvShowDesc && data.description && data.description.trim() !== '') {
                            $fv.find('.msdl-fm-fv-desc').html(data.description).show();
                        } else {
                            $fv.find('.msdl-fm-fv-desc').hide();
                        }
                        
                        var metaHtml = '';
                        if (fvShowExt) metaHtml += '<span>' + data.ext + '</span>';
                        if (fvShowSize) metaHtml += '<span>' + data.size + '</span>';
                        if (fvShowDate) metaHtml += '<span><i class="far fa-calendar-alt"></i> ' + data.date + '</span>';
                        $fv.find('.msdl-fm-fv-meta').html(metaHtml);

                        var absUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?msdl_file=" + data.id;
                        var actionsHtml = '';
                        
                        if (fvShowDownload) {
                            actionsHtml += '<a href="'+data.download_url+'" target="_blank" class="msdl-fm-btn" style="color: #fff !important; background: #2271b1 !important;"><svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 512 512"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg> Letöltés</a>';
                        }
                        if (fvShowCopy) {
                            actionsHtml += '<button class="msdl-fm-btn-outline msdl-copy-link" data-url="'+absUrl+'" title="Link másolása"><svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 640 512"><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg> Link másolása</button>';
                        }
                        $fv.find('.msdl-fm-fv-actions').html(actionsHtml);

                        $fv.fadeIn(300);
                    } else {
                        alert(response.data || "Hiba történt a fájl betöltésekor.");
                        loadFolder(baseFolder, '');
                    }
                });
            }

            if (currentFile !== '') {
                loadFile(currentFile);
            } else {
                loadFolder(currentFolder, currentSearch);
            }

            $wrapper.on('click', '.msdl-fm-dir-link, .msdl-fm-bc-link', function(e) {
                e.preventDefault();
                if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val('');
                loadFolder($(this).data('id'), '');
            });

            $wrapper.on('click', '.msdl-fm-file-link', function(e) {
                e.preventDefault();
                loadFile($(this).data('id'));
            });

            $wrapper.on('click', '.msdl-fm-back-btn', function(e) {
                e.preventDefault();
                var pId = $(this).attr('data-folder');
                if (!allowUp && pId === 'root' && baseFolder !== 'root' && baseFolder !== '0') {
                    pId = baseFolder;
                }
                loadFolder(pId, '');
            });

            $wrapper.on('click', '.msdl-copy-link', function(e) {
                e.preventDefault();
                var text = $(this).data('url');
                var tempInput = document.createElement("input");
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                
                $toast.addClass('show');
                setTimeout(function() { $toast.removeClass('show'); }, 2500);
            });

            var searchTimer;
            $wrapper.on('input', '#msdl-fm-search-input', function() {
                clearTimeout(searchTimer);
                var q = $(this).val();
                searchTimer = setTimeout(function() {
                    loadFolder(currentFolder, q);
                }, 500);
            });

            window.addEventListener('popstate', function(e) {
                if (e.state) {
                    if (e.state.type === 'file') {
                        loadFile(e.state.id);
                    } else if (e.state.type === 'search') {
                        if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val(e.state.searchQ);
                        loadFolder(e.state.id, e.state.searchQ);
                    } else {
                        if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val('');
                        loadFolder(e.state.id, '');
                    }
                } else {
                    if ($wrapper.find('#msdl-fm-search-input').length) $('#msdl-fm-search-input').val('');
                    loadFolder(baseFolder, '');
                }
            });
        });
        </script>
        <?php
    }
}