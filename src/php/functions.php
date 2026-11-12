<?php

/**
 * theme puffspot  functions and definitions
 *
 * @package theme puffspot
 */
function puffspot_scripts(): void {
    //wp_enqueue_style( 'puffspot-fancybox-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), '1.0.0' );
    wp_enqueue_style( 'puffspot-fancybox-intlTelInput', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css', array(), '1.0.0' );
    wp_enqueue_style( 'puffspot-main-css', get_template_directory_uri() . '/assets/css/style.css', array(), '1.0.0' );
    wp_enqueue_style( 'puffspot-fix-css', get_template_directory_uri() . '/assets/css/fix.css', array(), '1.0.0' );

    wp_enqueue_script( 'puffspot-lib-scripts-js', get_template_directory_uri() . '/assets/js/jquery.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'puffspot-lib-intlTelInput','https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js', array( 'puffspot-lib-scripts-js' ), '1.0.0', true );
    wp_enqueue_script( 'puffspot-lib-utils','https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js', array( 'puffspot-lib-intlTelInput' ), '1.0.0', true );
    //wp_enqueue_script( 'puffspot-fancybox-js','https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.js', array( 'puffspot-fancybox-js' ), '1.0.0', true );
    wp_enqueue_script(
        'puffspot-scripts-js',
        get_template_directory_uri() . '/assets/js/common.js',
        array( 'puffspot-lib-scripts-js' ),
        '1.0.0',
        true
    );

    wp_localize_script( 'puffspot-scripts-js', 'MyProductsAjax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'my_products_nonce' ),
        'lang'     => function_exists( 'pll_current_language' )
            ? pll_current_language( 'slug' )
            : 'en',
    ) );
}
add_action( 'wp_enqueue_scripts', 'puffspot_scripts' );


get_template_part( 'functions/wp/helpers' );
get_template_part( 'functions/wp/Carbon' );
get_template_part( 'functions/wp/ajax-functions' );
get_template_part( 'functions/wp/Screens' );

add_action('init', function() {
    pll_register_string('CONTACT MANAGER', 'CONTACT MANAGER');
    pll_register_string('Sort: by', 'Sort: by');
    pll_register_string('Newest', 'Newest');
    pll_register_string('Price ↑', 'Price ↑');
    pll_register_string('Price ↓', 'Price ↓');
    pll_register_string('Name A–Z', 'Name A–Z');
    pll_register_string('Name Z–A', 'Name Z–A');
    pll_register_string('Not found', 'Not found');
    pll_register_string('Catalog', 'Catalog');
    pll_register_string('Home', 'Home');
    pll_register_string('CAllBACK', 'CAllBACK');
    pll_register_string('Search', 'Search');
    pll_register_string('Specifications', 'Specifications');
    pll_register_string('Message sent', 'Message sent');
    pll_register_string('Go to page:', 'Go to page:');
});