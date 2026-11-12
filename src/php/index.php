<?php
/* Template Name: main template */
get_header();
$var = variables();
$set = $var['setting_home'];
$assets = $var['assets'];
$url = $var['url'];
$url_home = $var['url_home'];
$id = get_the_ID();
$screens = carbon_get_post_meta($id, 'screens');
new Screens($screens);

get_footer();