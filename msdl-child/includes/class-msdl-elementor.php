<?php
// Közvetlen hozzáférés tiltása
if ( ! defined( 'ABSPATH' ) ) exit;

class MSDL_Child_Elementor {

    public function init() {
        // Ellenőrizzük, hogy az Elementor be van-e kapcsolva
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        // Kategória regisztrálása az Elementor oldalsávjában (Hogy külön blokkban legyenek a mi widgetjeink)
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

        // Widgetek regisztrálása
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        
        // CSS és JS betöltése a widgetekhez (ezt majd a következő lépésekben írjuk meg)
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        
        add_action( 'elementor/controls/controls_registered', [ $this, 'register_custom_controls' ] );
        add_action( 'wp_ajax_msdl_get_picker_items', [ $this, 'ajax_get_picker_items' ] );
        add_action( 'wp_ajax_msdl_get_single_item', [ $this, 'ajax_get_single_item' ] ); 
        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
    }

    public function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'msdl-widgets',
            [
                'title' => 'Dokumentumtár (MSDL)',
                'icon' => 'fa fa-folder',
            ]
        );
    }

    public function register_widgets( $widgets_manager ) {
        // 1. Widget: Gomb
        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-button.php';
        $widgets_manager->register( new MSDL_Widget_Button() );

        // 2. Widget: Fájl Kártya
        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-file-card.php';
        $widgets_manager->register( new MSDL_Widget_File_Card() );

        // 3. Widget: Mappa Lista
        require_once MSDL_CHILD_DIR . 'includes/widgets/class-msdl-widget-folder-view.php';
        $widgets_manager->register( new MSDL_Widget_Folder_View() );
    }

    public function enqueue_frontend_assets() {
        // Itt töltjük majd be a Vanilla JS fájlunkat a mappa navigációhoz
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
        
        // Szigorú szabály: Mappák mindig legelöl, utána ABC sorrend
        $order_sql = "ORDER BY CASE WHEN type='folder' THEN 1 ELSE 2 END ASC, name ASC";
        
        if ( !empty($search) ) {
            $query = $wpdb->prepare( "SELECT * FROM $table WHERE name LIKE %s $order_sql", '%' . $wpdb->esc_like($search) . '%' );
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
                    'id'    => $item->id,
                    'type'  => $item->type,
                    'name'  => $item->name,
                    'roles' => $this->format_roles_for_display( $item->visibility_roles ),
                    'size'  => $this->format_size_for_display( $item->type, $item->size ?? null ),
                    'date'  => (!empty($item->last_modified) && $item->last_modified !== '0000-00-00 00:00:00') ? date('Y.m.d', strtotime($item->last_modified)) : '-',
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
                'name'  => 'Dokumentumtár (Gyökér)',
                'roles' => '-',
                'size'  => 'Mappa',
                'date'  => '-'
            ]);
        }

        $id = intval( $id_param );
        if ( ! $id ) wp_send_json_error( 'Érvénytelen azonosító' );

        global $wpdb;
        $table = $wpdb->prefix . 'msdl_nodes';
        $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );

        if ( $item ) {
            wp_send_json_success([
                'name'  => $item->name,
                'roles' => $this->format_roles_for_display( $item->visibility_roles ),
                'size'  => $this->format_size_for_display( $item->type, $item->size ?? null ),
                'date'  => (!empty($item->last_modified) && $item->last_modified !== '0000-00-00 00:00:00') ? date('Y.m.d', strtotime($item->last_modified)) : '-',
            ]);
        }
        wp_send_json_error( 'Fájl nem található' );
    }

    // --- SEGÉDFÜGGVÉNYEK A FORMÁZÁSHOZ ---

    private function format_size_for_display( $type, $size ) {
        if ( $type !== 'file' || $size === null ) return '-';
        $size_mb = round( $size / 1048576, 2 );
        return $size_mb > 0 ? $size_mb . ' MB' : '< 1 MB';
    }

    private function format_roles_for_display( $roles_raw ) {
        if ( empty( $roles_raw ) || $roles_raw === 'public' ) return 'Mindenki (Nyilvános)';
        if ( $roles_raw === 'loggedin' ) return 'Bejelentkezett felhasználók';
        
        // Ha JSON tömb (pl. ["administrator", "editor"])
        $decoded = json_decode( $roles_raw, true );
        if ( is_array( $decoded ) ) {
            return implode( ', ', array_map( 'ucfirst', $decoded ) );
        }
        return ucfirst( $roles_raw );
    }

    public function enqueue_editor_scripts() {
        wp_enqueue_script( 'msdl-editor-js', plugin_dir_url( dirname(__FILE__) ) . 'assets/js/msdl-editor.js', ['jquery'], '1.0', true );
    }
}