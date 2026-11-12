<?php
/**
 * Plugin Name: Product CSV Importer
 * Description: Імпорт продуктів з CSV у post_type=product + таксономії + Carbon Fields.
 * Author: Bogdan Project
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Product_CSV_Importer {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    public function register_menu() {
        // Сторінка в меню продуктів: Товари → Import from CSV
        add_submenu_page(
            'edit.php?post_type=product',
            'Import from CSV',
            'Import from CSV',
            'manage_options',
            'product-csv-import',
            [ $this, 'render_page' ]
        );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Not allowed' );
        }

        if (
            isset( $_POST['product_csv_import'] )
            && check_admin_referer( 'product_csv_import', 'product_csv_import_nonce' )
        ) {
            $this->handle_import();
        }

        ?>
        <div class="wrap">
            <h1>Import Products from CSV</h1>

            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'product_csv_import', 'product_csv_import_nonce' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="csv_file">CSV file</label></th>
                        <td><input type="file" name="csv_file" id="csv_file" accept=".csv" required></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="import_lang">Language (Polylang)</label></th>
                        <td>
                            <select name="import_lang" id="import_lang">
                                <option value="">— Do not set language —</option>
                                <option value="en">English (en)</option>
                                <option value="de">Deutsch (de)</option>
                                <!-- якщо у тебе slug мови "eng" → поміняй value="en" на "eng" -->
                            </select>
                            <p class="description">
                                Обери мову для всіх продуктів цього імпорту (Polylang).
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="product_csv_import" class="button button-primary" value="Import">
                </p>
            </form>
        </div>
        <?php
    }

    protected function handle_import() {
        if ( empty( $_FILES['csv_file']['tmp_name'] ) ) {
            echo '<div class="notice notice-error"><p>No file uploaded.</p></div>';
            return;
        }

        $file = $_FILES['csv_file']['tmp_name'];

        if ( ( $handle = fopen( $file, 'r' ) ) === false ) {
            echo '<div class="notice notice-error"><p>Cannot open file.</p></div>';
            return;
        }

        $header = fgetcsv( $handle );
        if ( ! $header ) {
            echo '<div class="notice notice-error"><p>Empty CSV.</p></div>';
            fclose( $handle );
            return;
        }

        // Нормалізуємо заголовки
        $header = array_map( 'trim', $header );

        $row_count   = 0;
        $created     = 0;
        $updated     = 0;

        // Мова для цього імпорту (Polylang slug: en, de, eng, і т.д.)
        $lang = isset( $_POST['import_lang'] )
            ? sanitize_text_field( wp_unslash( $_POST['import_lang'] ) )
            : '';

        while ( ( $row = fgetcsv( $handle ) ) !== false ) {
            $row_count++;
            $data = array_combine( $header, $row );

            if ( ! $data ) {
                continue;
            }

            $this->import_row( $data, $created, $updated, $lang );
        }

        fclose( $handle );

        echo '<div class="notice notice-success"><p>';
        printf(
            'Processed rows: %d. Created: %d, Updated: %d.',
            intval( $row_count ),
            intval( $created ),
            intval( $updated )
        );
        echo '</p></div>';
    }

    /**
     * $data очікується структура:
     * Product Name
     * Flavors
     * Specifications
     * Price
     * SKU
     * Description (ENG)
     * Product Image (ігноруємо, ти сказав зробиш сам)
     */
    protected function import_row( $data, &$created, &$updated, $lang = '' ) {

        $title = isset( $data['Product Name'] ) ? trim( $data['Product Name'] ) : '';

        if ( $title === '' ) {
            return;
        }

        // Шукаємо існуючий product по title
        $existing = get_page_by_title( $title, OBJECT, 'product' );

        if ( $existing ) {
            $post_id = $existing->ID;
            $updated++;
        } else {
            $post_id = wp_insert_post( [
                'post_title'   => $title,
                'post_type'    => 'product',
                'post_status'  => 'publish',
            ] );
            if ( is_wp_error( $post_id ) ) {
                return;
            }
            $created++;
        }

        // Виставляємо мову (Polylang), якщо є
        if ( $lang !== '' ) {
            $this->set_post_language( $post_id, $lang );
        }

        // ==== Flavors → taxonomy=flavour & post_type=product ====
        if ( ! empty( $data['Flavors'] ) ) {
            $this->set_flavors_taxonomy( $post_id, $data['Flavors'] );
        }

        // ==== Specifications → таксономії ====
        if ( ! empty( $data['Specifications'] ) ) {
            $spec = $this->parse_specifications( $data['Specifications'] );
            $this->assign_spec_taxonomies( $post_id, $spec );
        }

        // ==== Price + SKU (Carbon Fields product_options) ====
        $price = isset( $data['Price'] ) ? trim( $data['Price'] ) : '';
        $sku   = isset( $data['SKU'] )   ? trim( $data['SKU'] )   : '';

        if ( $price !== '' || $sku !== '' ) {
            $this->set_main_price_and_sku( $post_id, $price, $sku );
        }

        // ==== Description (ENG) + price в screen_1 ====
        if ( ! empty( $data['Description (ENG)'] ) ) {
            $this->set_carbon_description( $post_id, $data['Description (ENG)'], $price );
        }
    }

    /**
     * Flavors колонка — це multi-line список смаків.
     * Вішаємо в taxonomy=flavour.
     */
    protected function set_flavors_taxonomy( $post_id, $raw ) {
        $parts = preg_split( '/\r\n|\n|,/', $raw );
        $terms = [];

        foreach ( $parts as $p ) {
            $p = trim( $p );
            if ( $p !== '' ) {
                $terms[] = $p;
            }
        }

        if ( ! empty( $terms ) ) {
            wp_set_object_terms( $post_id, $terms, 'flavour', false );
        }
    }

    /**
     * Розбираємо Specifications:
     * Brand: Elf Bar
     * Model: Raya D3
     * Puffs: 25 000
     * Nicotine Strength: 5%
     * E-liquid Capacity: 23 ml
     * Modes: Lite / Smooth / Turbo (12W / 17W / 30W)
     * Charging Port: Type-C
     * Battery Capacity: 850 mAh
     * Size: 52 × 28 × 94 mm
     * Ratio: ...
     */
    protected function parse_specifications( $raw ) {
        $lines = preg_split( '/\r\n|\n/', $raw );
        $spec  = [];

        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( $line === '' ) continue;

            $pos = strpos( $line, ':' );
            if ( $pos === false ) continue;

            $key = trim( substr( $line, 0, $pos ) );
            $val = trim( substr( $line, $pos + 1 ) );
            if ( $key !== '' ) {
                $spec[ $key ] = $val;
            }
        }

        return $spec;
    }

    /**
     * Маппінг:
     * Brand: taxonomy=brand
     * Model: taxonomy=brand (як ти й написав)
     * Puffs: taxonomy=puff
     * Nicotine Strength: taxonomy=nicotine
     * E-liquid Capacity: taxonomy=liquid_volume
     * Modes: taxonomy=model (Lite(12W) / Smooth(17W) / Turbo(30W))
     * Charging Port: taxonomy=charging
     * Battery Capacity: taxonomy=battery
     * Size: taxonomy=size
     * Ratio: taxonomy=ratio
     */
    protected function assign_spec_taxonomies( $post_id, $spec ) {

        // Brand
        if ( ! empty( $spec['Brand'] ) ) {
            wp_set_object_terms( $post_id, $spec['Brand'], 'brand', true );
        }

        // Model (ти вказав теж taxonomy=brand)
        if ( ! empty( $spec['Model'] ) ) {
            wp_set_object_terms( $post_id, $spec['Model'], 'brand', true );
        }

        // Puffs
        if ( ! empty( $spec['Puffs'] ) ) {
            $puffs = trim( str_replace( ' ', '', $spec['Puffs'] ) ); // 25 000 → 25000
            wp_set_object_terms( $post_id, $puffs, 'puff', false );
        }

        // Nicotine Strength
        if ( ! empty( $spec['Nicotine Strength'] ) ) {
            wp_set_object_terms( $post_id, $spec['Nicotine Strength'], 'nicotine', false );
        }

        // E-liquid Capacity
        if ( ! empty( $spec['E-liquid Capacity'] ) ) {
            wp_set_object_terms( $post_id, $spec['E-liquid Capacity'], 'liquid_volume', false );
        }

        // Modes → taxonomy=model, 3 терми Lite(12W) / Smooth(17W) / Turbo(30W)
        if ( ! empty( $spec['Modes'] ) ) {
            $this->set_modes_terms( $post_id, $spec['Modes'] );
        }

        // Charging Port
        if ( ! empty( $spec['Charging Port'] ) ) {
            wp_set_object_terms( $post_id, $spec['Charging Port'], 'charging', false );
        }

        // Battery Capacity
        if ( ! empty( $spec['Battery Capacity'] ) ) {
            wp_set_object_terms( $post_id, $spec['Battery Capacity'], 'battery', false );
        }

        // Size
        if ( ! empty( $spec['Size'] ) ) {
            wp_set_object_terms( $post_id, $spec['Size'], 'size', false );
        }

        // Ratio (якщо є)
        if ( ! empty( $spec['Ratio'] ) ) {
            wp_set_object_terms( $post_id, $spec['Ratio'], 'ratio', false );
        }
    }

    /**
     * Modes: Lite / Smooth / Turbo (12W / 17W / 30W)
     * → Lite (12W), Smooth (17W), Turbo (30W) в taxonomy=model
     */
    protected function set_modes_terms( $post_id, $raw ) {
        $raw = trim( $raw );

        // Якщо формат з дужками
        if ( strpos( $raw, '(' ) !== false && strpos( $raw, ')' ) !== false ) {
            $before = trim( substr( $raw, 0, strpos( $raw, '(' ) ) ); // Lite / Smooth / Turbo
            $inside = trim( substr( $raw, strpos( $raw, '(' ) + 1, -1 ) ); // 12W / 17W / 30W

            $names = array_map( 'trim', explode( '/', $before ) );
            $watts = array_map( 'trim', explode( '/', $inside ) );

            $terms = [];

            foreach ( $names as $i => $name ) {
                if ( $name === '' ) continue;
                $watt = isset( $watts[ $i ] ) ? $watts[ $i ] : '';
                $label = $watt ? sprintf( '%s (%s)', $name, $watt ) : $name;
                $terms[] = $label;
            }

            if ( ! empty( $terms ) ) {
                wp_set_object_terms( $post_id, $terms, 'model', false );
            }

        } else {
            // Фолбек: просто ріжемо по "/"
            $parts = array_map( 'trim', explode( '/', $raw ) );
            $parts = array_filter( $parts );
            if ( ! empty( $parts ) ) {
                wp_set_object_terms( $post_id, $parts, 'model', false );
            }
        }
    }

    /**
     * Price + SKU в основні поля Carbon Fields (product_options).
     *
     * Container::make( 'post_meta', 'product_options', ... )
     *   Field::make( 'text', 'price', 'Price (number, e.g. 10.90)' )
     *   Field::make( 'text', 'sku', 'sku' )
     */
    protected function set_main_price_and_sku( $post_id, $price, $sku ) {
        if ( ! function_exists( 'carbon_set_post_meta' ) ) {
            return;
        }

        if ( $price !== '' ) {
            carbon_set_post_meta( $post_id, 'price', sanitize_text_field( $price ) );
        }

        if ( $sku !== '' ) {
            carbon_set_post_meta( $post_id, 'sku', sanitize_text_field( $sku ) );
        }
    }

    /**
     * Carbon Fields:
     * Field::make( 'complex', 'product_screens', 'Product screens' )
     *   ->add_fields( 'screen_1', ...
     *      Field::make( 'text', 'price', 'Price (e.g. $10.90)' )
     *      Field::make( 'rich_text', 'description', 'Description' )
     *   )
     *
     * Ставимо / оновлюємо description і price в першому screen_1.
     */
    protected function set_carbon_description( $post_id, $description, $price = null ) {
        if ( ! function_exists( 'carbon_get_post_meta' ) || ! function_exists( 'carbon_set_post_meta' ) ) {
            return;
        }

        $screens = carbon_get_post_meta( $post_id, 'product_screens' );
        if ( ! is_array( $screens ) ) {
            $screens = [];
        }

        $found = false;

        foreach ( $screens as &$screen ) {
            if ( isset( $screen['_type'] ) && $screen['_type'] === 'screen_1' ) {
                $screen['description'] = wp_kses_post( $description );
                if ( $price !== null && $price !== '' ) {
                    $screen['price'] = sanitize_text_field( $price );
                }
                $found = true;
                break;
            }
        }

        if ( ! $found ) {
            $new_screen = [
                '_type'       => 'screen_1',
                'description' => wp_kses_post( $description ),
            ];

            if ( $price !== null && $price !== '' ) {
                $new_screen['price'] = sanitize_text_field( $price );
            }

            $screens[] = $new_screen;
        }

        carbon_set_post_meta( $post_id, 'product_screens', $screens );
    }

    /**
     * Виставляємо мову поста через Polylang.
     *
     * @param int    $post_id
     * @param string $lang   Slug мови (en, de, eng, і т.д.)
     */
    protected function set_post_language( $post_id, $lang ) {
        if ( function_exists( 'pll_set_post_language' ) && $lang ) {
            pll_set_post_language( $post_id, $lang );
        }
    }

}

new Product_CSV_Importer();
