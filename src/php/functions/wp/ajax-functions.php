<?php

// add_action( 'wp_ajax_nopriv_get_events_dates', 'get_events_dates' );
// add_action( 'wp_ajax_get_events_dates', 'get_events_dates' );

// function get_events_dates() {
// 	$month = $_POST['month'] ?? '';
// 	$year  = $_POST['year'] ?? '';
// 	$res   = array(
// 		'dates'  => array(),
// 		'_dates' => array(),
// 	);
// 	$args  = array(
// 		'post_type'      => 'event',
// 		'post_status'    => 'publish',
// 		'posts_per_page' => - 1,
// 	);
// 	if ( $month && $year ) {
// 		$month              = (int) $month;
// 		$year               = (int) $year;
// 		$m                  = $month < 10 ? '0' . $month : $month;
// 		$res['calendar']    = $month . '-' . $year;
// 		$string             = $year . '-' . $m . '-';
// 		$args['meta_query'] = array(
// 			array(
// 				'key'     => '_crb_event_start_date',
// 				'value'   => sanitize_text_field( $string ),
// 				'compare' => 'LIKE',
// 				'type'    => 'CHAR'
// 			)
// 		);
// 	} else {
// 		$res['calendar'] = 'all';
// 	}
// 	$query = new WP_Query( $args );
// 	if ( $query->have_posts() ) {
// 		while ( $query->have_posts() ) {
// 			$query->the_post();
// 			$id   = get_the_ID();
// 			$date = carbon_get_post_meta( $id, 'crb_event_start_date' );
// 			if ( $date ) {
// 				$arr = explode( '-', $date );
// 				if ( $arr ) {
// 					$y      = (int) $arr[0];
// 					$m      = (int) $arr[1];
// 					$d      = (int) $arr[2];
// 					$string = $d . '-' . $m . '-' . $y;
// 					if ( ! in_array( $string, $res['dates'] ) ) {
// 						$res['dates'][] = $string;
// 					}
// 				}
// 			}
// 		}
// 	}
// 	wp_reset_postdata();
// 	wp_reset_query();
// 	echo json_encode( $res );
// 	die();
// }
// functions.php
if ( ! function_exists( 'render_product_item' ) ) {
    function render_product_item( $post_id = null ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        $img  = get_the_post_thumbnail_url( $post_id, 'medium' );
        $name = get_the_title( $post_id );
        $link = get_permalink( $post_id );

        // своя ціна / sku з мети (можеш поміняти ключі)
        $price = carbon_get_post_meta( get_the_ID(), 'price' );
        $sku   = carbon_get_post_meta( $post_id, 'sku' );
        ?>
        <div class="product-item__wrap">
            <div class="product-item" data-cursor-hover="true">
                <div class="product-item__media">
                    <span class="product-item__bg _gray"></span>
                    <?php if ( $img ) : ?>
                        <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $name ); ?>">
                    <?php endif; ?>
                </div>
                <div class="product-item__desc">
                    <?php if ( $price ) : ?>
                        <div class="product-item__price">$ <?php echo  $price ; ?></div>
                    <?php endif; ?>
                    <div class="product-item__text">
                        <?php echo esc_html( $name ); ?>
                    </div>
                    <?php if ( $sku ) : ?>
                        <div class="product-item__sku">SKU: <?php echo  $sku ; ?></div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo esc_url( $link ); ?>" class="product-item-link"></a>
            </div>
            <div class="product-item__link">
                <button type="button" class="btn-primary btn-popup" data-popup="call">
                    <?php pll_e( 'CONTACT MANAGER' ); ?>
                </button>
            </div>
        </div>
        <?php
    }
}



// AJAX handler
add_action( 'wp_ajax_load_products', 'puffspot_ajax_load_products' );
add_action( 'wp_ajax_nopriv_load_products', 'puffspot_ajax_load_products' );

function puffspot_ajax_load_products_www() {
    check_ajax_referer( 'my_products_nonce', 'nonce' );

    $page      = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;
    $sort      = isset( $_POST['sort'] ) ? sanitize_text_field( $_POST['sort'] ) : 'date_desc';
    $source_id = isset( $_POST['source_id'] ) ? (int) $_POST['source_id'] : 0;
    $lang      = ! empty( $_POST['lang'] ) ? sanitize_text_field( $_POST['lang'] ) : '';

    $per_page = 8;

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    );

    if ( function_exists( 'pll_current_language' ) ) {
        $args['lang'] = $lang ? $lang : pll_current_language( 'slug' );
    }

    $assoc = carbon_get_post_meta( $source_id, 'crb_association' );
    if ( ! empty( $assoc ) && is_array( $assoc ) ) {
        $product_ids = array();
        foreach ( $assoc as $item ) {
            if ( ! empty( $item['id'] ) ) {
                $product_ids[] = (int) $item['id'];
            }
        }
        if ( $product_ids ) {
            $args['post__in'] = $product_ids;
            $args['orderby']  = 'post__in';
        }
    }

    // 🔹 УНІВЕРСАЛЬНІ ТАКСОНОМІЧНІ ФІЛЬТРИ
    $tax_query  = array();
    $tax_filters = isset( $_POST['tax'] ) && is_array( $_POST['tax'] ) ? $_POST['tax'] : array();

    foreach ( $tax_filters as $tax_name => $term_slug ) {
        $tax_name  = sanitize_key( $tax_name );
        $term_slug = sanitize_text_field( $term_slug );

        if ( ! $tax_name || ! $term_slug ) {
            continue;
        }

        $tax_query[] = array(
            'taxonomy' => $tax_name,
            'field'    => 'slug',
            'terms'    => $term_slug,
        );
    }

    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    // Сортування – як у тебе було
    switch ( $sort ) {
        case 'price_asc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;
        case 'price_desc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'title_asc':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;
        case 'title_desc':
            $args['orderby'] = 'title';
            $args['order']   = 'DESC';
            break;
        case 'date_asc':
            $args['orderby'] = 'date';
            $args['order']   = 'ASC';
            break;
        case 'date_desc':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    $q = new WP_Query( $args );

    ob_start();
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            render_product_item();
        }
        wp_reset_postdata();
    }
    else {?>
    <div class="search-form__wrap">
        <p><?php pll_e( 'Not found' ); ?></p>
    </div>
    <?php
    }
    $html      = ob_get_clean();
    $max_pages = (int) $q->max_num_pages;

    wp_send_json_success( array(
        'html'      => $html,
        'max_pages' => $max_pages,
        'current'   => $page,
    ) );
}
function puffspot_ajax_load_products() {
    check_ajax_referer( 'my_products_nonce', 'nonce' );

    $page      = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;
    $sort      = isset( $_POST['sort'] ) ? sanitize_text_field( $_POST['sort'] ) : 'date_desc';
    $source_id = isset( $_POST['source_id'] ) ? (int) $_POST['source_id'] : 0;
    $lang      = ! empty( $_POST['lang'] ) ? sanitize_text_field( $_POST['lang'] ) : '';
    $per_page  = isset( $_POST['per_page'] ) ? max( 1, (int) $_POST['per_page'] ) : 8;
    $mode      = ! empty( $_POST['mode'] ) ? sanitize_text_field( $_POST['mode'] ) : 'catalog';

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    );

    if ( function_exists( 'pll_current_language' ) ) {
        $args['lang'] = $lang ? $lang : pll_current_language( 'slug' );
    }

    $tax_query   = array();
    $tax_filters = isset( $_POST['tax'] ) && is_array( $_POST['tax'] ) ? $_POST['tax'] : array();

    // 🔹 MODE
    switch ( $mode ) {
        case 'hits':
            $tax_query[] = array(
                'taxonomy' => 'ptype',
                'field'    => 'slug',
                'terms'    => 'hits',
            );
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;

        case 'manual':
            $manual_ids = array();

            if ( ! empty( $_POST['manual_ids'] ) ) {
                $manual_ids_raw = explode( ',', $_POST['manual_ids'] );
                foreach ( $manual_ids_raw as $mid ) {
                    $mid = (int) $mid;
                    if ( $mid > 0 ) {
                        $manual_ids[] = $mid;
                    }
                }
            }

            if ( $manual_ids ) {
                $args['post__in'] = $manual_ids;
                $args['orderby']  = 'post__in';
            } else {
                $args['post__in'] = array( 0 );
            }
            break;

        case 'newest':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;

        case 'catalog':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    // 🔹 Таксономічні фільтри
    foreach ( $tax_filters as $tax_name => $term_slug ) {
        $tax_name  = sanitize_key( $tax_name );
        $term_slug = sanitize_text_field( $term_slug );

        if ( ! $tax_name || ! $term_slug ) {
            continue;
        }

        $tax_query[] = array(
            'taxonomy' => $tax_name,
            'field'    => 'slug',
            'terms'    => $term_slug,
        );
    }

    if ( ! empty( $tax_query ) ) {
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    }

    // 🔹 Сортування
    switch ( $sort ) {
        case 'price_asc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;
        case 'price_desc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'title_asc':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;
        case 'title_desc':
            $args['orderby'] = 'title';
            $args['order']   = 'DESC';
            break;
        case 'date_asc':
            $args['orderby'] = 'date';
            $args['order']   = 'ASC';
            break;
        case 'date_desc':
        default:
            // залишаємо як є
            break;
    }

    $q = new WP_Query( $args );

    ob_start();
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            render_product_item();
        }
        wp_reset_postdata();
    } else {
        ?>
        <div class="search-form__wrap">
            <p><?php pll_e( 'Not found' ); ?></p>
        </div>
        <?php
    }

    $html      = ob_get_clean();
    $max_pages = (int) $q->max_num_pages;

    wp_send_json_success( array(
        'html'      => $html,
        'max_pages' => $max_pages,
        'current'   => $page,
    ) );
}


add_action( 'wp_ajax_nopriv_ajax_products_search', 'ajax_products_search' );
add_action( 'wp_ajax_ajax_products_search', 'ajax_products_search' );

function ajax_products_search() {
    $keyword = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    $paged   = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;

    $per_page = 4;

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $keyword,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) :
        echo '<div class="search-form__wrap">';

        while ($query->have_posts()) :
            $query->the_post();

            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            if (!$thumb) {
                // запасне зображення, якщо хочеш
                $thumb = get_template_directory_uri() . '/assets/images/placeholder.png';
            }
            ?>
            <div class="search-product__item">
                <div class="search-product__media">
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                </div>
                <?php
                $keyword = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
                $title   = get_the_title();

                if ($keyword) {
                    // Регулярка без регістра, з екрануванням спецсимволів
                    $escaped = preg_quote($keyword, '/');
                    $title = preg_replace('/(' . $escaped . ')/i', '<b>$1</b>', $title);
                }
                ?>

                <div class="search-product__desc">
                    <h4><?php echo $title; ?></h4>
                </div>
                <a href="<?php the_permalink(); ?>" class="search-product__link"></a>
            </div>
        <?php
        endwhile;

        echo '</div>'; // .search-form__wrap

        // Пагінація
        $total_pages = $query->max_num_pages;

        if ($total_pages > 1) :
            echo '<div class="pagination"><ul>';

            // prev
            if ($paged > 1) {
                echo '<li>
                    <a href="#" class="prev js-search-page" data-page="' . esc_attr($paged - 1) . '">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L7 9.5L12 15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </li>';
            }

            // номери сторінок
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $paged) {
                    echo '<li><span class="active">' . $i . '</span></li>';
                } else {
                    echo '<li><a href="#" class="js-search-page" data-page="' . esc_attr($i) . '">' . $i . '</a></li>';
                }
            }

            // next
            if ($paged < $total_pages) {
                echo '<li>
                    <a href="#" class="next js-search-page" data-page="' . esc_attr($paged + 1) . '">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 4L12 9.5L7 15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </li>';
            }

            echo '</ul></div>';
        endif;

    else :
        ?>
        <div class="search-form__wrap">
            <p><?php pll_e( 'Not found' ); ?></p>
        </div>
    <?php
    endif;

    wp_reset_postdata();

    echo ob_get_clean();
    wp_die(); // обов'язково для AJAX в WP
}
if ( ! function_exists( 'custom_portfolio_navigation' ) ) {
    function custom_portfolio_navigation( $max_pages, $current_page ) {
        $load_more = '';
        if ( $current_page < $max_pages ) {
            $load_more = '<a class="pagination-more" id="load-more" href="#" data-page="'. ( $current_page + 1 ) .'">
                            <span>Показати більше кейсів</span>
                            <img class="svg-convert" src="'. get_template_directory_uri() .'/assets/img/right.svg" alt="next">
                          </a>';
        }
        
        $pagination = '<ul class="page-numbers">';
        if ( $current_page > 1 ) {
            $prev_page = $current_page - 1;
            $pagination .= '<li><a class="prev page-numbers" href="#" data-page="'. $prev_page .'">
                              <img class="svg-convert" src="'. get_template_directory_uri() .'/assets/img/left.svg" alt="prev">
                            </a></li>';
        }
        
        for ( $i = 1; $i <= $max_pages; $i++ ) {
            if ( $i == $current_page ) {
                $pagination .= '<li><span aria-current="page" class="page-numbers current">'. $i .'</span></li>';
            } else {
                $pagination .= '<li><a class="page-numbers" href="#" data-page="'. $i .'">'. $i .'</a></li>';
            }
        }
        
        if ( $current_page < $max_pages ) {
            $next_page = $current_page + 1;
            $pagination .= '<li><a class="next page-numbers" href="#" data-page="'. $next_page .'">
                              <img class="svg-convert" src="'. get_template_directory_uri() .'/assets/img/right.svg" alt="next">
                            </a></li>';
        }
        $pagination .= '</ul>';
        
        return '<div data-aos="fade-up" class="navigation">' . $load_more . '<div class="nav-list">' . $pagination . '</div></div>';
    }
}
add_action( 'wp_ajax_nopriv_load_portfolio_page', 'load_portfolio_page' );
add_action( 'wp_ajax_load_portfolio_page', 'load_portfolio_page' );

function load_portfolio_page() {
    $paged = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
    $posts_per_page = 6;
    
    $args = array(
        'post_type'      => 'portfolio',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    );
    
    $query = new WP_Query( $args );
    
    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            render_portfolio_item();
        }
    }
    $posts_html = ob_get_clean();
    
    $navigation_html = custom_portfolio_navigation( $query->max_num_pages, $paged );
    
    $response = array(
        'status'          => 'success',
        'posts_html'      => $posts_html,
        'navigation_html' => $navigation_html,
    );
    
    wp_reset_postdata();
    wp_send_json( $response );
    wp_die();
}

