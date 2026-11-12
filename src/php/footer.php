<?php
$var      = variables();
$set      = $var['setting_home'];
$assets   = $var['assets'];
$url      = $var['url'];
$url_home = $var['url_home'];
$logo = carbon_get_theme_option('logo');
$logo_url  = wp_get_attachment_image_url( $logo, 'full' );
$logo_link = carbon_get_theme_option('logo_link');

$whatsapp_logo = carbon_get_theme_option('whatsapp_logo');
$whatsapp_logo_url  = wp_get_attachment_image_url( $whatsapp_logo, 'full' );
$whatsapp_link = carbon_get_theme_option('whatsapp_link');
$telegram_logo = carbon_get_theme_option('telegram_logo');
$telegram_logo_url  = wp_get_attachment_image_url( $telegram_logo, 'full' );
$telegram_link = carbon_get_theme_option('telegram_link');

$nav_list = carbon_get_theme_option('nav_list');

$home_id = get_option( 'page_on_front' );
$copy_text = carbon_get_post_meta($home_id, 'copy_text');
$popup_title = carbon_get_post_meta($home_id, 'popup_title');
$popup_form = carbon_get_post_meta($home_id, 'popup_form');
$form_bottom_text = carbon_get_post_meta($home_id, 'form_bottom_text');

$poppup_text = carbon_get_theme_option('poppup_text');
$poppup_form = carbon_get_theme_option('poppup_form');
$poppup_thn_title = carbon_get_theme_option('poppup_thn_title');
$poppup_thn_text = carbon_get_theme_option('poppup_thn_text');
$poppup_thn_btn = carbon_get_theme_option('poppup_thn_btn');
$nav_list_footer = carbon_get_theme_option('nav_list_footer');
$copy = carbon_get_theme_option('copy');
?>
</div>
<footer class="footer"  role="contentinfo">
    <div class="container">
        <div class="footer-inner">
            <div class="footer-top">
                <div class="footer-nav">
                    <?php
                    $menu_html = wp_nav_menu( array(
                        'theme_location' => 'footer_menu_1',
                    ) );?>
                </div>
                <div class="footer-logo">
                    <a href="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>"><img src="<?php echo $logo_url;?>" alt="logo"></a>
                </div>
                <div class="footer-action">
                    <?php
                    $languages = pll_the_languages(array(
                        'raw' => 1,
                        'hide_current' => 0
                    ));
                    ?>
                    <ul>
                        <li>
                            <span class="lang-title" data-cursor-hover="true"><?php
                                foreach ($languages as $lng) {
                                    if ($lng['current_lang']) {
                                        echo esc_html($lng['slug']);
                                        break;
                                    }
                                }
                                ?></span>
                        </li>
                        <li>
                            <a href="<?php echo $whatsapp_link;?>"><img class="svg-convert" src="<?php echo $whatsapp_logo_url;?>" alt="whatsapp"></a>
                        </li>
                        <li>
                            <a href="<?php echo $telegram_link;?>"><img class="svg-convert" src="<?php echo $telegram_logo_url;?>" alt="telegram"></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span><?php echo $copy_text ;?> </span>
                <?php
                $menu_html = wp_nav_menu( array(
                    'theme_location' => 'footer_menu_2',
                ) );?>
            </div>
        </div>
    </div>
</footer>
<div class="popup" data-popup="call">
    <div class="popup-wrap">
        <div class="popup-close"></div>
        <div class="contact-inner">
            <div class="title-section"><?php echo $popup_title ;?></div>
            <div class="contact-form">
                <?php echo do_shortcode( $popup_form ); ?>
                <div class="text-form">
                    <?php echo $form_bottom_text ;?>
                </div>
            </div>
            <span class="_bg"></span>
        </div>

    </div>
    <div class="popup-overlay"></div>
</div>
<div class="popup" data-popup="product">
    <div class="popup-wrap">
        <div class="popup-close"></div>
        <div class="contact-inner">
            <div class="title-section"><?php echo $popup_title ;?></div>
            <div class="contact-form">
                <?php echo do_shortcode( '[custom-form id="276"]' ); ?>
<!--                --><?php //echo do_shortcode( $popup_form ); ?>
                <div class="text-form">
                    <?php echo $form_bottom_text ;?>
                </div>
            </div>
            <span class="_bg"></span>
        </div>

    </div>
    <div class="popup-overlay"></div>
</div>
<div class="modal modal-sm" style="display: none" id="">
    <div class="modal-content">
        <div class="modal-title text-center">
            <div class="modal-title__main"></div>
        </div>
    </div>
</div>
<div class="popup" data-popup="thanks">
    <div class="popup-wrap">
        <div class="popup-close"></div>
        <div class="contact-inner">
            <div class="title-section" style="text-align: center"><?php pll_e( 'Message sent' ); ?></div>
            <span class="_bg"></span>
        </div>
    </div>
    <div class="popup-overlay"></div>
</div>

<?php wp_footer(); ?>
<script>
    // Smoke cursor: desktop only (mouse), lazy-loaded after the page is ready
    if (window.innerWidth >= 769 && window.matchMedia('(pointer: fine)').matches) {
        var s = document.createElement('script');
        s.src = '<?php echo esc_url( get_template_directory_uri() . '/assets/js/cursor.js?v=' . filemtime( get_template_directory() . '/assets/js/cursor.js' ) ); ?>';
        document.body.appendChild(s);
    }
</script>
</body>
</html>
