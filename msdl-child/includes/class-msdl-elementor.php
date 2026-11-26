<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Child_Elementor {

    public function init() {
        if ( ! did_action( 'elementor/loaded' ) ) return;

        add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        add_action( 'elementor/controls/controls_registered', [ $this, 'register_custom_controls' ] );
        add_action( 'wp_ajax_msdl_get_picker_items', [ $this, 'ajax_get_picker_items' ] );
        add_action( 'wp_ajax_msdl_get_single_item', [ $this, 'ajax_get_single_item' ] ); 
        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
        
        add_action( 'wp_ajax_msdl_frontend_get_folder', [ $this, 'ajax_frontend_get_folder' ] );
        add_action( 'wp_ajax_nopriv_msdl_frontend_get_folder', [ $this, 'ajax_frontend_get_folder' ] );
        add_action( 'wp_ajax_msdl_frontend_get_file', [ $this, 'ajax_frontend_get_file' ] );
        add_action( 'wp_ajax_nopriv_msdl_frontend_get_file', [ $this, 'ajax_frontend_get_file' ] );
    }

    public function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category( 'msdl-widgets', [ 'title' => 'Dokumentumtár (MSDL)', 'icon' => 'fa fa-folder' ] );
    }

    public function register_widgets( $widgets_manager ) {
        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-button.php';
        $widgets_manager->register( new MSDL_Widget_Button() );

        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-file-card.php';
        $widgets_manager->register( new MSDL_Widget_File_Card() );

        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-folder-view.php';
        $widgets_manager->register( new MSDL_Widget_Folder_View() );

        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-file-manager.php';
        $widgets_manager->register( new MSDL_Widget_File_Manager() );
    }

    public function enqueue_frontend_assets() {}

    /**
     * Rendezési beállítások közös whitelistje.
     * Miért whitelist: az értékek a publikus AJAX-ból is érkeznek, SQL-be nem kerülhet nyers input.
     */
    public static function get_orderby_options() {
        return [
            'name' => 'Név (megjelenített cím szerint, A-Z)',
            'date' => 'Módosítás dátuma',
            'size' => 'Méret',
            'sync' => 'Szinkronizálási sorrend',
            'custom' => 'Egyedi sorrend (Dokumentumtár adminban állítható)',
        ];
    }

    /**
     * ORDER BY szakasz összeállítása whitelistelt értékekből.
     * A névrendezés a megjelenített címre (custom_title, ennek hiányában name) megy, hogy a
     * látogató által látott sorrend egyezzen a rendezéssel.
     */
    public static function build_order_sql( $orderby = 'name', $order = 'ASC', $folders_first = true ) {
        $columns = [
            'name' => "COALESCE(NULLIF(custom_title, ''), name)",
            'date' => 'last_modified',
            'size' => 'size',
            'sync' => 'id',
        ];
        $column    = isset( $columns[ $orderby ] ) ? $columns[ $orderby ] : $columns['name'];
        $direction = ( strtoupper( (string) $order ) === 'DESC' ) ? 'DESC' : 'ASC';
        $sql       = 'ORDER BY ';
        if ( $folders_first ) {
            $sql .= "CASE WHEN type='folder' THEN 1 ELSE 2 END ASC, ";
        }
        if ( $orderby === 'custom' ) {
            // Kézzel rendezett elemek elöl (sort_order), a rendezetlenek (NULL) mindig a végén, név szerint.
            return $sql . "(sort_order IS NULL) ASC, sort_order $direction, COALESCE(NULLIF(custom_title, ''), name) ASC, name ASC";
        }
        $sql .= "$column $direction, name ASC";
        return $sql;
    }

    /**
     * Közös rendezés-vezérlők (Elementor widget content szekcióba hívandó).
     */
    public static function add_order_controls( $widget ) {
        $widget->add_control( 'orderby', [
            'label'     => 'Rendezés alapja',
            'type'      => 'select', // = Controls_Manager::SELECT; sztring, mert az Elementor osztályai nincsenek stub-olva a phpstan-ban
            'options'   => self::get_orderby_options(),
            'default'   => 'name',
            'description' => 'Az „Egyedi sorrend” a Dokumentumtár → Fájlkezelő → „Sorrend szerkesztése” gombbal mappánként állítható; a még nem rendezett elemek a végén, név szerint jelennek meg.',
            'separator' => 'before',
        ] );
        $widget->add_control( 'order', [
            'label'   => 'Irány',
            'type'    => 'select', // = Controls_Manager::SELECT; sztring, mert az Elementor osztályai nincsenek stub-olva a phpstan-ban
            'options' => [ 'ASC' => 'Növekvő (A-Z / régebbi elöl)', 'DESC' => 'Csökkenő (Z-A / újabb elöl)' ],
            'default' => 'ASC',
        ] );
        $widget->add_control( 'folders_first', [
            'label'   => 'Mappák elöl',
            'type'    => 'switcher', // = Controls_Manager::SWITCHER (lásd fent)
            'default' => 'yes',
        ] );
    }

    /**
     * Leírás-stílus szekció (szín, tipográfia, margin, padding) egy adott CSS selectorra.
     * A konstansok helyett sztringeket használunk ('color', 'typography', 'dimensions'), mert az
     * Elementor osztályai nincsenek stub-olva a phpstan-ban; az értékek megegyeznek a Controls_Manager konstansokkal.
     */
    public static function add_description_style_section( $widget, $section_id, $label, $selector, $condition = [] ) {
        $section_args = [ 'label' => $label, 'tab' => 'style' ];
        if ( ! empty( $condition ) ) {
            $section_args['condition'] = $condition;
        }
        $widget->start_controls_section( $section_id, $section_args );
        $widget->add_control( $section_id . '_color', [
            'label'     => 'Szín',
            'type'      => 'color',
            'selectors' => [ '{{WRAPPER}} ' . $selector => 'color: {{VALUE}};' ],
        ] );
        $widget->add_group_control( 'typography', [
            'name'     => $section_id . '_typo',
            'selector' => '{{WRAPPER}} ' . $selector,
        ] );
        $widget->add_responsive_control( $section_id . '_margin', [
            'label'      => 'Külső térköz (margin)',
            'type'       => 'dimensions',
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} ' . $selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        $widget->add_responsive_control( $section_id . '_padding', [
            'label'      => 'Belső térköz (padding)',
            'type'       => 'dimensions',
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        $widget->end_controls_section();
    }

    /**
     * Közös "doboz" stílus-szekció: egy CSS selector minden gyakori vizuális tulajdonsága.
     *
     * Opciók ($args):
     *  - parts:      mely csoportok jelenjenek meg: background (szín+színátmenet), border, radius, shadow, margin, padding
     *  - typography: betűcsalád/méret/vastagság/sormagasság/betűköz (reszponzív)
     *  - color:      szövegszín
     *  - icon_size:  [ selector => css-sablon ] reszponzív méret-csúszka ikonokhoz
     *  - condition:  Elementor feltétel a szekcióra
     *
     * Miért sztringek a konstansok helyén ('color', 'dimensions', 'box-shadow'...): az Elementor osztályai
     * nincsenek stub-olva a phpstan-ban; az értékek megegyeznek a Controls_Manager / Group_Control típusnevekkel.
     * A régi (már mentett) vezérlőket nem érinti: az új szekciók csak kiegészítenek, a vezérlő-azonosítók egyediek.
     */
    public static function add_box_style_section( $widget, $id, $label, $selector, $args = [] ) {
        $args = array_merge( [
            'parts'      => [ 'background', 'border', 'radius', 'shadow', 'margin', 'padding' ],
            'typography' => false,
            'color'      => false,
            'icon_size'  => [],
            'condition'  => [],
        ], $args );
        $sel = '{{WRAPPER}} ' . $selector;
        $dim = 'size_units';

        $section = [ 'label' => $label, 'tab' => 'style' ];
        if ( ! empty( $args['condition'] ) ) {
            $section['condition'] = $args['condition'];
        }
        $widget->start_controls_section( $id, $section );

        if ( $args['color'] ) {
            $widget->add_control( $id . '_color', [ 'label' => 'Szövegszín', 'type' => 'color', 'selectors' => [ $sel => 'color: {{VALUE}};' ] ] );
        }
        if ( $args['typography'] ) {
            $widget->add_group_control( 'typography', [ 'name' => $id . '_typo', 'selector' => $sel ] );
        }
        if ( ! empty( $args['icon_size'] ) ) {
            $selectors = [];
            foreach ( $args['icon_size'] as $css_selector => $css_template ) {
                $selectors[ '{{WRAPPER}} ' . $css_selector ] = $css_template;
            }
            $widget->add_responsive_control( $id . '_size', [
                'label'      => 'Ikon mérete',
                'type'       => 'slider',
                'size_units' => [ 'px', 'em' ],
                'range'      => [ 'px' => [ 'min' => 8, 'max' => 120 ], 'em' => [ 'min' => 0.5, 'max' => 8, 'step' => 0.1 ] ],
                'selectors'  => $selectors,
            ] );
        }
        if ( in_array( 'background', $args['parts'], true ) ) {
            $widget->add_group_control( 'background', [ 'name' => $id . '_bg', 'types' => [ 'classic', 'gradient' ], 'selector' => $sel ] );
        }
        if ( in_array( 'border', $args['parts'], true ) ) {
            $widget->add_group_control( 'border', [ 'name' => $id . '_border', 'selector' => $sel ] );
        }
        if ( in_array( 'radius', $args['parts'], true ) ) {
            $widget->add_responsive_control( $id . '_radius', [
                'label' => 'Lekerekítés', 'type' => 'dimensions', $dim => [ 'px', '%', 'em' ],
                'selectors' => [ $sel => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            ] );
        }
        if ( in_array( 'shadow', $args['parts'], true ) ) {
            $widget->add_group_control( 'box-shadow', [ 'name' => $id . '_shadow', 'selector' => $sel ] );
        }
        if ( in_array( 'margin', $args['parts'], true ) ) {
            $widget->add_responsive_control( $id . '_margin', [
                'label' => 'Külső térköz (margin)', 'type' => 'dimensions', $dim => [ 'px', '%', 'em' ],
                'selectors' => [ $sel => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            ] );
        }
        if ( in_array( 'padding', $args['parts'], true ) ) {
            $widget->add_responsive_control( $id . '_padding', [
                'label' => 'Belső térköz (padding)', 'type' => 'dimensions', $dim => [ 'px', '%', 'em' ],
                'selectors' => [ $sel => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            ] );
        }
        $widget->end_controls_section();
    }

    /**
     * Több doboz-szekció egyszerre: [ [ id, label, selector, args ], ... ].
     */
    public static function add_box_style_sections( $widget, $sections ) {
        foreach ( $sections as $s ) {
            self::add_box_style_section( $widget, $s[0], $s[1], $s[2], isset( $s[3] ) ? $s[3] : [] );
        }
    }

    public function register_custom_controls( $controls_manager ) {
        require_once MSDL_CHILD_DIR . 'includes/controls/class-msdl-control-picker.php';
        $controls_manager->register( new MSDL_Control_Picker() );
    }

    public function ajax_get_picker_items() {
        check_ajax_referer( 'msdl_picker_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Nincs jogosultság' );

        global $wpdb;
        $table = $wpdb->prefix . 'msdl_nodes';

        $parent_id = isset($_POST['parent_id']) ? sanitize_text_field($_POST['parent_id']) : 'root';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        $order_sql = "ORDER BY CASE WHEN type='folder' THEN 1 ELSE 2 END ASC, name ASC";
        
        if ( !empty($search) ) {
            $query = $wpdb->prepare( "SELECT * FROM $table WHERE name LIKE %s OR custom_title LIKE %s OR custom_description LIKE %s $order_sql", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%' );
        } else {
            if ( $parent_id === 'root' || $parent_id === '0' ) {
                $query = "SELECT * FROM $table WHERE parent_graph_id IS NULL $order_sql";
            } else {
                $parent_node = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table WHERE id = %d", intval($parent_id) ) );
                if ( $parent_node ) {
                    $query = $wpdb->prepare( "SELECT * FROM $table WHERE parent_graph_id = %s $order_sql", $parent_node->graph_id );
                } else {
                    wp_send_json_error( 'Mappa nem található.' );
                }
            }
        }

        $items = $wpdb->get_results( $query );
        $formatted = [];
        
        if ( $items ) {
            foreach ( $items as $item ) {
                $formatted[] = [
                    'id'                 => $item->id,
                    'type'               => $item->type,
                    'name'               => $item->name,
                    'custom_title'       => $item->custom_title,
                    'custom_description' => $item->custom_description,
                    'roles'              => $item->visibility_roles,
                    'size'               => $this->format_size_for_display( $item->type, $item->size ?? null ),
                    'date'               => (!empty($item->last_modified) && $item->last_modified !== '0000-00-00 00:00:00') ? date('Y.m.d', strtotime($item->last_modified)) : '-',
                ];
            }
        }
        wp_send_json_success( $formatted );
    }

    public function ajax_get_single_item() {
        check_ajax_referer( 'msdl_picker_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Nincs jogosultság' );

        $id_param = isset($_POST['item_id']) ? sanitize_text_field($_POST['item_id']) : '';

        if ( $id_param === '0' || $id_param === 'root' ) {
            wp_send_json_success([
                'name'               => 'Dokumentumtár (Gyökér)',
                'custom_title'       => '',
                'custom_description' => '',
                'roles'              => 'public',
                'size'               => 'Mappa',
                'date'               => '-'
            ]);
        }

        $id = intval( $id_param );
        if ( ! $id ) wp_send_json_error( 'Érvénytelen azonosító' );

        global $wpdb;
        $table = $wpdb->prefix . 'msdl_nodes';
        $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );

        if ( $item ) {
            wp_send_json_success([
                'name'               => $item->name,
                'custom_title'       => $item->custom_title,
                'custom_description' => $item->custom_description,
                'roles'              => $item->visibility_roles,
                'size'               => $this->format_size_for_display( $item->type, $item->size ?? null ),
                'date'               => (!empty($item->last_modified) && $item->last_modified !== '0000-00-00 00:00:00') ? date('Y.m.d', strtotime($item->last_modified)) : '-',
            ]);
        }
        wp_send_json_error( 'Fájl nem található' );
    }

    public function ajax_frontend_get_folder() {
        check_ajax_referer( 'msdl_frontend_nonce', 'nonce' );
        
        $folder_id = isset($_POST['folder_id']) ? sanitize_text_field($_POST['folder_id']) : 'root';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        global $wpdb;
        $table = $wpdb->prefix . 'msdl_nodes';

        $req_orderby       = isset($_POST['orderby']) ? sanitize_key($_POST['orderby']) : 'name';
        $req_order         = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'ASC';
        $req_folders_first = ! isset($_POST['folders_first']) || $_POST['folders_first'] !== 'no';
        $order_sql = self::build_order_sql( $req_orderby, $req_order, $req_folders_first );
        
        if ( !empty($search) ) {
            $items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE name LIKE %s OR custom_title LIKE %s OR custom_description LIKE %s $order_sql", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%' ) );
        } else {
            if ( $folder_id === '0' || $folder_id === 'root' ) {
                $items = $wpdb->get_results( "SELECT * FROM $table WHERE parent_graph_id IS NULL $order_sql" );
            } else {
                $folder = $wpdb->get_row( $wpdb->prepare( "SELECT graph_id FROM $table WHERE id = %d", intval($folder_id) ) );
                if ( $folder ) {
                    $items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE parent_graph_id = %s $order_sql", $folder->graph_id ) );
                } else {
                    wp_send_json_error( 'Mappa nem található.' );
                }
            }
        }

        $filtered = [];
        if ( $items ) {
            foreach ( $items as $item ) {
                if ( $this->frontend_check_access( $item->visibility_roles ) ) {
                    $ext = pathinfo( $item->name, PATHINFO_EXTENSION );
                    $ext = $ext ? strtolower($ext) : 'file';

                    $icon_class = 'fas fa-file';
                    if ( $item->type === 'folder' ) $icon_class = 'fas fa-folder';
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

                    $formatted_size = '-';
                    if ( $item->type === 'folder' ) {
                        $formatted_size = 'Mappa';
                    } else {
                        $bytes = intval($item->size);
                        if ( $bytes >= 1048576 ) {
                            $formatted_size = round($bytes / 1048576, 2) . ' MB';
                        } elseif ( $bytes >= 1024 ) {
                            $formatted_size = round($bytes / 1024, 0) . ' KB';
                        } else {
                            $formatted_size = $bytes . ' B';
                        }
                    }

                    $display_name = $item->name;
                    if ( !empty($item->custom_title) ) {
                        $display_name = $item->custom_title;
                        if ( $item->type === 'file' && $ext !== 'file' ) {
                            if ( !preg_match('/\.'.$ext.'$/i', $display_name) ) {
                                $display_name .= '.' . $ext;
                            }
                        }
                    }

                    $filtered[] = [
                        'id' => $item->id,
                        'type' => $item->type,
                        'name' => $display_name,
                        'icon_html' => $icon_render,
                        'description' => !empty($item->custom_description) ? wp_kses_post($item->custom_description) : '',
                        'size' => $formatted_size,
                        'date' => (!empty($item->last_modified) && $item->last_modified !== '0000-00-00 00:00:00') ? date('Y.m.d.', strtotime($item->last_modified)) : '-'
                    ];
                }
            }
        }

        $breadcrumbs = [];
        if ( empty($search) && $folder_id !== '0' && $folder_id !== 'root' ) {
            $curr = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, custom_title, parent_graph_id FROM $table WHERE id = %d", intval($folder_id) ) );
            if ( $curr ) {
                $bc_name = !empty($curr->custom_title) ? $curr->custom_title : $curr->name;
                $breadcrumbs[] = [ 'id' => $curr->id, 'name' => $bc_name ];
                
                $parent_gid = $curr->parent_graph_id;
                while ( !empty($parent_gid) ) {
                    $parent = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, custom_title, parent_graph_id FROM $table WHERE graph_id = %s", $parent_gid ) );
                    if ( $parent ) {
                        $p_name = !empty($parent->custom_title) ? $parent->custom_title : $parent->name;
                        $breadcrumbs[] = [ 'id' => $parent->id, 'name' => $p_name ];
                        $parent_gid = $parent->parent_graph_id;
                    } else { break; }
                }
            }
        }
        $breadcrumbs[] = [ 'id' => 'root', 'name' => 'Dokumentumtár' ];
        $breadcrumbs = array_reverse($breadcrumbs);

        wp_send_json_success([
            'items' => $filtered,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function ajax_frontend_get_file() {
        check_ajax_referer( 'msdl_frontend_nonce', 'nonce' );
        
        $file_id = intval( $_POST['file_id'] );
        if ( !$file_id ) wp_send_json_error( 'Érvénytelen azonosító.' );

        global $wpdb;
        $table = $wpdb->prefix . 'msdl_nodes';
        
        $file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d AND type = 'file'", $file_id ) );
        
        if ( ! $file || ! $this->frontend_check_access( $file->visibility_roles ) ) {
            wp_send_json_error( 'A fájl nem található, vagy nincs hozzá jogosultságod.' );
        }

        $ext = pathinfo( $file->name, PATHINFO_EXTENSION );
        $ext = $ext ? strtolower($ext) : 'file';

        $icon_class = 'fas fa-file';
        if ( in_array( $ext, ['pdf'] ) ) $icon_class = 'fas fa-file-pdf';
        elseif ( in_array( $ext, ['doc', 'docx'] ) ) $icon_class = 'fas fa-file-word';
        elseif ( in_array( $ext, ['xls', 'xlsx', 'csv'] ) ) $icon_class = 'fas fa-file-excel';
        elseif ( in_array( $ext, ['jpg', 'jpeg', 'png', 'gif'] ) ) $icon_class = 'fas fa-file-image';
        elseif ( in_array( $ext, ['zip', 'rar'] ) ) $icon_class = 'fas fa-file-archive';

        $icon_render = '';
        if ( class_exists( '\Elementor\Icons_Manager' ) ) {
            ob_start(); \Elementor\Icons_Manager::render_icon( [ 'value' => $icon_class, 'library' => 'fa-solid' ], [ 'aria-hidden' => 'true' ] ); $icon_render = ob_get_clean();
        }
        if ( empty( $icon_render ) ) $icon_render = sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $icon_class ) );

        $parent_id = 'root';
        if ( !empty($file->parent_graph_id) ) {
            $parent_node = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table WHERE graph_id = %s", $file->parent_graph_id ) );
            if ( $parent_node ) $parent_id = $parent_node->id;
        }

        $bytes = intval($file->size);
        $formatted_size = '-';
        if ( $bytes >= 1048576 ) {
            $formatted_size = round($bytes / 1048576, 2) . ' MB';
        } elseif ( $bytes >= 1024 ) {
            $formatted_size = round($bytes / 1024, 0) . ' KB';
        } else {
            $formatted_size = $bytes . ' B';
        }

        $display_name = $file->name;
        if ( !empty($file->custom_title) ) {
            $display_name = $file->custom_title;
            if ( $ext !== 'file' ) {
                if ( !preg_match('/\.'.$ext.'$/i', $display_name) ) {
                    $display_name .= '.' . $ext;
                }
            }
        }

        $description = !empty($file->custom_description) ? wp_kses_post($file->custom_description) : '';

        wp_send_json_success([
            'id' => $file->id,
            'name' => $display_name,
            'description' => $description,
            'icon_html' => $icon_render,
            'ext' => strtoupper($ext),
            'parent_id' => $parent_id,
            'size' => $formatted_size,
            'date' => (!empty($file->last_modified) && $file->last_modified !== '0000-00-00 00:00:00') ? date('Y.m.d.', strtotime($file->last_modified)) : '-',
            'download_url' => site_url( '/?msdl_download=' . $file->id )
        ]);
    }

    // --- ÚJ, KÖZPONTI BIZTONSÁGI KAPU ---
    public static function check_item_access( $roles_data ) {
        // 1. GLOBÁLIS GYÖKÉR VÉDELEM ELLENŐRZÉSE
        $root_visibility = get_option( 'msdl_root_visibility', 'public' );
        if ( $root_visibility === 'hidden' ) return false;
        
        if ( $root_visibility !== 'public' && ! empty( $root_visibility ) ) {
            if ( ! is_user_logged_in() ) return false;
            
            $current_user = wp_get_current_user();
            if ( ! in_array( 'administrator', (array) $current_user->roles ) ) {
                if ( $root_visibility !== 'loggedin' ) {
                    $root_allowed_roles = json_decode( $root_visibility, true );
                    if ( ! is_array( $root_allowed_roles ) ) $root_allowed_roles = [ $root_visibility ];
                    
                    $intersect = array_intersect( $root_allowed_roles, (array) $current_user->roles );
                    if ( empty( $intersect ) ) return false; 
                }
            }
        }

        // 2. LOKÁLIS FÁJL/MAPPA SZINTŰ ELLENŐRZÉS
        if ( $roles_data === 'hidden' ) return false; // A rejtett fájlt SOHA senki nem látja a frontenden!
        
        if ( empty( $roles_data ) || $roles_data === 'public' ) return true;
        if ( ! is_user_logged_in() ) return false;
        if ( $roles_data === 'loggedin' ) return true;
        
        $current_user = wp_get_current_user();
        if ( in_array( 'administrator', (array) $current_user->roles ) ) return true;
        
        $allowed_roles = json_decode( $roles_data, true );
        if ( ! is_array( $allowed_roles ) ) $allowed_roles = [ $roles_data ];
        
        $intersect = array_intersect( $allowed_roles, (array) $current_user->roles );
        if ( ! empty( $intersect ) ) return true;

        return false;
    }

    // Visszafelé kompatibilitás a régi AJAX hívásokhoz, ami a fenti kaput használja
    private function frontend_check_access( $roles_data ) {
        return self::check_item_access( $roles_data );
    }

    private function format_size_for_display( $type, $size ) {
        if ( $type !== 'file' || $size === null ) return '-';
        $size_mb = round( $size / 1048576, 2 );
        return $size_mb > 0 ? $size_mb . ' MB' : '< 1 MB';
    }

    public function enqueue_editor_scripts() {
        wp_enqueue_script( 'msdl-editor-js', plugin_dir_url( dirname(__FILE__) ) . 'assets/js/msdl-editor.js', ['jquery'], '1.0', true );
    }
}