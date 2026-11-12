<?php
$var      = variables();
$set      = $var['setting_home'];
$assets   = $var['assets'];
$url      = $var['url'];
$url_home = $var['url_home'];
$logo = carbon_get_theme_option('logo');
$logo_url  = wp_get_attachment_image_url( $logo, 'full' );

$whatsapp_logo = carbon_get_theme_option('whatsapp_logo');
$whatsapp_logo_url  = wp_get_attachment_image_url( $whatsapp_logo, 'full' );
$whatsapp_link = carbon_get_theme_option('whatsapp_link');

//
$home_id = get_option('page_on_front');
$header_info = carbon_get_post_meta($home_id, 'header_info');
?>

<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport"
              content="width=device-width, initial-scale=1, maximum-scale=5">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="msapplication-TileColor" content="#e43e59">
        <meta name="theme-color" content="#e43e59">
        <title><?php wp_title(); ?></title>
		<?php wp_head(); ?>
    </head>
<body>
<div class="cursor-circle"></div>
<canvas id="fluid"></canvas>
<div role="main" class="page-wrapper">
    <header class="header" role="banner">
        <?php if ( $header_info ) : ?>
        <div class="marquee">
            <ul class="marquee__content">
                <?php foreach ( $header_info as $item ) :?>
                <li><?php echo  $item['text']; ?></li>
                <?php endforeach; ?>
            </ul>
            <ul class="marquee__content" aria-hidden="true">
                <?php foreach ( $header_info as $item ) :?>
                <li><?php echo  $item['text']; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <div class="container">
            <div class="header-top">
                <div class="header-top__bg"></div>
                <div class="header-btn"><span></span></div>
                <div class="header-nav" role="navigation">
                    <?php
                    $menu_html = wp_nav_menu( array(
                        'theme_location' => 'header_menu',
                    ) );?>

                </div>
                <div class="header-logo">
                    <a href="<?php echo $url;?>"><img src="<?php echo $logo_url;?>" alt="logo"></a>
                </div>
                <div class="search-form">
                    <form action="" class="js-search-form">
                        <div class="modal-group">
                            <input type="text" name="keyword" class="js-search-input" placeholder="<?php pll_e( 'Search' ); ?>">
                            <button type="submit" class="search-form-btn"></button>
                            <button type="button" class="search-form-close"></button>
                        </div>
                    </form>
                </div>
                <div class="header-action">
                    <ul>
                        <li>
                            <button class="search-btn" type="button" aria-label="<?php esc_attr_e( 'Open search', 'textdomain' ); ?>">
                                <img class="svg-convert" src="<?php echo $assets;?>images/search.svg" alt="search">
                            </button>
                        </li>
                        <li class="header-action__lang">
                            <?php
                            $languages = pll_the_languages(array(
                                'raw' => 1,
                                'hide_current' => 0
                            ));
                            ?>

                            <span class="lang-title" data-cursor-hover="true">
                                <?php
                                foreach ($languages as $lng) {
                                    if ($lng['current_lang']) {
                                        echo esc_html($lng['slug']);
                                        break;
                                    }
                                }
                                ?>
                            </span>
                            <ul class="lang-list">
                                <?php foreach ($languages as $lng): ?>
                                    <li class="<?php echo $lng['current_lang'] ? 'active' : ''; ?>">
                                        <a href="<?php echo esc_url($lng['url']); ?>">
                                            <?php echo esc_html($lng['slug']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo $whatsapp_link;?>"><img class="svg-convert" src="<?php echo $whatsapp_logo_url;?>" alt="whatsapp"></a>
                        </li>
                    </ul>
                    <a href="#" class="btn-primary btn-popup" data-popup="call"><?php pll_e( 'CAllBACK' ); ?></a>
                </div>

            </div>
        </div>
        <div class="header-overlay"></div>
        <div class="search-form__list" id="search-results"></div>
    </header>
    <div class="main-content">