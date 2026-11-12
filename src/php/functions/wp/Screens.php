<?php

class Screens {
	private mixed $screens;
	private array $variables;
	private string $currency;

    public function __construct( $screens, bool $auto_render = true ) {
        $this->screens   = is_array( $screens ) ? $screens : [];
        $this->currency  = '';
        $this->variables = variables();
        // $this->set_currency();

        if ( $auto_render ) {
            $this->the_screens();
        }
    }

	// private function set_currency(): void {
	// 	if ( function_exists( 'carbon_get_theme_option' ) ) {
	// 		$this->currency = carbon_get_theme_option( 'currency_symbol' );
	// 	}
	// }

	private function the_first_screen( $screen, $screen_id = false ): void {
		$assets     = $this->variables['assets'];
        $list     = $screen['list'];
        $listBanner     = $screen['list_2'];
		?>
        <section class="hero">
            <div class="container">
                <?php if ( ! empty( $list ) ) : ?>
                <div class="hero-slider__inner">
                    <div class="hero-slider">
                        <?php foreach ( $list as $item ) :?>
                        <div class="slide">
                            <div class="hero-slider__item">
                                <div class="hero-slider__media">
                                    <img src=" <?php echo $item['img']; ?>" alt="hero">
                                </div>
                                <div class="hero-slider__desc">
                                    <div class="hero-slider__title"> <?php echo $item['title']; ?></div>
                                    <div class="hero-slider__text"><?php echo wp_kses_post($item['text']); ?></div>
                                    <div class="hero-slider__link"><a href=" <?php echo $item['btn_link']; ?>" class="btn-white"> <?php echo $item['btn_link_text']; ?></a></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="hero-nav">
                        <button type="button" aria-label="hero-prev" class="hero-prev prev">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4L7 9.5L12 15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="hero-dots"></div>
                        <button type="button" aria-label="hero-next" class="hero-next next">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 4L12 9.5L7 15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ( ! empty( $listBanner ) ) : ?>
                <div class="hero-new">
                    <?php foreach ( $listBanner as $item ) :
                        $subtitle = $item['subtitle'];
                        $title = $item['title'];
                        $text = $item['text'];

                        ?>
                    <div class="hero-new__item">
                        <div class="hero-new__media">
                            <img src="<?php echo $item['img']; ?>" alt="hero">
                        </div>
                        <div class="hero-new__desc">
                            <?php if ( $subtitle ) : ?>
                            <div class="hero-new__subtitle" style="color: #00285A;"><?php echo $item['subtitle']; ?></div>
                            <?php endif; ?>
                             <?php if ( $title ) : ?>
                            <div class="hero-new__title"><?php echo $item['title']; ?></div>
                             <?php endif; ?>
                            <?php if ( $text ) : ?>
                            <div class="hero-new__text"><?php echo $item['text']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
	}


    private function the_2_screen( $screen, $screen_id = false ): void {
        if ( empty( $screen['list'] ) || ! is_array( $screen['list'] ) ) {
            return;
        }
        $list = $screen['list'];
        ?>
        <section class="advantages">
            <div class="container">
                <?php if ( ! empty( $list ) ) : ?>
                <div class="advantages-wrap">
                    <?php foreach ( $list as $item ) :
                        $icon_id   = $item['icon'];
                        $icon_url  = wp_get_attachment_image_url( $icon_id, 'full' );

                        ?>
                    <div class="advantages-item item">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <div class="advantages-item__media">
                            <img src="<?php echo $icon_url; ?>" alt="advantages">
                        </div>
                        <div class="advantages-item__title"><?php echo $item['icon_text']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <?php
    }

    private function the_3_screen( $screen, $screen_id = false ): void {
        // Якщо поля вкладені в підмасив, наприклад 'products' – підтягуємо його:
        $block = $screen['products'] ?? $screen;

        $title   = $screen['title'] ?? '';
        $text    = $block['text'] ?? '';
        $image   = ! empty( $block['image'] ) ? esc_url( $block['image'] ) : '';
        $btn_txt = $block['btn_link_text'] ?? '';
        $btn_url = $block['btn_link'] ?? '';

        $products_mode = $screen['products_mode'] ?? 'catalog';

        $assoc       = $screen['crb_association'] ?? array();
        $product_ids = array();

        if ( ! empty( $assoc ) && is_array( $assoc ) ) {
            foreach ( $assoc as $item ) {
                if ( ! empty( $item['id'] ) ) {
                    $product_ids[] = (int) $item['id'];
                }
            }
        }

        $paged    = 1;
        $per_page = 8;

        $query_args = array(
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
        );

        if ( function_exists( 'pll_current_language' ) ) {
            $query_args['lang'] = pll_current_language( 'slug' );
        }

        // 🔹 застосовуємо режим
        switch ( $products_mode ) {
            case 'hits':
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'ptype',
                        'field'    => 'slug',
                        'terms'    => 'hits', // slug категорії Hits
                    ),
                );
                $query_args['orderby'] = 'date';
                $query_args['order']   = 'DESC';
                break;

            case 'manual':
                if ( $product_ids ) {
                    $query_args['post__in'] = $product_ids;
                    $query_args['orderby']  = 'post__in';
                } else {
                    $query_args['post__in'] = array( 0 ); // щоб було пусто, а не каталог
                }
                break;

            case 'newest':
                $query_args['orderby'] = 'date';
                $query_args['order']   = 'DESC';
                break;

            case 'catalog':
            default:
                $query_args['orderby'] = 'date';
                $query_args['order']   = 'DESC';
                break;
        }

        $products_q = new WP_Query( $query_args );
        $max_pages  = (int) $products_q->max_num_pages;
        $max_pages_last  = $max_pages - 1;

        // 🔹 для manual передамо ID в data-атрибут
        $manual_ids_attr = '';
        if ( $products_mode === 'manual' && $product_ids ) {
            $manual_ids_attr = implode( ',', $product_ids );
        }
        ?>
        <section class="product js-products-section"
                 data-products-mode="<?php echo esc_attr( $products_mode ); ?>"
                 data-per-page="<?php echo esc_attr( $per_page ); ?>"
                 data-source-id="<?php echo esc_attr( get_the_ID() ); ?>"
            <?php if ( $manual_ids_attr ) : ?>
                data-manual-ids="<?php echo esc_attr( $manual_ids_attr ); ?>"
            <?php endif; ?>
        >
            <div class="container">
                <div class="product-inner item">
                    <div class="title-section">
                        <?php echo esc_html( $title ); ?>
                    </div>

                    <div class="section-filter">
                        <div class="filter-section">
                            <?php
                            $tax_objects = get_object_taxonomies( 'product', 'objects' );
                            $exclude_tax = [
                                'post_translations',
                                'language',
                                'ptype',
                                'post_format',
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
                                    $select_id = $tax->name . $screen_id;
                                    ?>
                                    <label for="<?php echo esc_attr( $select_id ); ?>" style="font-size: 0;">
                                        <?php echo esc_html( $tax->labels->singular_name ?? $tax->label ); ?>
                                    </label>
                                    <select id="<?php echo esc_attr( $select_id ); ?>"
                                            class="product-filter js-tax-filter"
                                            data-taxonomy="<?php echo esc_attr( $tax->name ); ?>">
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

                    <div class="product-wrap js-product-wrap"
                         data-current-page="<?php echo esc_attr( $paged ); ?>"
                         data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
                         data-per-page="<?php echo esc_attr( $per_page ); ?>"
                         data-source-id="<?php echo esc_attr( get_the_ID() ); ?>">
                        <?php
                        if ( $products_q->have_posts() ) :
                            while ( $products_q->have_posts() ) :
                                $products_q->the_post();
                                render_product_item();
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p>' . esc_html__( 'No products to display.', 'textdomain' ) . '</p>';
                        endif;
                        ?>
                    </div>

                    <div class="pagination-wrap js-pagination-wrap">
                        <div class="pagination js-product-pagination"></div>
                        <div class="pagination-info">
                            <?php pll_e( 'Go to page:' ); ?>
                            <a href="#" class="js-go-last" data-last="<?php echo esc_attr( $max_pages ); ?>">
                                <span><?php echo esc_html( $max_pages ); ?></span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <?php
    }


    private function the_4_screen( $screen, $screen_id = false ): void {
        $list = $screen['list'];
        ?>
        <section class="banner"  id="<?php echo esc_attr( $screen_id ); ?>">
            <div class="container">
                <?php if ( ! empty( $list ) ) : ?>
                <div class="banner-wrap">
                    <?php foreach ( $list as $item ) :
                        $bg_id   = $item['bg'];
                        $bg_url  = wp_get_attachment_image_url( $bg_id, 'full' );
                        $image_id   = $item['image'];
                        $image_url  = wp_get_attachment_image_url( $image_id, 'full' );
                        $class   = $item['class'];
                        $title   = $item['title'];
                        $text   = $item['text'];
                        ?>
                    <div class="banner-item <?php echo $class;?>" style="background-image: url('<?php echo $bg_url; ?>')">
                        <div class="banner-item__desc">
                            <div class="banner-item__title"><?php echo wp_kses_post( $title ); ?></div>
                            <div class="banner-item__text"><?php echo wp_kses_post( $text ); ?></div>
                        </div>
                        <div class="banner-item__media">
                            <img src="<?php echo $image_url; ?>" alt="banner">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <?php
    }

    private function the_5_screen( $screen, $screen_id = false ): void {
        $title = $screen['title'] ?? '';
        $text  = $screen['text'] ?? '';
        $list  = $screen['list'] ?? [];
        if ( ! is_array( $list ) ) {
            $list = [];
        }
        if ( $screen && empty( $screen['screen_off'] ) ) :

        $title               = isset( $screen['title'] ) ? $screen['title'] : '';
        $reviews_number      = isset( $screen['reviews_number'] ) ? $screen['reviews_number'] : '';
        $reviews_info        = isset( $screen['reviews_info'] ) ? $screen['reviews_info'] : '';
        $reviews_trustpilot  = isset( $screen['reviews_trustpilot'] ) ? $screen['reviews_trustpilot'] : '';
        $list_trustpilot     = isset( $screen['list_trustpilot'] ) ? $screen['list_trustpilot'] : array();
        $reviews_number_star = isset( $screen['reviews_number_star'] ) ? (int) $screen['reviews_number_star'] : 5;
        $reviews_info_trust  = isset( $screen['reviews_info_trusted_by'] ) ? $screen['reviews_info_trusted_by'] : '';
        $trusted_text_link   = isset( $screen['trusted_text_link'] ) ? $screen['trusted_text_link'] : '';
        $trusted_link        = isset( $screen['trusted_link'] ) ? $screen['trusted_link'] : '#';
        $reviews_list        = isset( $screen['reviews_list'] ) ? $screen['reviews_list'] : array();
        ?>

        <section class="reviews">
            <div class="container">
                <div class="top-section">
                    <?php if ( $title ) : ?>
                        <div class="title-section">
                            <?php echo $title; ?>
                        </div>
                    <?php endif; ?>

                    <div class="nav-rating">
                        <button type="button" aria-label="reviews-prev" class="reviews-prev">
                            <img class="svg-convert" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/arrow-left.svg' ); ?>" alt="arrow-left">
                        </button>
                        <button type="button" aria-label="reviews-next"  class="reviews-next">
                            <img class="svg-convert" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/arrow-right.svg' ); ?>" alt="arrow-right">
                        </button>
                    </div>
                </div>

                <div class="reviews-wrap">
                    <div class="reviews-info">
                        <span class="product-item__bg _gray"></span>

                        <div class="reviews-info__top">
                            <?php if ( $reviews_number ) : ?>
                                <div class="reviews-info__number">
                                    <?php echo  $reviews_number; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $reviews_info ) : ?>
                                <div class="reviews-info__info">
                                    <?php echo $reviews_info; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ( $reviews_trustpilot ) : ?>
                            <div class="reviews-info__title">
                                <?php echo $reviews_trustpilot ; ?>
                            </div>
                        <?php endif; ?>

                        <div class="reviews-info__text">
                            <?php if ( ! empty( $list_trustpilot ) ) : ?>
                                <div class="reviews-info__trustpilot">
                                    <ul>
                                        <?php foreach ( $list_trustpilot as $tp_item ) :
                                            if ( empty( $tp_item['image'] ) ) {
                                                continue;
                                            }
                                            $img_url = wp_get_attachment_image_url( $tp_item['image'], 'thumbnail' );
                                            if ( ! $img_url ) {
                                                continue;
                                            }
                                            ?>
                                            <li>
                                                <img src="<?php echo esc_url( $img_url ); ?>" alt="trustpilot-user">
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="reviews-item__rating">
                                <ul>
                                    <?php
                                    $max_stars = 5;
                                    for ( $i = 1; $i <= $max_stars; $i++ ) :
                                        $active_class = ( $i <= $reviews_number_star ) ? 'active' : '';
                                        ?>
                                        <li class="<?php echo esc_attr( $active_class ); ?>">
                                            <img class="svg-convert" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/star.svg' ); ?>" alt="star">
                                        </li>
                                    <?php endfor; ?>
                                </ul>

                                <?php if ( $reviews_info_trust ) : ?>
                                    <p>
                                        <?php echo  $reviews_info_trust; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ( $trusted_link && $trusted_text_link ) : ?>
                            <div class="reviews-info__link">
                                <a href="<?php echo esc_url( $trusted_link ); ?>" class="btn-primary" target="_blank" rel="noopener">
                                    <?php echo  $trusted_text_link; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $reviews_list ) ) : ?>
                        <div class="reviews-inner">
                            <div class="reviews-slider">
                                <?php foreach ( $reviews_list as $review ) :
                                    if ( empty( $review['image'] ) ) {
                                        continue;
                                    }
                                    $img_url = wp_get_attachment_image_url( $review['image'], 'thumbnail' );
                                    $name    = isset( $review['name'] ) ? $review['name'] : '';
                                    $date    = isset( $review['date'] ) ? $review['date'] : '';
                                    $text    = isset( $review['text'] ) ? $review['text'] : '';
                                    $stars   = isset( $review['reviews_number'] ) ? (int) $review['reviews_number'] : 5;
                                    ?>
                                    <div class="slide">
                                        <div class="reviews-item">
                                            <div class="reviews-item__author">
                                                <div class="reviews-item__media">
                                                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                                                </div>
                                                <div class="reviews-item__info">
                                                    <?php if ( $name ) : ?>
                                                        <h4><?php echo esc_html( $name ); ?></h4>
                                                    <?php endif; ?>
                                                    <?php if ( $date ) : ?>
                                                        <p><?php echo esc_html( $date ); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="reviews-item__desc">
                                                <div class="reviews-item__rating">
                                                    <ul>
                                                        <?php
                                                        for ( $i = 1; $i <= 5; $i++ ) :
                                                            $active_class = ( $i <= $stars ) ? 'active' : '';
                                                            ?>
                                                            <li class="<?php echo esc_attr( $active_class ); ?>">
                                                                <img class="svg-convert" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/star.svg' ); ?>" alt="star">
                                                            </li>
                                                        <?php endfor; ?>
                                                    </ul>
                                                </div>

                                                <?php if ( $text ) : ?>
                                                    <div class="reviews-item__text">
                                                        <?php echo wp_kses_post( $text ); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>

        <?php endif; ?>
        <?php
    }

    private function the_6_screen( $screen, $screen_id = false ): void {
        $title    = $screen['title'] ?? '';
        $list     = $screen['list'] ?? [];

        ?>

        <section class="our-advantages">
            <div class="container">
                <div class="title-section"><?php echo $title;?></div>
                <?php if ( ! empty( $list ) ) : ?>
                <div class="our-advantages__wrap">
                    <?php foreach ( $list as $item ) :
                        $bg_id   = $item['icon'];
                        $bg_url  = wp_get_attachment_image_url( $bg_id, 'full' );
                        $title   = $item['title'];
                        $text   = $item['text'];?>
                    <div class="our-advantages__item">
                        <div class="our-advantages__media">
                            <span class="product-item__bg _gray"></span>
                            <img src="<?php echo $bg_url;?>" alt="our-advantages-1">
                        </div>
                        <div class="our-advantages__desc">
                            <h4><?php echo $title;?></h4>
                            <p><?php echo $text;?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <?php
    }
    private function the_7_screen( $screen, $screen_id = false ): void {
        $title = $screen['title'] ?? '';
        $titleText = $screen['title_text'] ?? '';
        $bg_id = $screen['bg'] ?? '';
        $bg_url  = wp_get_attachment_image_url( $bg_id, 'full' );
        $list  = $screen['list'] ?? [];
        ?>
        <section class="distributor">
            <div class="container">
                <div class="distributor-inner" style="background-image: url('<?php echo $bg_url;?>')">
                    <div class="top-section">
                        <div class="title-section"><?php echo $title;?></div>
                        <div class="text-section"><?php echo $titleText;?></div>
                    </div>
                    <?php if ( ! empty( $list ) ) : ?>

                    <div class="distributor-wrap">
                        <?php foreach ( $list as $item ) :
                            $title   = $item['title'];
                            $text   = $item['text'];?>
                        <div class="distributor-item">
                            <div class="distributor-item__desc">
                                <h3><?php echo $title;?></h3>
                                <p><?php echo $text;?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php
    }

    private function the_8_screen( $screen, $screen_id = false ): void {
        $title    = $screen['title'] ?? '';
        $list     = $screen['list'] ?? [];
        ?>
        <section class="faq">
            <div class="container">

                <div class="faq-inner item">
                    <div class="title-section"><?php echo $title;?></div>
                    <?php if ( ! empty( $list ) ) : ?>
                    <?php foreach ( $list as $item ) :
                        $title   = $item['title'];
                        $text   = $item['text'];?>
                    <div class="faq-item">
                        <div class="faq-item__title"><?php echo $title;?></div>
                        <div class="faq-item__preview">
                            <div class="faq-item__text"><?php echo $text;?></div>
                            <div class="faq-item__full"><?php echo $text;?></div>
                        </div>
                    </div>
                     <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    }

    private function the_9_screen( $screen, $screen_id = false ): void {
        $title = $screen['title'] ?? '';
        $text = $screen['text'] ?? '';
        $form_shortcode = $screen['form_shortcode'] ?? '';
        $list  = $screen['list'] ?? [];
        $home_id = get_option( 'page_on_front' );
        $form_bottom_text = carbon_get_post_meta($home_id, 'form_bottom_text');
        if ( ! is_array( $list ) ) {
            $list = [];
        }
        ?>
        <section class="contact" id="contact">
            <div class="container">
                <div class="contact-wrap">
                    <div class="contact-inner">
                        <div class="title-section"><?php echo $title;?></div>
                        <div class="text-section"><?php echo $text;?> </div>
                        <?php if ( ! empty( $list ) ) : ?>
                        <div class="contact-card">
                            <?php foreach ( $list as $item ) :
                                $icon   = $item['icon'];
                                $icon_url  = wp_get_attachment_image_url( $icon, 'full' );
                                $title   = $item['title'];
                                $link   = $item['link'];
                                $link_text   = $item['link_text'];
                                ?>
                            <div class="contact-item item">
                                <div class="contact-item__media">
                                    <img src=" <?php echo $icon_url;?>" alt="phone">
                                </div>
                                <div class="contact-item__desc">
                                    <h4><?php echo $title;?></h4>
                                    <a href="<?php echo $link;?>" target="_blank"> <?php echo $link_text;?></a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                                <?php endif; ?>
                        <span class="_bg"></span>
                    </div>
                    <div class="contact-inner">
                        <div class="contact-form">
                            <?php echo do_shortcode( $form_shortcode ); ?>
                            <div class="text-form">
                                <?php echo $form_bottom_text ;?>
                            </div>
                        </div>
                        <span class="_bg"></span>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }

    private function the_10_screen( $screen, $screen_id = false ): void {
        $list           = $screen['list'] ?? [];
        $form_shortcode = $screen['form_shortcode'] ?? '';
        ?>
        <section class="seo-text">
            <div class="container">

                <?php if ( ! empty( $list ) && is_array( $list ) ) : ?>
                    <?php foreach ( $list as $item ) :
                        $title = $item['title'] ?? '';
                        $text  = $item['text'] ?? '';
                        ?>
                        <div class="seo-text-item">
                            <?php if ( $title ) : ?>
                                <div class="title-block"><?php echo esc_html( $title ); ?></div>
                            <?php endif; ?>
                            <?php if ( $text ) : ?>
                                <div class="text-section">
                                    <?php echo wp_kses_post( $text ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ( ! empty( $form_shortcode ) ) : ?>
                    <div class="seo-text-form">
                        <?php echo do_shortcode( $form_shortcode ); ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        <?php
    }
    private function the_11_screen( $screen, $screen_id = false ): void {
        $image_id = $screen['image'] ?? '';
        $image    = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
        $title    = $screen['title'] ?? '';
        $text     = $screen['text'] ?? '';
        ?>
        <section class="hero hero-page">
            <div class="container">
                <div class="hero-slider__item">
                    <div class="hero-slider__media">
                        <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="hero-slider__desc">
                        <?php if ( $title ) : ?>
                            <div class="hero-slider__title"><?php echo esc_html( $title ); ?></div>
                        <?php endif; ?>
                        <?php if ( $text ) : ?>
                            <div class="hero-slider__text"><?php echo esc_html( $text ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
    private function the_12_screen( $screen, $screen_id = false ): void {
        $title      = $screen['title'] ?? '';
        $title_text = $screen['title_text'] ?? '';
        $list       = $screen['list'] ?? [];
        ?>
        <section class="payment" id="payment">
            <div class="container">
                <div class="top-section">
                    <?php if ( $title ) : ?>
                        <div class="title-section"><?php echo esc_html( $title ); ?></div>
                    <?php endif; ?>
                    <?php if ( $title_text ) : ?>
                        <div class="title-block"><?php echo esc_html( $title_text ); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $list ) && is_array( $list ) ) : ?>
                    <div class="payment-wrap">
                        <?php foreach ( $list as $item ) :
                            $icon_id = $item['icon'] ?? '';
                            $icon    = $icon_id ? wp_get_attachment_image_url( $icon_id, 'full' ) : '';
                            $ititle  = $item['title'] ?? '';
                            $text    = $item['link'] ?? ''; // тут з CF йде опис
                            ?>
                            <div class="contact-item item">
                                <div class="contact-item__media">
                                    <?php if ( $icon ) : ?>
                                        <img src="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $ititle ); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="contact-item__desc">
                                    <?php if ( $ititle ) : ?>
                                        <h4><?php echo esc_html( $ititle ); ?></h4>
                                    <?php endif; ?>
                                    <?php if ( $text ) : ?>
                                        <p><?php echo esc_html( $text ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        <?php
    }
    private function the_13_screen( $screen, $screen_id = false ): void {
        $title        = $screen['title'] ?? '';
        $list         = $screen['list'] ?? [];

        $bg_id        = $screen['bg_banner'] ?? '';
        $bg_url       = $bg_id ? wp_get_attachment_image_url( $bg_id, 'full' ) : '';

        $img_id       = $screen['image_banner'] ?? '';
        $img_url      = $img_id ? wp_get_attachment_image_url( $img_id, 'full' ) : '';

        $banner_title = $screen['title_banner'] ?? '';
        $banner_text  = $screen['text_banner'] ?? '';
        ?>
        <section class="our-advantages" id="delivery">
            <div class="container">
                <div class="our-advantages__inner item">

                    <?php if ( $title ) : ?>
                        <div class="title-section"><?php echo esc_html( $title ); ?></div>
                    <?php endif; ?>

                    <?php if ( ! empty( $list ) && is_array( $list ) ) : ?>
                        <div class="our-advantages__wrap">
                            <?php foreach ( $list as $item ) :
                                $icon_id = $item['icon'] ?? '';
                                $icon    = $icon_id ? wp_get_attachment_image_url( $icon_id, 'full' ) : '';
                                $ititle  = $item['title'] ?? '';
                                $text    = $item['text'] ?? '';
                                ?>
                                <div class="our-advantages__item">
                                    <div class="our-advantages__media">
                                        <span class="product-item__bg _gray"></span>
                                        <?php if ( $icon ) : ?>
                                            <img src="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $ititle ); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="our-advantages__desc">
                                        <?php if ( $ititle ) : ?>
                                            <h4><?php echo esc_html( $ititle ); ?></h4>
                                        <?php endif; ?>
                                        <?php if ( $text ) : ?>
                                            <p><?php echo esc_html( $text ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="our-advantages__banner"<?php if ( $bg_url ) : ?> style="background-image:url('<?php echo esc_url( $bg_url ); ?>')"<?php endif; ?>>
                        <div class="banner-desc">
                            <?php if ( $banner_title ) : ?>
                                <div class="title-section"><?php echo esc_html( $banner_title ); ?></div>
                            <?php endif; ?>
                            <?php if ( $banner_text ) : ?>
                                <div class="text-section">
                                    <?php echo esc_html( $banner_text ); ?>
                                </div>
                            <?php endif; ?>
                            <!-- Якщо захочеш кнопку — додамо ще поля в CF -->
                        </div>
                        <div class="banner-media">
                            <?php if ( $img_url ) : ?>
                                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $banner_title ); ?>">
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <?php
    }
    private function the_14_screen( $screen, $screen_id = false ): void {
        $gallery = $screen['media_gallery'] ?? [];
        if ( empty( $gallery ) || ! is_array( $gallery ) ) {
            return;
        }
        ?>
        <section class="brand">
            <div class="brand-marquee">
                <div class="brand-marquee__wrap">
                    <?php foreach ( $gallery as $img_id ) :
                        $url = wp_get_attachment_image_url( $img_id, 'medium' );
                        if ( ! $url ) continue;
                        ?>
                        <div class="brand-item">
                            <img src="<?php echo esc_url( $url ); ?>" alt="brand">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="brand-marquee__wrap" aria-hidden="true">
                    <?php foreach ( $gallery as $img_id ) :
                        $url = wp_get_attachment_image_url( $img_id, 'medium' );
                        if ( ! $url ) continue;
                        ?>
                        <div class="brand-item">
                            <img src="<?php echo esc_url( $url ); ?>" alt="brand">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
    private function the_15_screen( $screen, $screen_id = false ): void {
        $title    = $screen['title'] ?? '';
        $text     = $screen['text'] ?? '';
        $image_id = $screen['image'] ?? '';
        $image    = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
        $link_txt = $screen['link'] ?? ''; // зараз як текст кнопки
        ?>
        <section class="about">
            <div class="container">
                <div class="about-inner item">
                    <div class="about-desc">
                        <?php if ( $title ) : ?>
                            <div class="title-section">
                                <?php echo esc_html( $title ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $text ) : ?>
                            <div class="text-section">
                                <?php echo wp_kses_post( $text ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $link_txt ) : ?>
                            <div class="section-link">
                                <a href="#" class="btn-primary btn-popup" data-popup="call"> <?php echo esc_html( $link_txt ); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="about-media">
                        <div class="about-image">
                            <?php if ( $image ) : ?>
                                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }

    private function the_16_screen( $screen, $screen_id = false ): void {
        $title      = $screen['title'] ?? '';
        $title_text = $screen['title_text'] ?? '';
        $list       = $screen['list'] ?? [];
        ?>
        <section class="competitors">
            <div class="container">
                <div class="competitors-inner item">
                    <div class="top-section">
                        <?php if ( $title ) : ?>
                            <div class="title-section"><?php echo esc_html( $title ); ?></div>
                        <?php endif; ?>
                        <?php if ( $title_text ) : ?>
                            <div class="title-block"><?php echo esc_html( $title_text ); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $list ) && is_array( $list ) ) : ?>
                        <div class="competitors-wrap">
                            <?php foreach ( $list as $item ) :
                                $icon_id = $item['icon'] ?? '';
                                $icon    = $icon_id ? wp_get_attachment_image_url( $icon_id, 'full' ) : '';
                                $ititle  = $item['title'] ?? '';
                                $text    = $item['text'] ?? '';
                                ?>
                                <div class="competitors-item">
                                    <div class="competitors-item__bgImage"></div>
                                    <div class="competitors-item__bg"></div>
                                    <div class="competitors-item__media">
                                        <?php if ( $icon ) : ?>
                                            <img src="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $ititle ); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="competitors-item__desc">
                                        <?php if ( $ititle ) : ?>
                                            <div class="title-block"><?php echo esc_html( $ititle ); ?></div>
                                        <?php endif; ?>
                                        <?php if ( $text ) : ?>
                                            <div class="text-section">
                                                <?php echo esc_html( $text ); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>
        <?php
    }
    private function the_17_screen( $screen, $screen_id = false ): void {
        $title = $screen['title'] ?? '';
        $list  = $screen['list'] ?? [];
        ?>
        <section class="original">
            <div class="container">
                <div class="original-inner item">
                    <?php if ( $title ) : ?>
                        <div class="title-section"><?php echo esc_html( $title ); ?></div>
                    <?php endif; ?>

                    <?php if ( ! empty( $list ) && is_array( $list ) ) : ?>
                        <div class="original-wrap">
                            <?php foreach ( $list as $item ) :
                                $ititle      = $item['title'] ?? '';
                                $text        = $item['text'] ?? '';
                                $gallery     = $item['media_gallery'] ?? [];
                                $link_url    = $item['original_link'] ?? '';
                                $link_text   = $item['original_link_text'] ?? '';
                                ?>
                                <div class="original-item">
                                    <?php if ( $ititle ) : ?>
                                        <div class="title-block">
                                            <?php echo esc_html( $ititle ); ?>
                                            <span data-cursor-hover="true"></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $text ) : ?>
                                        <div class="text-section">
                                            <?php echo wp_kses_post( wpautop( $text ) ); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $gallery ) && is_array( $gallery ) ) : ?>
                                        <div class="original-qr">
                                            <?php foreach ( $gallery as $img_id ) :
                                                $url = wp_get_attachment_image_url( $img_id, 'medium' );
                                                if ( ! $url ) continue;
                                                ?>
                                                <div class="original-qr__media">
                                                    <img src="<?php echo esc_url( $url ); ?>" alt="original-qr">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $link_text && $link_url ) : ?>
                                        <div class="original-link">
                                            <a href="<?php echo esc_url( $link_url ); ?>" class="link-ytb" target="_blank">
                                                <span><?php echo esc_html( $link_text ); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>
        <?php
    }

    private function the_18_screen( $screen, $screen_id = false ): void {
        $title    = $screen['title'] ?? '';
        $text     = $screen['text'] ?? '';
        ?>

        <?php
    }
	private function the_screen_default( $screen, $screen_id = false ) {
		$this_is_gray   = $screen['this_is_gray'] ? '#F2F7F6' : '#ffffff';
		$background     = $screen['background_color'] ?: $this_is_gray;
		$slug           = $screen['slug'];
		$is_row_reverse = $screen['is_row_reverse'];
		$cls            = $is_row_reverse ? 'default-section-row--reverse' : '';
		?>
        <section class="section default-section <?php echo $screen['this_is_gray'] ? 'section-grey' : ''; ?>"
                 style="background-color:<?php echo $background ?>;" id="<?php echo $screen_id ?>">
            <div class="container">
                <div class="default-section-row <?php echo $cls ?>">
                    <div class="default-section__image ">
                        <img class="cover" src="<?php _u( $screen['image'] ) ?>" alt="">
                    </div>
                    <div class="default-section-content">
						<?php $this->the__slug( $slug, 'about__tag' ); ?>
                        <div class="title default-section__title">
							<?php _t( $screen['title'] ) ?>
                        </div>
                        <div class="default-section__text text">
							<?php _t( $screen['text'] ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

		<?php
	}

	private function the_screen_price_list( $screen, $screen_id = false ) {
		$this_is_gray = $screen['this_is_gray'] ? '#F2F7F6' : '#ffffff';
		$background   = $screen['background_color'] ?: $this_is_gray;
		$slug         = $screen['slug'];
		$list         = $screen['list'];
		$currency     = $this->currency;
		?>
        <section class="section price-list-section <?php echo $screen['this_is_gray'] ? 'section-grey' : ''; ?>"
                 style="background-color:<?php echo $background ?>;" id="<?php echo $screen_id ?>">
            <div class="container">

                <div class="price-list-section-head">
                    <div class="price-list-section-head-column">
						<?php $this->the__slug( $slug, 'services__tag' ); ?>
                        <div class="services__title title">
							<?php _t( $screen['title'] ) ?>
                        </div>
                    </div>

                    <div class="services__subtitle subtitle text">
						<?php _t( $screen['text'] ) ?>
                    </div>
                </div>
				<?php if ( $list ): foreach ( $list as $service ): $_id = $service['id'];
					if ( get_post( $_id ) ):
						if ( $p_list = carbon_get_post_meta( $_id, 'service_price_list' ) ):
							?>
                            <div class="price-list-box">
                                <div class="price-list-box__title" style="text-transform: uppercase">
									<?php echo get_the_title( $_id ) ?>
                                </div>
                                <ul class="price-list">
									<?php foreach ( $p_list as $item ): ?>
                                        <li class="price-list-item">
                                            <span><?php echo $item['title'] ?></span>
                                            <span class="line"></span>
                                            <div class="price-list-item__value">
												<?php echo $currency . $item['price'] ?>
                                            </div>
                                        </li>
									<?php endforeach; ?>
                                </ul>
                            </div>
						<?php endif; endif; endforeach; endif; ?>
            </div>
        </section>
		<?php
	}
    private function the_screen_calc( $screen, $screen_id = false ) {
		$this_is_gray = $screen['this_is_gray'] ? '#F2F7F6' : '#ffffff';
		$background   = $screen['background_color'] ?: $this_is_gray;
		$slug         = $screen['slug'];
		$lists         = $screen['steps'];
		$currency     = $this->currency;
		?>
        <section class="section price-list-section <?php echo $screen['this_is_gray'] ? 'section-grey' : ''; ?>"
                 style="background-color:<?php echo $background ?>;" id="<?php echo $screen_id ?>">
            <div class="container">
            <?php if ( $lists ):  $i = 1; ?>     
            <div class="head-calculate ">
            <form id="calculateForm">
            <div class="calc-wrap">
                <?php foreach ( $lists as $index => $item ):
                    $index = $index + 1;
                    ?>                
                    <?php if ($item['_type'] == 'step_block') : 
                        $bottomText =  $item['info_text'];
                        $bottomNext =  $item['next_step'];
                        ?>
                    <div class="head-calculate-step calc-item <?php if($i === 1){echo 'active';}?>" data-step="<?php echo $item['item_slug'] ?>" data-next-step="<?php echo $bottomNext; ?>">
                        <div class="head-calculate-row">
                            <div class="head-calculate__title"><?php if($i === 1):else:?>
                                <a href="#" class="head-calculate__back">
                                    <img src="/wp-content/themes/warehouse/assets/img/calc-back.svg" alt="calc-back">
                                </a>
                            <?php
                            endif;?><h2><?php echo $item['title_step']; ?></h2></div>
                            <div class="head-calculate-header"><ul class="navigation-steps"><li class="head-calculate-header__item"><a href="#" data-href-step="inbound">Inbound</a></li></ul></div>  
                        </div>
                        <div class="form"> 
                            <?php $list_inner = $item['form_item']; if ( $list_inner ): foreach ( $list_inner as $item ):?>        
                                <?php  $field_type = $item['select_field_type']; $value_item = $item['value_item']; ?>
                                <?php if ( $field_type === 'number' ): ?>
                                    <label class="form-label third">
                                        <span class="form-label-head">
                                            <?php if($item['icon']){?><span class="icon"><img src="<?php _u( $item['icon'] ) ?>" alt="icon"></span><?php } ?>
                                            <span class="form-label-res"><?php echo $item['title']; ?></span>
                                            <?php if($item['label_info']){?>
                                            <span class="form-label-hint">
                                                <span class="form-label-hint__icon"><img src="/wp-content/themes/warehouse/assets/img/hint.svg" alt=""></span>
                                                <span class="form-label-hint__text"><?php echo $item['label_info']; ?></span>
                                            </span>
                                            <?php } ?>
                                        </span>
                                        <span class="form-label-container">
                                            <input type="number" step="1" min="0" class="number-input" name="inbound_box"
                                            <?php if($item['multiply']){echo 'data-calc-type="multiply"';}?>
                                            
                                            <?php if($item['ranges']){echo 'data-ranges="'.$item['ranges_item'].'"';}?> data-price="<?php echo $item['value_item']; ?>" placeholder="">
                                            <a href="#" class="plus number-input-button"><img src="/wp-content/themes/warehouse/assets/img/arrow_drop_down.svg" alt=""></a>
                                            <a href="#" class="minus number-input-button"><img src="/wp-content/themes/warehouse/assets/img/arrow_drop_up.svg" alt=""></a>
                                        </span>
                                    </label>
                                <?php elseif ( $field_type === 'radio' ): ?>
                                    <label class="form-label third">
                                        <span class="form-checkbox-wrapper">
                                            <input type="radio" name="service" value="<?php echo $value_item;?>">
                                            <span class="icon"><img src="/wp-content/themes/warehouse/assets/img/ckeck.svg" alt=""></span>
                                            <?php echo $item['title']; ?>
                                        </span>
                                    </label>
                                <?php elseif ( $field_type === 'text' ): ?>
                                    <input type="text" name="custom_field" placeholder="Enter text" value="<?php echo esc_attr( $value_item ); ?>">
                                <?php elseif ( $field_type === 'checkbox' ): ?>
                                    <label class="form-label third">
                                        <span class="form-checkbox-wrapper">
                                            <input type="checkbox" name="storage" value="<?php echo $value_item;?>">
                                            <span class="icon"><img src="/wp-content/themes/warehouse/assets/img/ckeck.svg" alt=""></span>
                                            <?php echo $item['title']; ?>
                                        </span>
                                    </label>
                                <?php elseif ( $field_type === 'select' ): ?>
                                        <select name="monts"><option value="0">14 days</option><option value="1">1 monts</option><option value="2">2 monts</option><option value="3">3 monts</option><option value="4">4 monts</option><option value="5">5 monts</option><option value="6">6 monts</option><option value="7">7 monts</option><option value="8">8 monts</option><option value="9">9 monts</option><option value="10">10 monts</option><option value="11">11 monts</option><option value="12">12 monts</option></select>
                                <?php endif; ?>
                            <?php endforeach; endif; ?>
                        </div>
                        <div class="head-calculate-footer">
                            <div class="head-calculate-footer__text"><?php echo $bottomText; ?></div>
                            <button type="button" class="nextStep button head-calculate-footer__button" data-next="<?php echo $bottomNext; ?>">Next</button>
                        </div> 
                    </div>    
                <?php elseif ($item['_type'] == 'step_block_storage') :
                    $bottomText =  $item['info_text'];
                    $bottomNext =  $item['next_step'];
                     ?>
                    <div class="head-calculate-step calc-item" data-step="<?php echo $item['item_slug'] ?>" data-next-step="<?php echo $bottomNext; ?>">
                        <div class="head-calculate-row">
                            <div class="head-calculate__title"><a href="#" class="head-calculate__back">
                                    <img src="/wp-content/themes/warehouse/assets/img/calc-back.svg" alt="calc-back">
                                </a><h2><?php echo $item['title_step']; ?></h2></div>
                            <div class="head-calculate-header"><ul class="navigation-steps"></ul></div>  
                        </div>
                        
                        <div class="form"> 
                            <?php $list_inner = $item['form_item']; if ( $list_inner ): foreach ( $list_inner as $item ):?>        
                                <?php  $field_type = $item['select_field_type']; $value_item = $item['value_item']; ?>
                                <?php if ( $field_type === 'number' ): ?>
                                    <label class="form-label third">
                                        <span class="form-label-head">
                                            <?php if($item['icon']){?><span class="icon"><img src="<?php _u( $item['icon'] ) ?>" alt="icon"></span><?php } ?>
                                            <span class="form-label-res"><?php echo $item['title']; ?></span>
                                            <?php if($item['label_info']){?>
                                            <span class="form-label-hint">
                                                <span class="form-label-hint__icon"><img src="/wp-content/themes/warehouse/assets/img/hint.svg" alt=""></span>
                                                <span class="form-label-hint__text"><?php echo $item['label_info']; ?></span>
                                            </span>
                                            <?php } ?>
                                        </span>
                                        <span class="form-label-container">
                                            <?php if(!$item['readonly']):  ?>
                                                <input type="number" step="1" min="0"  class="number-input" name="box" placeholder="<?php echo $item['value_item']; ?>">
                                                <a href="#" class="plus number-input-button"><img src="/wp-content/themes/warehouse/assets/img/arrow_drop_down.svg" alt=""></a>
                                                <a href="#" class="minus number-input-button"><img src="/wp-content/themes/warehouse/assets/img/arrow_drop_up.svg" alt=""></a>
                                            <?php else:?>
                                                <input type="number" step="1" min="0" placeholder="<?php echo $item['value_item']; ?>" value="<?php echo $item['value_item']; ?>"  readonly>
                                            <?php  endif; ?>
                                        </span>
                                    </label>
                                <?php elseif ( $field_type === 'radio' ): ?>
                                    <label class="form-label third">
                                        <span class="form-checkbox-wrapper">
                                            <input type="radio" name="service" value="<?php echo $value_item;?>">
                                            <span class="icon"><img src="/wp-content/themes/warehouse/assets/img/ckeck.svg" alt=""></span>
                                            <?php echo $item['title']; ?>
                                        </span>
                                    </label>
                                <?php elseif ( $field_type === 'text' ): ?>
                                    <input type="text" name="custom_field" placeholder="Enter text" value="<?php echo esc_attr( $value_item ); ?>">
                                <?php elseif ( $field_type === 'checkbox' ): ?>
                                    <label class="form-label third">
                                        <span class="form-checkbox-wrapper">
                                            <input type="checkbox" name="storage" value="<?php echo $value_item;?>">
                                            <span class="icon"><img src="/wp-content/themes/warehouse/assets/img/ckeck.svg" alt=""></span>
                                            <?php echo $item['title']; ?>
                                        </span>
                                    </label>
                                <?php elseif ( $field_type === 'select' ): ?>
                                    <label class="form-label third">
                                        <span class="form-label-head">
                                            <?php if($item['icon']){?><span class="icon"><img src="<?php _u( $item['icon'] ) ?>" alt="icon"></span><?php } ?>
                                            <span class="form-label-res"><?php echo $item['title']; ?></span>
                                            <?php if($item['label_info']){?>
                                            <span class="form-label-hint">
                                                <span class="form-label-hint__icon"><img src="/wp-content/themes/warehouse/assets/img/hint.svg" alt=""></span>
                                                <span class="form-label-hint__text"><?php echo $item['label_info']; ?></span>
                                            </span>
                                            <?php } ?>
                                        </span>
                                        <span class="form-checkbox-wrapper monts-select">
                                            <select name="monts"><option value="0">14 days</option><option value="1">1 monts</option><option value="2">2 monts</option><option value="3">3 monts</option><option value="4">4 monts</option><option value="5">5 monts</option><option value="6">6 monts</option><option value="7">7 monts</option><option value="8">8 monts</option><option value="9">9 monts</option><option value="10">10 monts</option><option value="11">11 monts</option><option value="12">12 monts</option></select>
                                        </span> 
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; endif; ?>
                        </div>
                        <div class="head-calculate-footer">
                            <div class="head-calculate-footer__text"><?php echo $bottomText; ?></div>
                            <button type="button" class="nextStep button head-calculate-footer__button" data-next="<?php echo $bottomNext; ?>">Next</button>
                        </div>   
                    </div>
                <?php elseif ($item['_type'] == 'step_result') : ?>
                    <div class="head-calculate-step calc-item" data-step="<?php echo $item['item_slug'] ?>">
                            <div class="head-calculate-row">
                                <div class="head-calculate__title"><a href="#" class="head-calculate__back">
                                <img src="/wp-content/themes/warehouse/assets/img/calc-back.svg" alt="calc-back">
                            </a><h2><?php echo $item['title_step']; ?></h2></div>
                                <div class="head-calculate-header"><ul class="navigation-steps"><li class="head-calculate-header__item"><a href="#" data-href-step="inbound">Inbound</a></li></ul></div>  
                            </div>
                        
                        <ul class="list-result head-calculate-result"></ul>
                        <div class="form">
                            <?php $list_inner = $item['form_item']; if ( $list_inner ): foreach ( $list_inner as $item ):?>  
                                <label class="form-label third">
                                    <span class="form-label-head">
                                        <?php if($item['icon']){?><span class="icon"><img src="<?php _u( $item['icon'] ) ?>" alt="icon"></span><?php } ?>
                                        <span class="form-label-res"><?php echo $item['title']; ?></span>
                                    </span>
                                    <span class="form-label-container">
                                        <input type="<?php echo $item['field_type']; ?>" name="<?php echo $item['field_type']; ?>" placeholder="<?php echo $item['title']; ?>" required>
                                    </span>
                                </label>
                            <?php endforeach; endif; ?>
                            <input type="hidden" name="totalResult">
                            <div class="form-label third user-info">
                                <button class="button-send" type="submit">Send</button>
                            </div>
                            <div class="checkbox-group ">
                                <label class="checkbox-wrapper">
                                    <input name="Privacy_Policy[]" data-required="" style="display:none;" value=" By click “Send” you accept Privacy Policy" type="checkbox" checked>
                                    <span class="checkbox-icon"></span>
                                    <span class="checkbox-text">By click “Send” you accept Privacy Policy</span>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php $i++; endforeach; ?>
            </div>
            <div class="result" style="opacity:0;"><label><span class="result-label" style="display:none;">Total : $</span><input type="text" name="result" class="result" value="" readonly></label></div>
        </form>
        </div>
        <?php endif; ?>
            </div>
            <style>
                .calc-item.active{display: block;}
                input.result{position: absolute;bottom: 0;width: auto;height: auto;background: transparent;color: #fff;border: 0;right: 14rem;left: auto;padding: 0;z-index: -1;}
                .head-calculate .form {-webkit-box-pack: start;-ms-flex-pack: start;justify-content: flex-start;}
                .navigation-steps{display:-webkit-box;display:-ms-flexbox;display:flex;  counter-reset: my-awesome-counter;}
                .head-calculate-header__item{  counter-increment: my-awesome-counter;}
                .head-calculate-header__item:before{content: counter(my-awesome-counter);-webkit-box-pack: center;-ms-flex-pack: center;-webkit-box-flex: 0;-ms-flex-positive: 0;-webkit-box-align: center;-ms-flex-align: center;align-items: center;background-color: rgba(0, 103, 67, 0); border: 1px solid #fff; border-radius: 50%; color: #fff; display: -webkit-box; display: -ms-flexbox;display: flex; flex-grow: 0; -ms-flex-negative: 0; flex-shrink: 0; font-size: 1.8rem; font-style: normal; font-weight: 400; height: 3.2rem; justify-content: center; line-height: 100%; margin-right: .5rem; padding: 2px; text-align: center; -webkit-transition: .5s ease-in-out; -o-transition: .5s ease-in-out; transition: .5s ease-in-out; vertical-align: center; width: 3.2rem;}
                .head-calculate-header__item.done:before{padding: 0;background-position: center;background-size: cover;font-size: 0;border-color: #006743;background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='33' viewBox='0 0 32 33' fill='none'%3E%3Cg id='Group 288'%3E%3Ccircle id='Ellipse 53' cx='16' cy='16.5' r='16' fill='%23006743'/%3E%3Cg id='check_small'%3E%3Cmask id='mask0_512_8549' style='mask-type:alpha' maskUnits='userSpaceOnUse' x='6' y='6' width='20' height='21'%3E%3Crect id='Bounding box' x='6' y='6.5' width='20' height='20' fill='%23D9D9D9'/%3E%3C/mask%3E%3Cg mask='url(%23mask0_512_8549)'%3E%3Cpath id='check_small_2' d='M13.6667 21.5L9 17.2447L10.6333 15.7553L13.6667 18.5213L21.3667 11.5L23 12.9894L13.6667 21.5Z' fill='white'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
                .head-calculate-header__item.active:before{background-color: #006743;border-color: #006743;color: #fff;}
                .calc-item input[type="number"]::-webkit-outer-spin-button,
                .calc-item input[type="number"]::-webkit-inner-spin-button {-webkit-appearance: none;margin: 0;}
                .calc-item input[type="number"] {-moz-appearance: textfield;}
                .calc-wrap .head-calculate-result{max-width: 100%;margin-bottom: 3rem;}
                .calc-wrap .checkbox-group {margin-top: 2rem; margin-bottom: 3rem;}
                .calc-item select{background: #fff;border: 1px solid hsla(0, 0%, 100%, .2);border-radius: 1rem;color: #000;-ms-flex-negative: 0;flex-shrink: 0;font-family: Inter, serif;font-size: 1.8rem;font-style: normal;font-weight: 400;line-height: 160%;width: 100%;outline: none;}
                .result.is-active .result-label{display: inline-block !important; color:#fff;text-transform: uppercase;}
                .result.is-active input.result{position: relative;right: 0;bottom: 0;}
                .calc-wrap .button-send{width: 100%;padding: 1.58rem;}
                div.result{position: relative;z-index: -1;}
                .result.is-active label{display: -webkit-box;display: -ms-flexbox;display: flex;min-width: auto;-webkit-box-align: center;-ms-flex-align: center;align-items: center;-webkit-box-pack: end;-ms-flex-pack: end;justify-content: flex-end;}
                .price-list-section{position: relative;}
                .head-calculate-footer>:not(:last-child) {margin-right: 19rem;}
                .price-list-section:after{position: absolute;content: '';width: 100%;height: 50%;top: 0;left: 0;background-color:#F7F7F7;}
                .head-section:after{display: none;}
                .popup {position: fixed;display: -webkit-box;display: -ms-flexbox;display: flex;width: 100%;height: 100vh;-webkit-box-orient: vertical;-webkit-box-direction: normal;    -ms-flex-direction: column;        flex-direction: column;-webkit-box-pack: center;    -ms-flex-pack: center;        justify-content: center;left: -150%;visibility: hidden;-webkit-transition: all ease 300ms;-o-transition: all ease 300ms;transition: all ease 300ms;z-index: -1;text-align: center;}
                .popup.active {top: 0;left: 0;opacity: 1;visibility: visible;z-index: 150;}
                .popup-wrap {position: relative;max-width: 60rem;width: 100%;text-align: left;background: #ffffff;margin: 0 auto;padding: 4rem;z-index: 121;height: auto;border-radius: 4rem;-webkit-box-shadow: 0px 0px 32.9374px rgba(0, 50, 111, 0.1);        box-shadow: 0px 0px 32.9374px rgba(0, 50, 111, 0.1);}
                .popup-close {position: absolute;right: 3rem;top: 3rem;cursor: pointer;width: 20px;height: 20px;}
                .popup-close:before, .popup-close:after {position: absolute;content: "";top: 50%;margin-top: .1rem;left: 0;width: 100%;height: .1rem;background: #000000;}
                .popup-close:before {-webkit-transform: rotate(45deg);    -ms-transform: rotate(45deg);        transform: rotate(45deg);}
                .popup-close:after {-webkit-transform: rotate(-45deg);    -ms-transform: rotate(-45deg);        transform: rotate(-45deg);}
                .popup-overlay {position: fixed;z-index: -1;top: 0;left: 0;width: 100%;height: 100%;background: rgba(0, 0, 0, 0.3);}
                .popup .title, .popup .text, .popup .image{ margin-bottom: 2rem; text-align: center;}
                .popup .link button{margin: 0 auto;}
                .popup .image {text-align: center;}
                .popup .image img{width: 10rem;height: 10rem;-o-object-fit: cover;object-fit: cover;}
                .calc-item .form-checkbox-wrapper.monts-select{padding: 1.2rem 1rem;}
                .calc-item select{
                    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')
      !important;
    background-repeat: no-repeat, repeat !important;
    background-position: right .7em top 50%, 0 0 !important;
    background-size: .65em auto, 100% !important;
    border: none;
    border-radius: 0px;
    
                }
            </style>
            <div class="popup " data-popup="video">
                <div class="popup-wrap">
                    <div class="popup-close"></div>
                    <div class="image">
                        <img src="/wp-content/themes/warehouse/assets/img/calc-check.svg" alt="check">
                    </div>
                    <div class="title">Your order has been accepted!</div>
                    <div class="text">Your order has been successfully received and is currently being processed. We will contact you shortly to clarify the details.</div>
                    <div class="link"><button>OK</button></div>
                </div>    
                <div class="popup-overlay"></div>
            </div>
        </section>
		<?php
	}   


	private function the__slug( $slug, $cls = '', $main_cls = ' tag ' ): void {
		if ( $slug ): ?>
            <div class=" <?php echo $cls . $main_cls; ?>">
				<?php echo $slug; ?>
            </div>
			<?php
			return;
		endif;
		echo '';
	}

    public function the_screens( ?array $screens = null, array $only_types = [] ): void {
        if ( $screens === null ) {
            $screens = $this->screens;
        }

        if ( $screens ):
            $var                  = $this->variables;
            $set                  = $var['setting_home'];
            $assets               = $var['assets'];
            $url                  = $var['url'];
            $url_home             = $var['url_home'];
            $screen_id_collection = array();
            $services_collection  = array();

            foreach ( $screens as $screen_index => $screen ) {
                $screen_off = $screen['screen_off'] ?? false;
                $screen_id  = $screen['id'] ?? '';

                if ( in_array( $screen_id, $screen_id_collection, true ) ) {
                    $screen_id .= $screen_index;
                }
                $screen_id_collection[] = $screen_id;

                if ( $screen_off ) {
                    continue;
                }

                if ( $only_types && ! in_array( $screen['_type'] ?? '', $only_types, true ) ) {
                    continue;
                }

                if ( $screen['_type'] == 'screen_1' ) {
                    $this->the_first_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_2' ) {
                    $this->the_2_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_3' ) {
                    $this->the_3_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_4' ) {
                    $this->the_4_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_5' ) {
                    $this->the_5_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_6' ) {
                    $this->the_6_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_7' ) {
                    $this->the_7_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_8' ) {
                    $this->the_8_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_8_1' ) {
                    $this->the_8_1_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_9' ) {
                    $this->the_9_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_10' ) {
                    $this->the_10_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_11' ) {
                    $this->the_11_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_11_1' ) {
                    $this->the_11_1_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_12' ) {
                    $this->the_12_screen( $screen, $screen_id, 'main__text' );
                } elseif ( $screen['_type'] == 'screen_12_1' ) {
                    $this->the_12_1_screen( $screen, $screen_id, 'main__text' );
                } elseif ( $screen['_type'] == 'screen_12_2' ) {
                    $this->the_12_2_screen( $screen, $screen_id, 'main__text' );
                } elseif ( $screen['_type'] == 'screen_12_4' ) {
                    $this->the_12_4_screen( $screen, $screen_id, 'main__text' );
                } elseif ( $screen['_type'] == 'screen_13' ) {
                    $this->the_13_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_14' ) {
                    $this->the_14_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_15' ) {
                    $this->the_15_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_16' ) {
                    $this->the_16_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_17' ) {
                    $this->the_17_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_18' ) {
                    $this->the_18_screen( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_default' ) {
                    $this->the_screen_default( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_price_list' ) {
                    $this->the_screen_price_list( $screen, $screen_id );
                } elseif ( $screen['_type'] == 'screen_calc' ) {
                    $this->the_screen_calc( $screen, $screen_id );
                }
            }

            if ( ! empty( $services_collection ) ) {
                $this->the_services_modals( $services_collection );
            }
        endif;
    }

    public function the_home_tail_screens(): void {

        if ( empty( $this->screens ) || ! is_array( $this->screens ) ) {
            return;
        }

        $this->the_screens(
            $this->screens,
            array(
                'screen_7',
                'screen_8',
                'screen_9',
                'screen_10',
            )
        );
    }
    public function the_home_product_screens(): void {

        if ( empty( $this->screens ) || ! is_array( $this->screens ) ) {
            return;
        }

        $this->the_screens(
            $this->screens,
            array(
                'screen_7',
                'screen_8',
                'screen_10',
            )
        );
    }
}

