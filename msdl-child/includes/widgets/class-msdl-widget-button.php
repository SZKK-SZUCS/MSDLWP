<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Widget_Button extends \Elementor\Widget_Base {

    public function get_name() { return 'msdl_button'; }
    public function get_title() { return 'MSDL Fájl Letöltés'; }
    public function get_icon() { return 'eicon-download-button'; }
    public function get_categories() { return [ 'msdl-widgets' ]; }

    protected function register_controls() {

        $this->start_controls_section( 'section_query', [ 
            'label' => 'Adatforrás (Query)', 
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT 
        ]);
        $this->add_control( 'file_id', [
            'label' => 'Fájl Tallózása',
            'type' => 'msdl_picker',
            'item_type' => 'file',
            'default' => '',
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_text_settings', [ 
            'label' => 'Gomb Szövege', 
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT 
        ]);
        $this->add_control( 'show_text', [
            'label' => 'Szöveg megjelenítése',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);
        $this->add_control( 'button_text', [
            'label' => 'Felirat',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Letöltés',
            'condition' => [ 'show_text' => 'yes' ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_icon_settings', [ 
            'label' => 'Gomb Ikon', 
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT 
        ]);
        $this->add_control( 'show_icon', [
            'label' => 'Ikon megjelenítése',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);
        $this->add_control( 'button_icon', [
            'label' => 'Ikon kiválasztása',
            'type' => \Elementor\Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-download', 'library' => 'fa-solid' ],
            'condition' => [ 'show_icon' => 'yes' ],
        ]);
        $this->add_control( 'icon_align', [
            'label' => 'Pozíció',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'left' => 'Szöveg előtt', 'right' => 'Szöveg után' ],
            'default' => 'left',
            'condition' => [ 'show_icon' => 'yes', 'show_text' => 'yes' ],
        ]);
        $this->add_control( 'icon_indent', [
            'label' => 'Távolság a szövegtől',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'max' => 50 ] ],
            'default' => [ 'size' => 8, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .msdl-btn-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .msdl-btn-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'show_icon' => 'yes', 'show_text' => 'yes' ],
        ]);
        $this->add_responsive_control( 'icon_size', [
            'label' => 'Ikon Mérete',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => 5, 'max' => 100 ] ],
            'default' => [ 'size' => 14, 'unit' => 'px' ], 
            'selectors' => [
                '{{WRAPPER}} .msdl-btn-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .msdl-btn-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'show_icon' => 'yes' ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_alignment', [ 
            'label' => 'Igazítás', 
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT 
        ]);
        $this->add_responsive_control( 'align', [
            'label' => 'Gomb Igazítása',
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => 'Balra', 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => 'Középre', 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => 'Jobbra', 'icon' => 'eicon-text-align-right' ],
                'justify'=> [ 'title' => 'Sorkizárt', 'icon' => 'eicon-text-align-justify' ],
            ],
            'selectors' => [ '{{WRAPPER}} .msdl-btn-wrapper' => 'text-align: {{VALUE}};' ],
        ]);
        $this->end_controls_section();

        // --- STÍLUS FÜL ---

        $this->start_controls_section( 'style_template_section', [ 
            'label' => 'Dizájn Sablonok', 
            'tab' => \Elementor\Controls_Manager::TAB_STYLE 
        ]);
        $this->add_control( 'button_template', [
            'label' => 'Sablon Választása',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'tpl-solid'   => 'Sötét Corporate',
                'tpl-pill'    => 'Modern Lekerekített',
                'tpl-outline' => 'Elegáns Körvonalas',
                'custom'      => 'Egyéni (Custom)',
            ],
            'default' => 'tpl-solid',
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'style_colors_section', [ 
            'label' => 'Színek (Szöveg és Ikon)', 
            'tab' => \Elementor\Controls_Manager::TAB_STYLE 
        ]);
        
        $this->start_controls_tabs( 'tabs_text_icon_style' );
        
        $this->start_controls_tab( 'tab_ti_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'text_color', [
            'label' => 'Szöveg Színe',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff', 
            'selectors' => [ '{{WRAPPER}} .msdl-btn-text' => 'color: {{VALUE}};' ],
            'condition' => [ 'show_text' => 'yes' ],
        ]);
        $this->add_control( 'icon_color', [
            'label' => 'Ikon Színe',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff', 
            'selectors' => [ 
                '{{WRAPPER}} .msdl-btn-icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .msdl-btn-icon svg' => 'fill: {{VALUE}};' 
            ],
            'condition' => [ 'show_icon' => 'yes' ],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_ti_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'hover_text_color', [
            'label' => 'Szöveg Színe',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff', 
            'selectors' => [ '{{WRAPPER}} .msdl-btn:hover .msdl-btn-text' => 'color: {{VALUE}};' ],
            'condition' => [ 'show_text' => 'yes' ],
        ]);
        $this->add_control( 'hover_icon_color', [
            'label' => 'Ikon Színe',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff', 
            'selectors' => [ 
                '{{WRAPPER}} .msdl-btn:hover .msdl-btn-icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .msdl-btn:hover .msdl-btn-icon svg' => 'fill: {{VALUE}};' 
            ],
            'condition' => [ 'show_icon' => 'yes' ],
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'label' => 'Tipográfia',
            'selector' => '{{WRAPPER}} .msdl-btn-text',
            'separator' => 'before',
            'condition' => [ 'show_text' => 'yes' ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'style_button_section', [ 
            'label' => 'Gomb Háttere és Animációja', 
            'tab' => \Elementor\Controls_Manager::TAB_STYLE 
        ]);

        $this->start_controls_tabs( 'tabs_button_style' );
        
        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'bg_color', [
            'label' => 'Háttérszín',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#242943', 
            'selectors' => [ '{{WRAPPER}} .msdl-btn' => 'background-color: {{VALUE}};' ],
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'hover_bg_color', [
            'label' => 'Háttérszín',
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#50ADC9', 
            'selectors' => [ '{{WRAPPER}} .msdl-btn:hover' => 'background-color: {{VALUE}};' ],
        ]);
        
        $this->add_control( 'hover_animation', [
            'label' => 'Hover Animáció',
            'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
            'separator' => 'before',
        ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name' => 'border',
            'selector' => '{{WRAPPER}} .msdl-btn',
            'separator' => 'before',
        ]);

        $this->add_responsive_control( 'border_radius', [
            'label' => 'Lekerekítés',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'default' => [ 'top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'unit' => 'px', 'isLinked' => true ],
            'selectors' => [ '{{WRAPPER}} .msdl-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->add_responsive_control( 'padding', [
            'label' => 'Belső margó (Padding)',
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default' => [ 'top' => '10', 'right' => '20', 'bottom' => '10', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
            'selectors' => [ '{{WRAPPER}} .msdl-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $file_id = intval( $settings['file_id'] );
        $download_url = $file_id > 0 ? site_url( '/?msdl_download=' . $file_id ) : '#';

        $icon_html = '';
        if ( $settings['show_icon'] === 'yes' && ! empty( $settings['button_icon']['value'] ) ) {
            $align_class = ($settings['show_text'] === 'yes' && $settings['icon_align'] === 'right') ? 'msdl-btn-icon-right' : 'msdl-btn-icon-left';
            
            $rendered_icon = '';
            if ( class_exists( '\Elementor\Icons_Manager' ) ) {
                ob_start();
                \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
                $rendered_icon = ob_get_clean();
            }
            
            if ( empty( $rendered_icon ) ) {
                $rendered_icon = sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $settings['button_icon']['value'] ) );
            }
            
            $icon_html = sprintf( '<span class="msdl-btn-icon %s" style="display:inline-flex; align-items:center; justify-content:center; line-height:1; transition:all 0.3s ease;">%s</span>', $align_class, $rendered_icon );
        }

        $text_html = '';
        if ( $settings['show_text'] === 'yes' && ! empty( $settings['button_text'] ) ) {
            $text_html = sprintf( '<span class="msdl-btn-text" style="display:inline-block; transition:all 0.3s ease;">%s</span>', esc_html( $settings['button_text'] ) );
        }

        $content = ( $settings['icon_align'] === 'right' ) ? ( $text_html . $icon_html ) : ( $icon_html . $text_html );
        $hover_animation_class = ! empty( $settings['hover_animation'] ) ? ' elementor-animation-' . $settings['hover_animation'] : '';

        ?>
        <style>
            .msdl-btn-wrapper .msdl-btn { 
                text-decoration: none !important; 
                font-family: inherit;
                font-weight: 600;
                box-sizing: border-box;
            }
            .msdl-btn-wrapper .msdl-btn-icon svg { fill: currentColor; }
            .msdl-btn-wrapper .msdl-btn-icon i { font-weight: 900; }
        </style>
        <?php

        echo '<div class="msdl-btn-wrapper">';
        echo sprintf(
            '<a href="%s" class="msdl-btn%s" style="display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease;">%s</a>',
            esc_url( $download_url ),
            $hover_animation_class,
            $content
        );
        echo '</div>';
    }
}