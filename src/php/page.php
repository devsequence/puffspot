<?php
/* Template Name: text template */
get_header();
$var = variables();
$set = $var['setting_home'];
$assets = $var['assets'];
$url = $var['url'];
$url_home = $var['url_home'];
$id = get_the_ID();
$screens = carbon_get_post_meta($id, 'screens');
?>
<section class="text-page-section">
    <div class="container">
        <div class="title-section"><?php echo the_title();?></div>
        <div class="text-section"><?php echo the_content();?></div>
    </div>
</section>

<?php
get_footer();