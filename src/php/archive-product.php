<?php
get_header();

global $wp_query;

$paged      = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$max_pages  = (int) $wp_query->max_num_pages;
$max_pages_last  = $max_pages - 1;
$lang       = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'en';
$source_id  = get_the_ID(); // якщо на окремій “каталог”-сторінці; для archive можеш залишити 0
?>
<section class="product js-products-section"
         data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
         data-current-page="<?php echo esc_attr( $paged ); ?>"
         data-lang="<?php echo esc_attr( $lang ); ?>"
         data-source-id="<?php echo esc_attr( $source_id ); ?>" data-products-mode="catalog">
    <div class="container">
        <div class="product-inner item">
            <div class="bread-crumbs">
                <ul>
                    <li><a href="/"><?php pll_e( 'Home' ); ?></a></li>
                    <li><?php pll_e( 'Catalog' ); ?></li>
                </ul>
            </div>
            <div class="title-section"><?php pll_e( 'Catalog' ); ?></div>
            <div class="section-filter">
                <div class="filter-section">

                    <?php
                    $tax_objects = get_object_taxonomies( 'product', 'objects' );
                    $exclude_tax = [
                        'post_translations',
                        'post_format',
                        'language',
                        'nav_menu',
                        'wp_theme',
                        'wp_pattern_category',
                    ];

                    if ( $tax_objects ) :

                        foreach ( $tax_objects as $tax ) :
                            if ( in_array( $tax->name, $exclude_tax, true ) ) {
                                continue;
                            }
                            $terms = get_terms( array(
                                'taxonomy'   => $tax->name,
                                'hide_empty' => true,
                            ) );

                            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                                continue;
                            }
                            ?>
                            <select
                                    class="product-filter js-tax-filter"
                                    data-taxonomy="<?php echo esc_attr( $tax->name ); ?>"
                            >
                                <option value="">
                                    <?php echo esc_html( $tax->labels->singular_name ?? $tax->label ); ?>
                                </option>

                                <?php foreach ( $terms as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>">
                                        <?php echo esc_html( $term->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php
                        endforeach;
                    endif;
                    ?>

                </div>

                <div class="sort-section">
                    <span class="sort-section__title"><?php pll_e( 'Sort: by' ); ?> </span>
                    <ul class="sort-section__list js-sort-list">
                        <li data-sort="date_desc" class="active"><?php pll_e( 'Newest' ); ?></li>
                        <li data-sort="price_asc"><?php pll_e( 'Price ↓' ); ?></li>
                        <li data-sort="price_desc"><?php pll_e( 'Price ↑' ); ?></li>
                        <li data-sort="title_asc"><?php pll_e( 'Name A–Z' ); ?></li>
                        <li data-sort="title_desc"><?php pll_e( 'Name Z–A' ); ?></li>
                    </ul>
                </div>
            </div>


            <div class="product-wrap js-product-wrap" data-per-page="16">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();
                        render_product_item();
                    endwhile;
                else :?>
                    <p><?php pll_e( 'Not found' ); ?></p>;
                <?php
                endif;
                ?>
            </div>

            <div class="pagination-wrap js-pagination-wrap" data-max-pages="<?php echo esc_attr( $max_pages ); ?>">
                <div class="pagination js-product-pagination">
                </div>
                <div class="pagination-info">
                    <?php pll_e( 'Go to page:' ); ?>
                    <a href="#" class="js-go-last" data-last="<?php echo esc_attr( $max_pages_last ); ?>">
                        <span><?php echo esc_html( $max_pages_last ); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$home_id = get_option( 'page_on_front' );
if ( $home_id ) {
    $home_screens = carbon_get_post_meta( $home_id, 'screens' );
    if ( ! empty( $home_screens ) && is_array( $home_screens ) ) {
        require_once get_template_directory() . '/functions/wp/Screens.php';
        $screens_obj = new Screens( $home_screens, false );
        $screens_obj->the_home_tail_screens();
    }
}
?>
<?php
get_footer();
?>