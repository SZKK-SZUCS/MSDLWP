<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Widget_File_Card extends \Elementor\Widget_Base {

    public function get_name() { return 'msdl_file_card'; }
    public function get_title() { return 'MSDL Fájl Kártya'; }
    public function get_icon() { return 'eicon-info-box'; }
    public function get_categories() { return [ 'msdl-widgets' ]; }

    public function get_style_depends() { return [ 'elementor-icons-fa-solid' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_query', [ 'label' => 'Adatforrás', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'file_id', [ 'label' => 'Fájl Kiválasztása', 'type' => 'msdl_picker', 'item_type' => 'file' ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_elements', [ 'label' => 'Megjelenítendő Elemek', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_icon', [ 'label' => 'Ikon Mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_title', [ 'label' => 'Fájlnév / Cím Mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_desc', [ 'label' => 'Leírás Mutatása a kártya alatt', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_1', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        
        $this->add_control( 'show_meta_version', [ 'label' => 'Verzió (Legújabb)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta_ext', [ 'label' => 'Kiterjesztés', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta_size', [ 'label' => 'Méret', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta_date', [ 'label' => 'Módosítás Dátuma', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_2', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'show_button', [ 'label' => 'Gomb Mutatása', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'btn_text', [ 'label' => 'Gomb Szövege', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Letöltés', 'condition' => [ 'show_button' => 'yes' ] ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_template', [ 'label' => 'Dizájn Sablon', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'card_template', [ 'label' => 'Sablon Választó', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => [ 'tpl-list' => 'Vízszintes Lista', 'tpl-grid' => 'Kompakt Rács / Kártya', 'custom' => 'Egyéni' ], 'default' => 'custom' ] );
        $this->add_control( 'layout_style', [ 'label' => 'Asztali Elrendezés', 'type' => \Elementor\Controls_Manager::CHOOSE, 'options' => [ 'row' => [ 'title' => 'Vízszintes', 'icon' => 'eicon-h-align-left' ], 'column' => [ 'title' => 'Függőleges', 'icon' => 'eicon-v-align-top' ] ], 'default' => 'row', 'condition' => [ 'card_template' => 'custom' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_card_style', [ 'label' => '1. Kártya Doboz', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->start_controls_tabs( 'tabs_card_style' );
        $this->start_controls_tab( 'tab_card_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'card_bg_color', [ 'label' => 'Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .msdl-fc-wrapper' ] );
        $this->add_responsive_control( 'card_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        // JAVÍTÁS: Kisebb default padding
        $this->add_responsive_control( 'card_padding', [ 'label' => 'Belső Margó', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '15', 'right' => '15', 'bottom' => '15', 'left' => '15', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_box_shadow', 'selector' => '{{WRAPPER}} .msdl-fc-wrapper' ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_card_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'card_hover_bg_color', [ 'label' => 'Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'card_hover_border_color', [ 'label' => 'Keret Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#d0d0d0', 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper:hover' => 'border-color: {{VALUE}};' ] ] );
        $this->add_control( 'card_hover_transform', [ 'label' => 'Felemelkedés (Y tengely)', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => -20, 'max' => 0, 'step' => 1 ] ], 'default' => [ 'unit' => 'px', 'size' => -2 ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper:hover' => 'transform: translateY({{SIZE}}{{UNIT}});' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_hover_box_shadow', 'selector' => '{{WRAPPER}} .msdl-fc-wrapper:hover' ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->start_controls_section( 'section_icon_style', [ 'label' => '2. Fő Ikon', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_responsive_control( 'icon_size', [ 'label' => 'Ikon Mérete', 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => [ 'unit' => 'px', 'size' => 24 ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-icon i' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .msdl-fc-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'icon_container_size', [ 'label' => 'Doboz Mérete (Ha van háttér)', 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => [ 'unit' => 'px', 'size' => 24 ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
        $this->start_controls_tabs( 'tabs_icon_style' );
        $this->start_controls_tab( 'tab_icon_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'icon_color', [ 'label' => 'Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#50ADC9', 'selectors' => [ '{{WRAPPER}} .msdl-fc-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .msdl-fc-icon svg' => 'fill: {{VALUE}};' ] ] );
        $this->add_control( 'icon_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fc-icon' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'icon_box_shadow', 'selector' => '{{WRAPPER}} .msdl-fc-icon' ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_icon_hover', [ 'label' => 'Hover (Kártya)' ] );
        $this->add_control( 'icon_hover_color', [ 'label' => 'Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper:hover .msdl-fc-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .msdl-fc-wrapper:hover .msdl-fc-icon svg' => 'fill: {{VALUE}};' ] ] );
        $this->add_control( 'icon_hover_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fc-wrapper:hover .msdl-fc-icon' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'icon_border', 'selector' => '{{WRAPPER}} .msdl-fc-icon', 'separator' => 'before' ] );
        $this->add_responsive_control( 'icon_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_title_style', [ 'label' => '3. Cím (Fájlnév)', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .msdl-fc-title' ] );
        $this->start_controls_tabs( 'tabs_title_style' );
        $this->start_controls_tab( 'tab_title_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'title_color', [ 'label' => 'Cím Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#242943', 'selectors' => [ '{{WRAPPER}} .msdl-fc-title, {{WRAPPER}} .msdl-fc-title a' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_title_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'title_hover_color', [ 'label' => 'Cím Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#50ADC9', 'selectors' => [ '{{WRAPPER}} .msdl-fc-title a:hover' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->start_controls_section( 'section_meta_style', [ 'label' => '4. Meta Adatok (Méret, Dátum)', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'meta_typography', 'selector' => '{{WRAPPER}} .msdl-fc-meta' ] );
        $this->add_control( 'meta_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#787c82', 'selectors' => [ '{{WRAPPER}} .msdl-fc-meta' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'meta_icon_color', [ 'label' => 'Ikonok Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fc-meta i' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'meta_gap', [ 'label' => 'Távolság az elemektől', 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => [ 'unit' => 'px', 'size' => 6 ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-meta' => 'gap: {{SIZE}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_version_style', [ 'label' => '5. Verzió Jelzés (Badge)', 'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => [ 'show_meta_version' => 'yes' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'version_typo', 'selector' => '{{WRAPPER}} .msdl-version-badge' ] );
        $this->start_controls_tabs( 'tabs_version_style' );
        $this->start_controls_tab( 'tab_version_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'version_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => 'rgba(34,113,177,0.08)', 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'version_text_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#2271b1', 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'version_icon_color', [ 'label' => 'Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#2271b1', 'selectors' => [ '{{WRAPPER}} .msdl-version-badge i' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_version_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'version_hover_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => 'rgba(34,113,177,0.15)', 'selectors' => [ '{{WRAPPER}} .msdl-version-badge:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'version_hover_text_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'version_hover_icon_color', [ 'label' => 'Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-version-badge:hover i' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'version_border', 'selector' => '{{WRAPPER}} .msdl-version-badge', 'separator' => 'before' ] );
        $this->add_responsive_control( 'version_padding', [ 'label' => 'Belső Margó', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '2', 'right' => '6', 'bottom' => '2', 'left' => '6', 'unit' => 'px', 'isLinked' => false ], 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'version_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .msdl-version-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_desc_style', [ 'label' => '6. Leírás Doboz', 'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => [ 'show_desc' => 'yes' ] ] );
        $this->add_control( 'desc_bg_color', [ 'label' => 'Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => 'rgba(0,0,0,0.02)', 'selectors' => [ '{{WRAPPER}} .msdl-fc-desc-box' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'desc_text_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#50575e', 'selectors' => [ '{{WRAPPER}} .msdl-fc-desc-box' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .msdl-fc-desc-box' ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'desc_border', 'selector' => '{{WRAPPER}} .msdl-fc-desc-box' ] );
        $this->add_responsive_control( 'desc_padding', [ 'label' => 'Belső Margó', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '10', 'right' => '15', 'bottom' => '10', 'left' => '15', 'unit' => 'px', 'isLinked' => false ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-desc-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'desc_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '6', 'right' => '6', 'bottom' => '6', 'left' => '6', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-desc-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'desc_margin_top', [ 'label' => 'Távolság a kártyától', 'type' => \Elementor\Controls_Manager::SLIDER, 'default' => [ 'unit' => 'px', 'size' => 12 ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-desc-box' => 'margin-top: {{SIZE}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_btn_style', [ 'label' => '7. Letöltés Gomb', 'tab' => \Elementor\Controls_Manager::TAB_STYLE, 'condition' => [ 'show_button' => 'yes' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'btn_typography', 'selector' => '{{WRAPPER}} .msdl-fc-btn' ] );
        $this->start_controls_tabs( 'tabs_btn_style' );
        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'btn_text_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn' => 'color: {{VALUE}};' ] ]);
        $this->add_control( 'btn_bg_color', [ 'label' => 'Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#50ADC9', 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn' => 'background-color: {{VALUE}};' ] ]);
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_box_shadow', 'selector' => '{{WRAPPER}} .msdl-fc-btn' ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'btn_hover_text_color', [ 'label' => 'Szöveg Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn:hover' => 'color: {{VALUE}};' ] ]);
        $this->add_control( 'btn_hover_bg_color', [ 'label' => 'Háttérszín', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#3b8ba6', 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn:hover' => 'background-color: {{VALUE}};' ] ]);
        $this->add_control( 'btn_hover_border_color', [ 'label' => 'Keret Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn:hover' => 'border-color: {{VALUE}};' ] ]);
        $this->add_control( 'btn_hover_animation', [ 'label' => 'Hover Animáció', 'type' => \Elementor\Controls_Manager::HOVER_ANIMATION ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => '{{WRAPPER}} .msdl-fc-btn', 'separator' => 'before' ] );
        $this->add_responsive_control( 'btn_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '6', 'right' => '6', 'bottom' => '6', 'left' => '6', 'unit' => 'px', 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ]);
        $this->add_responsive_control( 'btn_padding', [ 'label' => 'Belső Margó', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'default' => [ 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'unit' => 'px', 'isLinked' => false ], 'selectors' => [ '{{WRAPPER}} .msdl-fc-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ]);
        $this->end_controls_section();
    }

    protected function render() {
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        if ( ! $is_editor ) wp_enqueue_style( 'elementor-icons-fa-solid' );

        $settings = $this->get_settings_for_display();
        $uid = $this->get_id();
        $file_id = intval( $settings['file_id'] );

        $file_name = 'Kérlek, válassz ki egy fájlt!';
        $file_desc = '';
        $file_size = '-';
        $file_ext = 'file';
        $file_date = '-';
        $download_url = '#';

        if ( ! $is_editor && ! MSDL_Child_Elementor::check_item_access( 'public' ) ) {
            return;
        }

        if ( $file_id > 0 ) {
            global $wpdb;
            $table = $wpdb->prefix . 'msdl_nodes';
            $file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d AND type = 'file'", $file_id ) );

            if ( $file ) {
                if ( $file->visibility_roles === 'hidden' ) {
                    if ( $is_editor ) {
                        echo '<div style="padding:10px; background:#f8d7da; color:#d63638; border-radius:4px; margin-bottom:10px; font-size:12px; text-align:center;">Figyelem: Ez a fájl REJTETT (Lomtár). A látogatók nem fogják látni.</div>';
                    } else {
                        return; 
                    }
                } elseif ( ! $is_editor && ! MSDL_Child_Elementor::check_item_access( $file->visibility_roles ) ) {
                    return; 
                }

                $ext = pathinfo( $file->name, PATHINFO_EXTENSION );
                if ( $ext ) $file_ext = strtolower( $ext );

                $file_name = $file->name;
                if ( !empty($file->custom_title) ) {
                    $file_name = $file->custom_title;
                    if ( $file_ext !== 'file' && !preg_match('/\.'.$file_ext.'$/i', $file_name) ) {
                        $file_name .= '.' . $file_ext;
                    }
                }

                $file_desc = !empty($file->custom_description) ? wp_kses_post( $file->custom_description ) : '';
                $download_url = site_url( '/?msdl_download=' . $file_id );
                
                if ( isset( $file->size ) ) {
                    $bytes = intval($file->size);
                    if ( $bytes >= 1048576 ) {
                        $file_size = round($bytes / 1048576, 2) . ' MB';
                    } elseif ( $bytes >= 1024 ) {
                        $file_size = round($bytes / 1024, 0) . ' KB';
                    } else {
                        $file_size = $bytes > 0 ? $bytes . ' B' : '-';
                    }
                }
                if ( !empty($file->last_modified) && $file->last_modified !== '0000-00-00 00:00:00' ) {
                    $file_date = date( 'Y.m.d.', strtotime( $file->last_modified ) );
                }
            }
        }

        $icon_class = 'fas fa-file';
        if ( in_array( $file_ext, ['pdf'] ) ) $icon_class = 'fas fa-file-pdf';
        elseif ( in_array( $file_ext, ['doc', 'docx'] ) ) $icon_class = 'fas fa-file-word';
        elseif ( in_array( $file_ext, ['xls', 'xlsx', 'csv'] ) ) $icon_class = 'fas fa-file-excel';
        elseif ( in_array( $file_ext, ['jpg', 'jpeg', 'png', 'gif'] ) ) $icon_class = 'fas fa-file-image';
        elseif ( in_array( $file_ext, ['zip', 'rar'] ) ) $icon_class = 'fas fa-file-archive';

        $icon_render = '';
        if ( class_exists( '\Elementor\Icons_Manager' ) ) {
            ob_start(); \Elementor\Icons_Manager::render_icon( [ 'value' => $icon_class, 'library' => 'fa-solid' ], [ 'aria-hidden' => 'true' ] ); $icon_render = ob_get_clean();
        }
        if ( empty( $icon_render ) ) $icon_render = sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $icon_class ) );

        $layout = $settings['layout_style'] ?? 'row';
        $show_icon = $settings['show_icon'] === 'yes';
        $show_title = $settings['show_title'] === 'yes';
        $show_desc = $settings['show_desc'] === 'yes';
        $show_version = $settings['show_meta_version'] === 'yes';
        $show_ext = $settings['show_meta_ext'] === 'yes';
        $show_size = $settings['show_meta_size'] === 'yes';
        $show_date = $settings['show_meta_date'] === 'yes';
        $show_btn = $settings['show_button'] === 'yes';

        $has_meta = $show_ext || $show_size || $show_date || $show_version;
        $btn_classes = 'msdl-fc-btn' . ( !empty($settings['btn_hover_animation']) ? ' elementor-animation-' . $settings['btn_hover_animation'] : '' );

        ?>
        <style>
            .msdl-fc-outer-container { display: flex; flex-direction: column; width: 100%; height: 100%; }
            .msdl-fc-wrapper { display: flex; gap: 15px; box-sizing: border-box; transition: all 0.3s ease; font-family: inherit; height: 100%; position: relative; overflow: hidden; width: 100%; }
            
            .msdl-fc-wrapper.layout-row { flex-direction: row; align-items: center; justify-content: space-between; text-align: left; flex-wrap: wrap;}
            .msdl-fc-wrapper.layout-row .msdl-fc-content { flex-grow: 1; min-width: 150px; }
            .msdl-fc-wrapper.layout-row .msdl-fc-action { flex-shrink: 0; margin-left: auto; }
            
            .msdl-fc-wrapper.layout-column { flex-direction: column; align-items: center; text-align: center; }
            .msdl-fc-wrapper.layout-column .msdl-fc-action { width: 100%; margin-top: auto; padding-top: 15px;}
            .msdl-fc-wrapper.layout-column .msdl-fc-btn { display: block; width: 100%; text-align: center; box-sizing: border-box; }

            .msdl-fc-icon { flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; line-height: 1; transition: all 0.3s ease;}
            .msdl-fc-icon svg { fill: currentColor; transition: all 0.3s ease;}
            .msdl-fc-icon i { transition: all 0.3s ease;}
            
            .msdl-fc-content { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
            .msdl-fc-title { font-size: 15px; margin: 0; word-break: break-word; white-space: normal; line-height: 1.3; transition: all 0.2s;}
            .msdl-fc-title a { text-decoration: none; color: inherit; transition: all 0.2s;}
            
            .msdl-fc-meta { font-size: 12px; display: flex; flex-wrap: wrap; justify-content: inherit; align-items: center; }
            .msdl-fc-meta span { display: inline-flex; align-items: center; }
            .msdl-fc-meta span:not(.msdl-version-badge):not(:last-child)::after { content: '•'; margin-left: 6px; opacity: 0.5; }
            
            .msdl-version-badge { display: inline-flex; align-items: center; gap: 4px; cursor: help; transition: all 0.2s; margin-right: 6px; }
            .msdl-version-badge i { transition: all 0.2s; }
            
            .msdl-fc-btn { font-size: 13px; text-decoration: none !important; transition: all 0.3s ease; display: inline-block; border: 1px solid transparent;}
            
            .msdl-fc-desc-box { font-size: 13px; word-wrap: break-word; }

            @media (max-width: 767px) {
                .msdl-fc-wrapper { flex-direction: column !important; align-items: stretch !important; text-align: center !important; gap: 12px; }
                .msdl-fc-wrapper .msdl-fc-icon { margin: 0 auto; }
                .msdl-fc-wrapper .msdl-fc-meta { justify-content: center; }
                .msdl-fc-wrapper .msdl-fc-action { width: 100% !important; margin: auto 0 0 0 !important; padding-top: 10px; }
                .msdl-fc-wrapper .msdl-fc-btn { display: block; width: 100%; text-align: center; }
            }
        </style>

        <div class="msdl-fc-outer-container" id="msdl-fc-<?php echo esc_attr($uid); ?>">
            <div class="msdl-fc-wrapper layout-<?php echo esc_attr( $layout ); ?>">
                <?php if ( $show_icon ) : ?><div class="msdl-fc-icon"><?php echo $icon_render; ?></div><?php endif; ?>
                <div class="msdl-fc-content">
                    <?php if ( $show_title ) : ?>
                        <h4 class="msdl-fc-title">
                            <a href="<?php echo esc_url( $download_url ); ?>" target="_blank"><?php echo esc_html($file_name); ?></a>
                        </h4>
                    <?php endif; ?>
                    
                    <?php if ( $has_meta ) : ?>
                        <div class="msdl-fc-meta">
                            <?php if ( $show_version ) : ?>
                                <span class="msdl-version-badge" data-file-id="<?php echo $file_id; ?>" title="Verzió információk betöltése...">
                                    <i class="fas fa-code-branch"></i> <span class="v-text">Verzió infó...</span>
                                </span>
                            <?php endif; ?>
                            <?php if ( $show_ext ) : ?><span><?php echo esc_html( strtoupper($file_ext) ); ?></span><?php endif; ?>
                            <?php if ( $show_size ) : ?><span><?php echo $file_size; ?></span><?php endif; ?>
                            <?php if ( $show_date ) : ?><span><i class="far fa-calendar-alt" style="margin-right:4px; opacity:0.7;"></i><?php echo $file_date; ?></span><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ( $show_btn ) : ?>
                    <div class="msdl-fc-action"><a href="<?php echo esc_url( $download_url ); ?>" target="_blank" class="<?php echo esc_attr( $btn_classes ); ?>"><?php echo esc_html( $settings['btn_text'] ); ?></a></div>
                <?php endif; ?>
            </div>
            
            <?php if ( $show_desc && !empty($file_desc) ) : ?>
                <div class="msdl-fc-desc-box"><?php echo $file_desc; ?></div>
            <?php endif; ?>
        </div>

        <?php if ( $show_version && $file_id > 0 ) : ?>
        <script>
        (function($) {
            $(function() {
                setTimeout(function() {
                    var widget = document.getElementById('msdl-fc-<?php echo esc_js($uid); ?>');
                    if (widget) {
                        var badges = widget.querySelectorAll('.msdl-version-badge[data-file-id="<?php echo $file_id; ?>"]:not(.loaded)');
                        if (badges.length > 0) {
                            badges.forEach(function(b) {
                                b.classList.add('loaded');
                                fetch('<?php echo esc_url( rest_url('msdl-child/v1/public-file-versions?id=') ); ?>' + '<?php echo $file_id; ?>')
                                .then(function(res) { return res.json(); })
                                .then(function(data) {
                                    if (data && data.total !== undefined) {
                                        var title = "Aktuális verzió: V" + data.total + "\nKorábbi változatok: " + data.previous + "\nUtolsó módosítás: " + data.last_modified;
                                        b.setAttribute('title', title);
                                        b.querySelector('.v-text').innerText = "Verzió: V" + data.total;
                                    } else {
                                        b.style.display = 'none';
                                    }
                                })
                                .catch(function(err) {
                                    b.style.display = 'none';
                                });
                            });
                        }
                    }
                }, 200);
            });
        })(jQuery);
        </script>
        <?php endif; ?>

        <?php
    }
}