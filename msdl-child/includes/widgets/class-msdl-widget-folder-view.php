<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Widget_Folder_View extends \Elementor\Widget_Base {

    public function get_name() { return 'msdl_folder_view'; }
    public function get_title() { return 'MSDL Mappa Lista'; }
    public function get_icon() { return 'eicon-folder-o'; }
    public function get_categories() { return [ 'msdl-widgets' ]; }

    public function get_style_depends() { return [ 'elementor-icons-fa-solid', 'e-swiper', 'swiper' ]; }
    public function get_script_depends() { return [ 'swiper' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_query', [ 'label' => 'Adatforrás és Rendezés', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'folder_id', [ 'label' => 'Mappa Kiválasztása (Gyökér)', 'type' => 'msdl_picker', 'item_type' => 'folder' ]);
        $this->add_control( 'allow_subfolders', [ 'label' => 'Almappák megjelenítése (Navigáció)', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before' ]);
        $this->add_control( 'orderby', [ 'label' => 'Rendezés alapja', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => [ 'name' => 'Fájlnév (A-Z)', 'date' => 'Módosítás Dátuma' ], 'default' => 'name' ]);
        $this->add_control( 'order', [ 'label' => 'Irány', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => [ 'ASC' => 'Növekvő (A-Z / Régebbi elöl)', 'DESC' => 'Csökkenő (Z-A / Újabb elöl)' ], 'default' => 'ASC' ]);
        $this->add_control( 'items_per_page', [ 'label' => 'Fájlok száma oldalanként', 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 12, 'separator' => 'before' ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_elements', [ 'label' => 'Megjelenítés', 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_icon', [ 'label' => 'Ikon', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_title', [ 'label' => 'Fájlnév / Cím', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_1', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'show_meta_ext', [ 'label' => 'Kiterjesztés', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta_size', [ 'label' => 'Méret', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta_date', [ 'label' => 'Dátum', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'divider_2', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );
        $this->add_control( 'show_button', [ 'label' => 'Gomb', 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'btn_text', [ 'label' => 'Fájl Gomb Szövege', 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Letöltés', 'condition' => [ 'show_button' => 'yes' ] ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_template', [ 'label' => 'Dizájn Sablon', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'folder_template', [ 
            'label' => 'Sablon Választó', 
            'type' => \Elementor\Controls_Manager::SELECT, 
            'options' => [ 
                'tpl-list' => 'Vízszintes Lista', 
                'tpl-grid' => 'Kompakt Rács (Grid)', 
                'tpl-carousel-light' => 'Carousel (Világos Kártyák)', 
                'tpl-carousel-dark' => 'Carousel (Sötét Kártyák)', 
                'custom' => 'Egyéni Haladó (Custom)' 
            ], 
            'default' => 'tpl-grid' 
        ]);
        
        $this->add_control( 'custom_card_base', [
            'label' => 'Kártya Stílus Alap',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'card-list' => 'Letisztult Lista (Vízszintes)', 'card-app'  => 'Modern App Kártya (Dobozos)', 'card-dark' => 'Kiemelt Sötét Kártya' ],
            'default' => 'card-app', 'condition' => [ 'folder_template' => 'custom' ],
        ]);

        $this->add_control( 'layout_style', [ 
            'label' => 'Elrendezés Bázis', 
            'type' => \Elementor\Controls_Manager::SELECT, 
            'options' => [ 'list' => 'Lista (Egymás alatt)', 'grid' => 'Rács (Oszlopos)', 'carousel' => 'Carousel' ], 
            'default' => 'grid', 'condition' => [ 'folder_template' => 'custom' ], 'separator' => 'before' 
        ]);
        
        // JAVÍTÁS: Az oszlopszám szinte mindig látszik, ha nem a sima lista van kiválasztva
        $this->add_responsive_control( 'items_per_row', [ 
            'label' => 'Oszlopok Száma', 
            'type' => \Elementor\Controls_Manager::SELECT, 
            'options' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ], 
            'default' => '3', 
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [ 'name' => 'folder_template', 'operator' => 'in', 'value' => ['tpl-grid', 'tpl-carousel-light', 'tpl-carousel-dark'] ],
                    [ 'name' => 'layout_style', 'operator' => 'in', 'value' => ['grid', 'carousel'] ]
                ]
            ]
        ]);
        $this->end_controls_section();

        // Egyedi stílus felülbírálások a TAB_STYLE alatt
        $this->start_controls_section( 'section_card_style', [ 'label' => 'Kártyák Színezése (Felülbírálás)', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'card_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-item' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .msdl-fv-item' ] );
        $this->add_responsive_control( 'card_border_radius', [ 'label' => 'Lekerekítés', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .msdl-fv-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_text_style', [ 'label' => 'Szövegek és Ikon', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'icon_color', [ 'label' => 'Ikon Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-item:not(.msdl-is-folder) .msdl-fv-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .msdl-fv-item:not(.msdl-is-folder) .msdl-fv-icon svg' => 'fill: {{VALUE}};' ] ] );
        $this->add_control( 'title_color', [ 'label' => 'Cím Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .msdl-fv-title' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .msdl-fv-title' ] );
        $this->add_control( 'meta_color', [ 'label' => 'Meta Színe', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-meta' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'meta_typo', 'selector' => '{{WRAPPER}} .msdl-fv-meta' ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_btn_style', [ 'label' => 'Gomb', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'btn_typo', 'selector' => '{{WRAPPER}} .msdl-fv-btn' ] );
        $this->start_controls_tabs( 'tabs_btn' );
        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => 'Normál' ] );
        $this->add_control( 'btn_text_color', [ 'label' => 'Szöveg', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-btn' => 'color: {{VALUE}};' ] ]);
        $this->add_control( 'btn_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-btn' => 'background-color: {{VALUE}};' ] ]);
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'btn_hover_text_color', [ 'label' => 'Szöveg', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-btn:hover' => 'color: {{VALUE}};' ] ]);
        $this->add_control( 'btn_hover_bg_color', [ 'label' => 'Háttér', 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .msdl-fv-btn:hover' => 'background-color: {{VALUE}};' ] ]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => '{{WRAPPER}} .msdl-fv-btn', 'separator' => 'before' ] );
        $this->add_responsive_control( 'btn_border_radius', [ 'label' => 'Radius', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .msdl-fv-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ]);
        $this->end_controls_section();
    }

    protected function render() {
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        if ( ! $is_editor ) wp_enqueue_style( 'elementor-icons-fa-solid' );

        $settings = $this->get_settings_for_display();
        $uid = $this->get_id();
        $param_name = 'msdl_folder_' . $uid;
        
        $base_folder_id = isset($settings['folder_id']) ? $settings['folder_id'] : '';
        $current_folder_id = $base_folder_id;
        $allow_subfolders = ($settings['allow_subfolders'] === 'yes');
        
        if ( $allow_subfolders && isset($_GET[$param_name]) ) {
            $current_folder_id = intval($_GET[$param_name]);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'msdl_nodes';
        
        if ( ! $is_editor && ! MSDL_Child_Elementor::check_item_access( 'public' ) ) {
            echo '<div style="padding:20px; text-align:center; color:#787c82; border:2px dashed #dcdcde; border-radius:8px; font-weight:500;">Nincs jogosultságod a dokumentumtár megtekintéséhez.</div>';
            return;
        }
        
        if ( ! $is_editor && intval($current_folder_id) > 0 ) {
            $current_folder_node = $wpdb->get_row( $wpdb->prepare( "SELECT visibility_roles FROM $table WHERE id = %d", intval($current_folder_id) ) );
            if ( $current_folder_node && ! MSDL_Child_Elementor::check_item_access( $current_folder_node->visibility_roles ) ) {
                echo '<div style="padding:20px; text-align:center; color:#787c82; border:2px dashed #dcdcde; border-radius:8px; font-weight:500;">Nincs jogosultságod a mappa megtekintéséhez.</div>';
                return;
            }
        }

        $type_sql = $allow_subfolders ? "" : " AND type = 'file'";
        $orderby = $settings['orderby'] === 'date' ? 'last_modified' : 'name';
        $order = $settings['order'] === 'DESC' ? 'DESC' : 'ASC';

        $items = [];
        if ( $current_folder_id === '0' || $current_folder_id === 'root' ) {
            $items = $wpdb->get_results( "SELECT * FROM $table WHERE parent_graph_id IS NULL $type_sql ORDER BY CASE WHEN type='folder' THEN 1 ELSE 2 END ASC, $orderby $order" );
        } elseif ( intval($current_folder_id) > 0 ) {
            $folder = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table WHERE id = %d", intval($current_folder_id) ) );
            if ( $folder ) {
                $items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE parent_graph_id = %s $type_sql ORDER BY CASE WHEN type='folder' THEN 1 ELSE 2 END ASC, $orderby $order", $folder->graph_id ) );
            }
        }

        $filtered_items = [];
        foreach ( $items as $item ) {
            if ( $item->visibility_roles === 'hidden' ) continue;
            if ( $is_editor || MSDL_Child_Elementor::check_item_access( $item->visibility_roles ) ) {
                $filtered_items[] = $item;
            }
        }
        $items = $filtered_items;

        if ( $is_editor && $base_folder_id === '' ) {
            for($i=1; $i<=6; $i++) $items[] = (object)[ 'id' => $i, 'type' => 'file', 'name' => 'Példa_Dokumentum_'.$i.'.pdf', 'size' => 1500000, 'last_modified' => date('Y-m-d H:i:s') ];
        }

        // JAVÍTÁS: A SABLON LOGIKA FELOSZTÁSA
        $template = $settings['folder_template'];
        $cols = $settings['items_per_row'] ?? '3';
        
        if ( $template === 'tpl-list' ) {
            $layout = 'list'; $card_style = 'card-list';
        } elseif ( $template === 'tpl-grid' ) {
            $layout = 'grid'; $card_style = 'card-app';
        } elseif ( $template === 'tpl-carousel-light' ) {
            $layout = 'carousel'; $card_style = 'card-app';
        } elseif ( $template === 'tpl-carousel-dark' ) {
            $layout = 'carousel'; $card_style = 'card-dark';
        } else {
            // Egyedi / Custom
            $layout = $settings['layout_style'];
            $card_style = $settings['custom_card_base'];
        }

        $per_page = intval($settings['items_per_page']);
        if ($per_page < 1) $per_page = 12;

        $folders_array = [];
        $files_array = [];
        foreach ($items as $item) {
            if ($item->type === 'folder') $folders_array[] = $item;
            else $files_array[] = $item;
        }

        $breadcrumbs = [];
        if ( $allow_subfolders ) {
            $temp_id = strval($current_folder_id);
            $stop_id = (empty($base_folder_id) || $base_folder_id === 'root') ? '0' : strval($base_folder_id);

            if ( $temp_id !== '0' && $temp_id !== 'root' && intval($temp_id) > 0 ) {
                $curr = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, custom_title, parent_graph_id FROM $table WHERE id = %d", intval($temp_id) ) );
                if ( $curr ) {
                    $bc_name = !empty($curr->custom_title) ? $curr->custom_title : $curr->name;
                    $breadcrumbs[] = [ 'id' => strval($curr->id), 'name' => $bc_name ];
                    $parent_gid = $curr->parent_graph_id;

                    while ( !empty($parent_gid) && strval($curr->id) !== $stop_id ) {
                        $parent = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, custom_title, parent_graph_id FROM $table WHERE graph_id = %s", $parent_gid ) );
                        if ( $parent ) {
                            if ( strval($parent->id) === $stop_id ) {
                                $p_name = !empty($parent->custom_title) ? $parent->custom_title : $parent->name;
                                $breadcrumbs[] = [ 'id' => strval($parent->id), 'name' => $p_name ];
                                break;
                            }
                            $p_name = !empty($parent->custom_title) ? $parent->custom_title : $parent->name;
                            $breadcrumbs[] = [ 'id' => strval($parent->id), 'name' => $p_name ];
                            $parent_gid = $parent->parent_graph_id;
                        } else { break; }
                    }
                }
            }
            
            $last_crumb = end($breadcrumbs);
            if ( !$last_crumb || $last_crumb['id'] !== $stop_id ) {
                if ( $stop_id === '0' ) {
                    $breadcrumbs[] = [ 'id' => '0', 'name' => 'Dokumentumtár' ];
                } else {
                    $base_node = $wpdb->get_row( $wpdb->prepare( "SELECT name, custom_title FROM $table WHERE id = %d", intval($stop_id) ) );
                    if ( $base_node ) {
                        $p_name = !empty($base_node->custom_title) ? $base_node->custom_title : $base_node->name;
                        $breadcrumbs[] = [ 'id' => strval($stop_id), 'name' => $p_name ];
                    }
                }
            }
            $breadcrumbs = array_reverse($breadcrumbs);
        }

        $grid_template = ($layout === 'grid') ? "grid-template-columns: repeat({$cols}, 1fr);" : "";

        $render_card = function($item, $item_layout, $is_hidden) use ($settings, $param_name, $card_style) {
            $is_folder = ($item->type === 'folder');
            $ext = pathinfo( $item->name, PATHINFO_EXTENSION );
            $ext = $ext ? strtolower($ext) : 'file';
            
            $icon_class = 'fas fa-file';
            if ( $is_folder ) $icon_class = 'fas fa-folder';
            elseif ( in_array( $ext, ['pdf'] ) ) $icon_class = 'fas fa-file-pdf';
            elseif ( in_array( $ext, ['doc', 'docx'] ) ) $icon_class = 'fas fa-file-word';
            elseif ( in_array( $ext, ['xls', 'xlsx', 'csv'] ) ) $icon_class = 'fas fa-file-excel';
            elseif ( in_array( $ext, ['jpg', 'jpeg', 'png', 'gif'] ) ) $icon_class = 'fas fa-file-image';
            elseif ( in_array( $ext, ['zip', 'rar'] ) ) $icon_class = 'fas fa-file-archive';

            $icon_render = '';
            if ( class_exists( '\Elementor\Icons_Manager' ) ) {
                ob_start(); \Elementor\Icons_Manager::render_icon( [ 'value' => $icon_class, 'library' => 'fa-solid' ], [ 'aria-hidden' => 'true' ] ); $icon_render = ob_get_clean();
            }
            if ( empty( $icon_render ) ) $icon_render = sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $icon_class ) );

            $size_str = '-';
            if ( $is_folder ) {
                $size_str = 'Mappa';
            } else {
                $bytes = intval($item->size);
                if ( $bytes >= 1048576 ) {
                    $size_str = round($bytes / 1048576, 2) . ' MB';
                } elseif ( $bytes >= 1024 ) {
                    $size_str = round($bytes / 1024, 0) . ' KB';
                } else {
                    $size_str = $bytes > 0 ? $bytes . ' B' : '-';
                }
            }
            
            $date_str = (!empty($item->last_modified)) ? date('Y.m.d.', strtotime($item->last_modified)) : '-';
            
            $display_name = $item->name;
            if ( !empty($item->custom_title) ) {
                $display_name = $item->custom_title;
                if ( !$is_folder ) {
                    if ( $ext !== 'file' && !preg_match('/\.'.$ext.'$/i', $display_name) ) {
                        $display_name .= '.' . $ext;
                    }
                }
            }

            $display_style = $is_hidden ? 'display:none;' : '';
            // JAVÍTÁS: Itt kapja meg a kártya a konkrét stílusosztályát!
            $item_class = 'msdl-fv-item layout-' . esc_attr($item_layout) . ' style-' . esc_attr($card_style) . ' msdl-page-item';
            
            if ( $is_folder ) {
                $item_class .= ' msdl-is-folder';
                $url = add_query_arg( $param_name, $item->id );
                $btn_text = 'Megnyitás <i class="fas fa-arrow-right" style="margin-left:5px;"></i>';
            } else {
                $url = site_url( '/?msdl_download=' . $item->id );
                $btn_text = $settings['btn_text'];
            }

            ?>
            <div class="<?php echo esc_attr($item_class); ?>" style="<?php echo $display_style; ?>">
                <?php if ( $settings['show_icon'] === 'yes' ) : ?>
                    <div class="msdl-fv-icon">
                        <?php if ( $is_folder ) : ?>
                            <a href="<?php echo esc_url($url); ?>" class="msdl-ajax-link" style="color:inherit; text-decoration:none; display:inherit; align-items:inherit; justify-content:inherit;"><?php echo $icon_render; ?></a>
                        <?php else : ?>
                            <?php echo $icon_render; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="msdl-fv-content">
                    <?php if ( $settings['show_title'] === 'yes' ) : ?>
                        <h4 class="msdl-fv-title">
                            <?php if ( $is_folder ) : ?>
                                <a href="<?php echo esc_url($url); ?>" class="msdl-ajax-link" style="color:inherit; text-decoration:none;"><?php echo esc_html($display_name); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($display_name); ?>
                            <?php endif; ?>
                        </h4>
                    <?php endif; ?>
                    
                    <div class="msdl-fv-meta">
                        <?php if ( $settings['show_meta_ext'] === 'yes' && !$is_folder ) : ?><span><?php echo esc_html( strtoupper($ext) ); ?></span><?php endif; ?>
                        <?php if ( $settings['show_meta_size'] === 'yes' ) : ?><span><?php echo $size_str; ?></span><?php endif; ?>
                        <?php if ( $settings['show_meta_date'] === 'yes' ) : ?><span><?php echo $date_str; ?></span><?php endif; ?>
                    </div>
                </div>
                
                <?php if ( $settings['show_button'] === 'yes' ) : ?>
                    <div class="msdl-fv-action"><a href="<?php echo esc_url($url); ?>" class="msdl-fv-btn <?php echo $is_folder ? 'msdl-ajax-link' : ''; ?>"><?php echo $btn_text; ?></a></div>
                <?php endif; ?>
            </div>
            <?php
        };

        ?>
        <style>
            #msdl-fv-<?php echo $uid; ?>-container { position: relative; width: 100%; min-height: 100px; font-family: inherit;}
            #msdl-fv-<?php echo $uid; ?> { display: <?php echo $layout === 'grid' ? 'grid' : 'flex'; ?>; flex-direction: column; gap: 20px; <?php echo $grid_template; ?> }

            .msdl-fv-breadcrumbs { margin-bottom: 25px; font-size: 15px; font-weight: 500; display:flex; flex-wrap:wrap; align-items:center; gap:8px;}
            .msdl-fv-breadcrumbs a { color: #50ADC9; text-decoration: none; padding: 6px 12px; border-radius: 6px; background: rgba(80,173,201,0.08); transition: background 0.2s;}
            .msdl-fv-breadcrumbs a:hover { background: rgba(80,173,201,0.15); }
            .msdl-fv-breadcrumbs .msdl-bc-sep { color: #a0a6b5; font-size: 12px;}
            .msdl-fv-breadcrumbs span.msdl-bc-current { color: #242943; font-weight: 700; padding: 6px 12px;}

            .swiper-wrapper { display: flex; align-items: stretch; box-sizing: content-box; }
            .swiper-button-next, .swiper-button-prev { background: #fff; width: 44px; height: 44px; border-radius: 50%; box-shadow: 0 4px 15px rgba(0,0,0,0.1); color: #242943; }
            .swiper-button-next:after, .swiper-button-prev:after { font-size: 16px !important; font-weight: bold; }

            .msdl-fv-item { display: flex; gap: 15px; box-sizing: border-box; transition: all 0.3s ease; position: relative; overflow: hidden; height: 100%;}
            
            /* --- ÚJ: BEÉPÍTETT PRÉMIUM STÍLUSOK --- */
            
            /* 1. App Card (Modern dobozos) */
            .msdl-fv-item.style-card-app {
                background: #ffffff;
                border: 1px solid #eaeaea;
                border-radius: 12px;
                padding: 24px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            }
            .msdl-fv-item.style-card-app:hover {
                box-shadow: 0 8px 30px rgba(0,0,0,0.08);
                transform: translateY(-3px);
                border-color: #d0d0d0;
            }

            /* 2. Dark Card (Sötét, letisztult) */
            .msdl-fv-item.style-card-dark {
                background: #1d2327;
                border: 1px solid #2c3338;
                border-radius: 12px;
                padding: 24px;
                color: #f0f0f1;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            .msdl-fv-item.style-card-dark:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 30px rgba(0,0,0,0.2);
                border-color: #4a545c;
            }
            .msdl-fv-item.style-card-dark .msdl-fv-title { color: #ffffff; }
            .msdl-fv-item.style-card-dark .msdl-fv-title a { color: #ffffff; }
            .msdl-fv-item.style-card-dark .msdl-fv-meta { color: #a7aaad; }
            
            /* 3. Letisztult Lista (Vízszintes) */
            .msdl-fv-item.style-card-list {
                background: transparent;
                border-bottom: 1px solid #f0f2f5;
                padding: 15px 10px;
                border-radius: 0;
            }
            .msdl-fv-item.style-card-list:last-child { border-bottom: none; }
            .msdl-fv-item.style-card-list:hover { background: #f8f9fa; transform: translateX(4px); }

            /* --- ALAP LOGIKAI ELRENDEZÉSEK --- */
            .msdl-fv-item.layout-list { flex-direction: row; align-items: center; }
            .msdl-fv-item.layout-grid, .msdl-fv-item.layout-carousel { flex-direction: column; align-items: center; text-align: center; }
            
            .msdl-fv-item.layout-list .msdl-fv-content { flex-grow: 1; }
            .msdl-fv-item:not(.layout-list) .msdl-fv-content { flex-grow: 1; display: flex; flex-direction: column; width: 100%; }
            
            .msdl-fv-item.layout-list .msdl-fv-action { flex-shrink: 0; margin-left: auto; }
            .msdl-fv-item:not(.layout-list) .msdl-fv-action { width: 100%; margin-top: auto; padding-top: 15px; }
            .msdl-fv-item:not(.layout-list) .msdl-fv-btn { display: block; width: 100%; text-align: center; box-sizing: border-box; }

            /* Ikonok és Címek */
            .msdl-is-folder .msdl-fv-icon i { color: #f5c342; }
            .msdl-is-folder .msdl-fv-icon svg { fill: #f5c342; }
            .msdl-fv-item:not(.msdl-is-folder) .msdl-fv-icon i { color: #50ADC9; }
            .msdl-fv-item:not(.msdl-is-folder) .msdl-fv-icon svg { fill: #50ADC9; }

            .msdl-fv-icon { flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; line-height: 1; font-size: 32px;}
            .msdl-fv-icon svg { width: 32px; height: 32px; fill: currentColor; }
            
            .msdl-fv-content { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
            .msdl-fv-title { font-size: 16px; font-weight: 700; margin: 0; word-wrap: break-word; line-height: 1.3; }
            .msdl-fv-title a { transition: color 0.2s; }
            .msdl-fv-title a:hover { opacity: 0.8; }
            
            .msdl-fv-meta { font-size: 13px; font-weight: 500; display: flex; flex-wrap: wrap; gap: 4px; color: #787c82;}
            .msdl-fv-item.layout-list .msdl-fv-meta { justify-content: flex-start; }
            .msdl-fv-item:not(.layout-list) .msdl-fv-meta { justify-content: center; }
            
            .msdl-fv-meta span { display: inline-flex; align-items: center; }
            .msdl-fv-meta span:not(:last-child)::after { content: '•'; margin-left: 6px; opacity: 0.5; }
            
            /* Beépített letisztult gomb dizájn */
            .msdl-fv-btn { font-size: 14px; font-weight: 600; text-decoration: none !important; transition: all 0.3s ease; padding: 10px 20px; border-radius: 8px; color: #50ADC9; background: rgba(80,173,201,0.1); border: 1px solid transparent;}
            .msdl-fv-btn:hover { background: #50ADC9; color: #ffffff; }

            .msdl-pagination { display: flex; justify-content: center; gap: 8px; margin-top: 40px; }
            .msdl-pag-btn { padding: 8px 15px; border: 1px solid #dcdcde; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; background: #fff; color:#242943;}
            .msdl-pag-btn:hover { background: #f6f7f7; }
            .msdl-pag-btn.active { background: #50ADC9; color: #fff; border-color: #50ADC9;}
            .msdl-pag-btn[disabled] { opacity: 0.5; cursor: not-allowed; }

            @media (max-width: 767px) {
                #msdl-fv-<?php echo $uid; ?> { grid-template-columns: 1fr !important; }
                .msdl-fv-item { flex-direction: column !important; text-align: center !important; gap: 10px; }
                .msdl-fv-item .msdl-fv-icon { margin: 0 auto; }
                .msdl-fv-item .msdl-fv-meta { justify-content: center; }
                .msdl-fv-item .msdl-fv-action { width: 100% !important; margin-left: 0 !important; }
                .msdl-fv-btn { display: block; width: 100%; text-align: center; }
            }
        </style>

        <div id="msdl-fv-<?php echo $uid; ?>-container" class="msdl-folder-view-wrapper">
            
            <?php
            if ( $allow_subfolders && !empty($breadcrumbs) && count($breadcrumbs) > 1 ) {
                echo '<div class="msdl-fv-breadcrumbs">';
                foreach ($breadcrumbs as $i => $crumb) {
                    if ( $i == count($breadcrumbs) - 1 ) {
                        echo '<span class="msdl-bc-current">' . esc_html($crumb['name']) . '</span>';
                    } else {
                        $b_url = ($crumb['id'] === '0') ? remove_query_arg($param_name) : add_query_arg($param_name, $crumb['id']);
                        echo '<a href="' . esc_url($b_url) . '" class="msdl-ajax-link">' . esc_html($crumb['name']) . '</a> <span class="msdl-bc-sep">/</span> ';
                    }
                }
                echo '</div>';
            }

            if ( empty($items) ) {
                echo '<div style="padding:30px; text-align:center; color:#787c82; border:2px dashed #dcdcde; border-radius:8px; font-weight:500;">Ebben a mappában nincsenek a számodra elérhető fájlok.</div>';
            }

            if ( $layout === 'carousel' ) {
                if ( !empty($folders_array) ) {
                    echo '<div class="msdl-fv-carousel-folders" style="display:flex; flex-direction:column; gap:15px; margin-bottom:25px;">';
                    foreach ($folders_array as $folder) {
                        $render_card($folder, 'list', false);
                    }
                    echo '</div>';
                }

                if ( !empty($files_array) ) {
                    echo '<div class="swiper swiper-container" id="swiper-'.$uid.'" style="padding: 15px 5px 40px 5px; overflow: hidden;"><div class="swiper-wrapper">';
                    foreach ($files_array as $file) {
                        echo '<div class="swiper-slide" style="height:auto;">';
                        $render_card($file, 'grid', false);
                        echo '</div>';
                    }
                    echo '</div><div class="swiper-button-next"></div><div class="swiper-button-prev"></div></div>';
                }
            } 
            else {
                echo '<div id="msdl-fv-'.$uid.'">';
                
                foreach ($folders_array as $folder) {
                    $render_card($folder, $layout, false);
                }
                
                $file_index = 0;
                foreach ($files_array as $file) {
                    $is_hidden = ($file_index >= $per_page);
                    $render_card($file, $layout, $is_hidden);
                    $file_index++;
                }
                
                echo '</div>';
            }
            ?>
            
            <?php if ( $layout !== 'carousel' && count($files_array) > $per_page ) : ?>
                <div class="msdl-pagination" id="pag-<?php echo $uid; ?>">
                    <button class="msdl-pag-btn msdl-pag-prev" disabled>&laquo; Előző</button>
                    <div class="msdl-pag-numbers" style="display:flex; gap:5px;"></div>
                    <button class="msdl-pag-btn msdl-pag-next">Következő &raquo;</button>
                </div>
            <?php endif; ?>

        </div>

        <script>
        jQuery(document).ready(function($) {
            var layout = "<?php echo $layout; ?>";
            var uid = "<?php echo $uid; ?>";
            var cols = <?php echo intval($cols); ?>;
            var perPage = <?php echo $per_page; ?>;

            function initMSDLFolderView() {
                var $container = $('#msdl-fv-' + uid);
                
                if ( layout === 'carousel' && $('#swiper-' + uid).length > 0 ) {
                    if (typeof Swiper !== 'undefined') {
                        new Swiper('#swiper-' + uid, {
                            slidesPerView: 1, spaceBetween: 20, loop: false,
                            navigation: { nextEl: '#swiper-' + uid + ' .swiper-button-next', prevEl: '#swiper-' + uid + ' .swiper-button-prev' },
                            breakpoints: { 768: { slidesPerView: cols > 1 ? 2 : 1 }, 1024: { slidesPerView: cols } }
                        });
                    }
                } 
                else if ( layout !== 'carousel' ) {
                    var $files = $container.find('.msdl-page-item:not(.msdl-is-folder)');
                    var totalFiles = $files.length;
                    
                    if (totalFiles > perPage) {
                        var totalPages = Math.ceil(totalFiles / perPage);
                        var currentPage = 1;
                        var $pagContainer = $('#pag-' + uid);
                        var $numContainer = $pagContainer.find('.msdl-pag-numbers');

                        $numContainer.empty();
                        for (var i = 1; i <= totalPages; i++) {
                            $numContainer.append('<button class="msdl-pag-btn ' + (i === 1 ? 'active' : '') + '" data-page="'+i+'">'+i+'</button>');
                        }

                        function showPage(page) {
                            currentPage = page;
                            var start = (page - 1) * perPage;
                            var end = start + perPage;
                            $files.hide().slice(start, end).fadeIn(300);
                            
                            $numContainer.find('button').removeClass('active');
                            $numContainer.find('button[data-page="'+page+'"]').addClass('active');
                            
                            $pagContainer.find('.msdl-pag-prev').prop('disabled', page === 1);
                            $pagContainer.find('.msdl-pag-next').prop('disabled', page === totalPages);
                        }

                        $pagContainer.off('click').on('click', '.msdl-pag-numbers button', function() { showPage($(this).data('page')); });
                        $pagContainer.on('click', '.msdl-pag-prev', function() { if (currentPage > 1) showPage(currentPage - 1); });
                        $pagContainer.on('click', '.msdl-pag-next', function() { if (currentPage < totalPages) showPage(currentPage + 1); });
                    }
                }
            }

            initMSDLFolderView();

            $('#msdl-fv-' + uid + '-container').on('click', '.msdl-ajax-link', function(e) {
                var url = $(this).attr('href');
                if (!url || url === '#') return;
                
                e.preventDefault();
                var $wrapper = $('#msdl-fv-' + uid + '-container');
                
                if (!$('style#msdl-ajax-anim').length) {
                    $('head').append('<style id="msdl-ajax-anim">@keyframes msdl-spin-loader { 100% { transform:rotate(360deg); } }</style>');
                }
                
                $wrapper.append('<div class="msdl-ajax-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); display:flex; align-items:center; justify-content:center; z-index:10; border-radius:12px; backdrop-filter:blur(2px);"><div style="width:40px;height:40px;border:3px solid #ccc;border-top-color:#2271b1;border-radius:50%;animation:msdl-spin-loader 1s linear infinite;"></div></div>');
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        try {
                            var doc = new DOMParser().parseFromString(response, 'text/html');
                            var newHtml = $(doc).find('#msdl-fv-' + uid + '-container').html();
                            
                            if (newHtml) {
                                $wrapper.html(newHtml);
                                window.history.pushState({path: url, type: 'msdl-folder-view'}, '', url);
                                initMSDLFolderView();
                                
                                var wrapperTop = $wrapper.offset().top;
                                if ($(window).scrollTop() > wrapperTop) {
                                    $('html, body').animate({ scrollTop: wrapperTop - 100 }, 300);
                                }
                            } else {
                                window.location.href = url;
                            }
                        } catch (err) {
                            window.location.href = url;
                        }
                    },
                    error: function() {
                        window.location.href = url;
                    }
                });
            });

            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.type === 'msdl-folder-view') {
                    var $wrapper = $('#msdl-fv-' + uid + '-container');
                    $wrapper.append('<div class="msdl-ajax-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); display:flex; align-items:center; justify-content:center; z-index:10; border-radius:12px; backdrop-filter:blur(2px);"><div style="width:40px;height:40px;border:3px solid #ccc;border-top-color:#2271b1;border-radius:50%;animation:msdl-spin-loader 1s linear infinite;"></div></div>');
                    $.get(e.state.path, function(html) {
                        try {
                            var doc = new DOMParser().parseFromString(html, 'text/html');
                            var newHtml = $(doc).find('#msdl-fv-' + uid + '-container').html();
                            if (newHtml) {
                                $wrapper.html(newHtml);
                                initMSDLFolderView();
                            } else {
                                window.location.reload();
                            }
                        } catch (err) {
                            window.location.reload();
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }
}