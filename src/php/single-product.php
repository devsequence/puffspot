<?php
/**
 * Single Product template
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();

        $product_id = get_the_ID();

        // Базові дані
        $title = get_the_title();
        $price = carbon_get_post_meta( $product_id, 'price' ); // своє поле з ціною
        $image_html = get_the_post_thumbnail( $product_id, 'large', [
            'alt' => $title,
        ] );

        // Опис: або мета, або контент
        $description = carbon_get_post_meta( $product_id, 'description' );


        // Flavors — наприклад, таксономія 'flavor'
        $flavors_terms = wp_get_post_terms( $product_id, 'flavor' );

        // Специфікація — приклад через мети (поміняй ключі під себе)
        $spec_model            = carbon_get_post_meta( $product_id, 'spec_model' );
        $spec_flavours         = carbon_get_post_meta( $product_id, 'spec_flavours' );
        $spec_puffs            = carbon_get_post_meta( $product_id, 'spec_puffs' );
        $spec_nicotine         = carbon_get_post_meta( $product_id, 'spec_nicotine' );
        $spec_liquid_volume    = carbon_get_post_meta( $product_id, 'spec_liquid_volume' );
        $spec_battery_display  = carbon_get_post_meta( $product_id, 'spec_battery_display' );
        $spec_battery_capacity = carbon_get_post_meta( $product_id, 'spec_battery_capacity' );

        $home_url    = home_url( '/' );
        $catalog_url = get_post_type_archive_link( 'product' );
        ?>

        <section style="display: none" class="product">
            <div class="container">
                <div class="product-inner item">
                    <div class="bread-crumbs">
                        <ul>
                            <li><a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Home', 'textdomain' ); ?></a></li>
                            <?php if ( $catalog_url ) : ?>
                                <li><a href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'Catalog', 'textdomain' ); ?></a></li>
                            <?php endif; ?>
                            <li><?php echo esc_html( $title ); ?></li>
                        </ul>
                    </div>

                    <div class="title-section">
                        <?php esc_html_e( 'Catalog', 'textdomain' ); ?>
                    </div>

                    <div class="product-page__wrap">
                        <div class="product-page__title">
                            <?php echo esc_html( $title ); ?>
                        </div>

                        <div class="product-page__media">
                            <div class="product-page__image">
                                <span></span>
                                <?php if ( $image_html ) : ?>
                                    <?php echo $image_html; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-page__desc">
                            <div class="product-page__title">
                                <?php echo esc_html( $title ); ?>
                            </div>

                            <?php if ( $price ) : ?>
                                <div class="product-page__price">
                                    <?php echo esc_html( $price ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $description ) : ?>
                                <div class="product-page__text">
                                    <?php echo wp_kses_post( $description ); ?>
                                </div>
                            <?php endif; ?>

                            <div class="product-page__action">
                                <a href="#"
                                   class="btn-primary btn-popup"
                                   data-popup="call">
                                    <?php pll_e( 'CONTACT MANAGER' ); ?>
                                </a>
                            </div>

                            <?php if ( ! empty( $flavors_terms ) && ! is_wp_error( $flavors_terms ) ) : ?>
                                <div class="product-page__category">
                                    <div class="title-block">
                                        <?php esc_html_e( 'Flavors', 'textdomain' ); ?>
                                    </div>
                                    <ul>
                                        <?php foreach ( $flavors_terms as $term ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
                                                    <?php echo esc_html( $term->name ); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="product-page__specifications">
                                <div class="title-block">
                                    <?php esc_html_e( 'Specifications', 'textdomain' ); ?>
                                </div>
                                <ul>
                                    <?php if ( $spec_model ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Model:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_model ); ?></span>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $spec_flavours ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Flavours:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_flavours ); ?></span>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $spec_puffs ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Puffs:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_puffs ); ?></span>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $spec_nicotine ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Nicotine Strength:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_nicotine ); ?></span>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $spec_liquid_volume ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Liquid Volume:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_liquid_volume ); ?></span>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $spec_battery_display ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Battery & E-juice:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_battery_display ); ?></span>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $spec_battery_capacity ) : ?>
                                        <li>
                                            <span><?php esc_html_e( 'Battery Capacity:', 'textdomain' ); ?></span>
                                            <span><?php echo esc_html( $spec_battery_capacity ); ?></span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                        </div><!-- /.product-page__desc -->
                    </div><!-- /.product-page__wrap -->
                </div>
            </div>
        </section>

        <?php
    endwhile;
endif;
?>
<?php
// Отримуємо всі “екрани” для цього продукту
$product_screens = carbon_get_the_post_meta( 'product_screens' );
// або: carbon_get_post_meta( get_the_ID(), 'product_screens' );

$screen_1 = null;
$screen_2 = null;

if ( ! empty( $product_screens ) && is_array( $product_screens ) ) {
    foreach ( $product_screens as $screen ) {
        switch ( $screen['_type'] ?? '' ) {
            case 'screen_1':
                $screen_1 = $screen;
                break;
            case 'screen_2':
                $screen_2 = $screen;
                break;
        }
    }
}
?>
<?php if ( $screen_1 ) :

    $title       = $screen_1['custom_title'] ?: get_the_title();
    $description = $screen_1['description'] ?? '';
    $flavors     = $screen_1['flavors'] ?? [];
    $image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
    $flavors_assoc = $screen_1['flavors_products'] ?? array();
    $flavour_terms = wp_get_post_terms( get_the_ID(), 'flavour' );

    ?>

<section class="product product-page">
        <div class="container">
            <div class="product-inner item">
                <div class="bread-crumbs">
                    <ul>
                        <li><a href="<?php echo esc_url( home_url('/') ); ?>"><?php pll_e( 'Home' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'product' ) ); ?>"><?php pll_e( 'Catalog' ); ?></a></li>
                        <li><?php echo esc_html( $title ); ?></li>
                    </ul>
                </div>
<!--                <div class="title-section">Catalog</div>-->
                <div class="product-page__wrap">
                    <div class="product-page__title"><?php echo esc_html( $title ); ?></div>
				<div class="product-page__sticky">
                    <div class="product-page__media">
                        <div class="product-page__image">
                            <span></span>
                            <?php if ( $image ) : ?>
                                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
</div>
                    <div class="product-page__desc">
                        <div class="product-page__title"><?php echo esc_html( $title ); ?></div>

                        <?php if ( $price ) : ?>
                            <div class="product-page__price">$ <?php echo esc_html( $price ); ?></div>
                        <?php endif; ?>

                        <?php if ( $description ) : ?>
                            <div class="product-page__text">
                                <?php echo wp_kses_post( $description ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="product-page__action">
                            <a href="#" class="btn-primary btn-popup" data-popup="call"><?php pll_e( 'CONTACT MANAGER' ); ?></a>
                        </div> <?php
                        $flavour_terms = wp_get_post_terms( get_the_ID(), 'flavour' );
                        ?>


                        <?php if ( ! empty( $flavour_terms ) && ! is_wp_error( $flavour_terms ) ) : ?>
                            <div class="product-page__category">
                                <div class="title-block"><?php pll_e( 'flavours' ); ?></div>
                                <ul>
                                    <?php $i=1; foreach ( $flavour_terms as $term ) : ?>
                                        <li>
                                            <a href="#" <?php if( $i === 1){ echo 'class="active"';}?>>
                                                <?php echo esc_html( $term->name ); ?>
                                            </a>
                                        </li>
                                        <?php $i++; endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $flavors_assoc ) ) : ?>
                            <div class="product-page__category">
                                <div class="title-block"><?php pll_e( 'flavours' ); ?></div>
                                <ul>
                                    <?php foreach ( $flavors_assoc as $item ) :
                                        if ( empty( $item['id'] ) ) {
                                            continue;
                                        }

                                        $pid    = (int) $item['id'];
                                        $plink  = get_permalink( $pid );

                                        // За замовчуванням – тайтл продукту
                                        $label  = get_the_title( $pid );

                                        $flavour_terms = wp_get_post_terms( $pid, 'flavour' );
                                        if ( ! empty( $flavour_terms ) && ! is_wp_error( $flavour_terms ) ) {
                                            $label = $flavour_terms[0]->name;
                                        }
                                        ?>
                                        <li>
                                            <a href="<?php echo esc_url( $plink ); ?>">
                                                <?php echo esc_html( $label ); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

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

                        $spec_rows = [];

                        if ( $tax_objects ) {
                            foreach ( $tax_objects as $tax ) {

                                // Пропускаємо службові таксономії
                                if ( in_array( $tax->name, $exclude_tax, true ) ) {
                                    continue;
                                }
                                $terms = wp_get_post_terms( get_the_ID(), $tax->name );

                                if ( empty( $terms ) || is_wp_error( $terms ) ) {
                                    continue;
                                }

                                $first_term = $terms[0];
                                $label = $tax->labels->singular_name ?? $tax->label ?? $tax->name;

                                $spec_rows[] = [
                                    'label' => $label,
                                    'value' => $first_term->name,
                                ];
                            }
                        }
                        ?>

                        <?php if ( ! empty( $spec_rows ) ) : ?>
                            <div class="product-page__specifications">
                                <div class="title-block">
                                    <?php pll_e( 'Specifications' ); ?>
                                </div>
                                <ul>
                                    <?php foreach ( $spec_rows as $row ) : ?>
                                        <li>
                                            <span><?php echo esc_html( $row['label'] ); ?>:</span>
                                            <span><?php echo esc_html( $row['value'] ); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                    </div><!-- .product-page__desc -->
                </div><!-- .product-page__wrap -->

            </div>
        </div>
    </section>
<?php endif; ?>
<?php if ( $screen_2 ) :
        $title       = $screen_2['title'];
        $title_text       = $screen_2['title_text'];
        $list        =  $screen_2['list'];

        ?>
    <section class="best-price">
        <div class="container">
            <div class="distributor-inner item">
                <div class="top-section">
                    <div class="title-section"><?php echo $title;?></div>
                    <div class="text-section"><?php echo $title_text;?></div>
                </div>
                <?php if ( ! empty( $list ) ) : ?>
                    <div class="best-price__list">
                        <ul>

                            <li>
                                <?php
                                $i = 1;
                                foreach ( $list as $item ) :
                                    $text1 = $item['text_1'] ?? '';

                                    if ( $i === 1 ) :
                                        echo '<b>' . esc_html( $text1 ) . '</b>';
                                    else :
                                        echo '<span>' . esc_html( $text1 ) . '</span>';
                                    endif;

                                    $i++;
                                endforeach;
                                ?>
                            </li>

                            <li>
                                <?php
                                $i = 1;
                                foreach ( $list as $item ) :
                                    $text2 = $item['text_2'] ?? '';

                                    if ( $i === 1 ) :
                                        echo '<b>' . esc_html( $text2 ) . '</b>';
                                    else :
                                        echo '<span>' . esc_html( $text2 ) . '</span>';
                                    endif;

                                    $i++;
                                endforeach;
                                ?>
                            </li>

                        </ul>
                    </div>
                <?php endif; ?>

                <div class="best-price__bg"></div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php
$home_id = get_option( 'page_on_front' );
if ( $home_id ) {
    $home_screens = carbon_get_post_meta( $home_id, 'screens' );
    if ( ! empty( $home_screens ) && is_array( $home_screens ) ) {
        require_once get_template_directory() . '/functions/wp/Screens.php';
        $screens_obj = new Screens( $home_screens, false );
        $screens_obj->the_home_product_screens();
    }
}
?>
<?php
get_footer();
?>