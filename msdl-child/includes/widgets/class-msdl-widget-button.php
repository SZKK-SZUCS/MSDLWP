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
            'selectors' => [ 
                '{{WRAPPER}} .msdl-btn-wrapper' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .msdl-btn-helper'  => 'text-align: {{VALUE}};'
            ],
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
        $download_url = $file_id > 0 ? rest_url( 'msdl-child/v1/download-file?id=' . $file_id ) : '#';

        $has_access = true;
        $needs_login = false;
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

        if ( $file_id > 0 && !$is_editor ) {
            global $wpdb;
            $table = $wpdb->prefix . 'msdl_nodes';
            $node = $wpdb->get_row( $wpdb->prepare( "SELECT visibility_roles FROM $table WHERE id = %d AND type = 'file'", $file_id ) );

            // 1. Gyökérmappa szintű ellenőrzés
            $root_vis = get_option( 'msdl_root_visibility', 'public' );
            if ( $root_vis === 'hidden' ) {
                $has_access = false;
            } elseif ( $root_vis !== 'public' && !empty($root_vis) ) {
                if ( ! is_user_logged_in() ) {
                    $has_access = false;
                    $needs_login = true;
                } else {
                    $curr_user = wp_get_current_user();
                    if ( ! in_array( 'administrator', (array)$curr_user->roles ) ) {
                        if ( $root_vis !== 'loggedin' ) {
                            $r_roles = json_decode( $root_vis, true );
                            if ( !is_array($r_roles) ) $r_roles = [$root_vis];
                            if ( empty(array_intersect($r_roles, (array)$curr_user->roles)) ) {
                                $has_access = false;
                            }
                        }
                    }
                }
            }

            // 2. Fájl szintű ellenőrzés
            if ( $has_access && $node ) {
                $f_vis = $node->visibility_roles;
                if ( $f_vis === 'hidden' ) {
                    $has_access = false;
                } elseif ( !empty($f_vis) && $f_vis !== 'public' ) {
                    if ( ! is_user_logged_in() ) {
                        $has_access = false;
                        $needs_login = true;
                    } else {
                        $curr_user = wp_get_current_user();
                        if ( ! in_array( 'administrator', (array)$curr_user->roles ) ) {
                            if ( $f_vis !== 'loggedin' ) {
                                $f_roles = json_decode( $f_vis, true );
                                if ( !is_array($f_roles) ) $f_roles = [$f_vis];
                                if ( empty(array_intersect($f_roles, (array)$curr_user->roles)) ) {
                                    $has_access = false;
                                }
                            }
                        }
                    }
                }
            } elseif ( !$node ) {
                $has_access = false;
            }
        }

        // Felülírások, ha nincs hozzáférés
        $btn_text = $settings['button_text'];
        $helper_text = '';
        $extra_style = '';
        $is_protected = false;

        if ( ! $has_access && $file_id > 0 && !$is_editor ) {
            $is_protected = true;
            $btn_text = 'Védett*';
            $download_url = '#';
            $extra_style = 'opacity: 0.55; pointer-events: none; cursor: not-allowed; filter: grayscale(100%);';
            $helper_text = $needs_login ? '*Kérjük, jelentkezzen be a fájl eléréséhez.' : '*Önnek nincs jogosultsága a fájl letöltéséhez.';
        } elseif ( $file_id === 0 && !$is_editor ) {
            $btn_text = 'Nincs fájl';
            $download_url = '#';
            $extra_style = 'opacity: 0.5; pointer-events: none;';
        }

        $icon_html = '';
        if ( $settings['show_icon'] === 'yes' ) {
            $align_class = ($settings['show_text'] === 'yes' && $settings['icon_align'] === 'right') ? 'msdl-btn-icon-right' : 'msdl-btn-icon-left';
            
            if ( $is_protected ) {
                $rendered_icon = '<i class="fas fa-lock" aria-hidden="true"></i>';
            } else {
                $rendered_icon = '';
                if ( !empty( $settings['button_icon']['value'] ) ) {
                    if ( class_exists( '\Elementor\Icons_Manager' ) ) {
                        ob_start();
                        \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
                        $rendered_icon = ob_get_clean();
                    }
                    if ( empty( $rendered_icon ) ) {
                        $rendered_icon = sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $settings['button_icon']['value'] ) );
                    }
                }
            }
            
            if ( !empty($rendered_icon) ) {
                $icon_html = sprintf( '<span class="msdl-btn-icon %s" style="display:inline-flex; align-items:center; justify-content:center; line-height:1; transition:all 0.3s ease;">%s</span>', $align_class, $rendered_icon );
            }
        }

        $text_html = '';
        if ( $settings['show_text'] === 'yes' ) {
            $text_html = sprintf( '<span class="msdl-btn-text" style="display:inline-block; transition:all 0.3s ease;">%s</span>', esc_html( $btn_text ) );
        }

        $content = ( $settings['icon_align'] === 'right' ) ? ( $text_html . $icon_html ) : ( $icon_html . $text_html );
        $hover_animation_class = ( ! empty( $settings['hover_animation'] ) && !$is_protected ) ? ' elementor-animation-' . $settings['hover_animation'] : '';

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
            .msdl-btn-helper { display: block; font-size: 13px; margin-top: 8px; opacity: 0.7; font-weight: 500; font-style: italic; }
        </style>
        <?php

        echo '<div class="msdl-btn-wrapper">';
        echo sprintf(
            '<a href="%s" class="msdl-btn%s" style="display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; %s">%s</a>',
            esc_url( $download_url ),
            $hover_animation_class,
            $extra_style,
            $content
        );
        if ( !empty($helper_text) ) {
            echo '<span class="msdl-btn-helper">' . esc_html($helper_text) . '</span>';
        }
        echo '</div>';
    }
}