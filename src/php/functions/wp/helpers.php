<?php

function variables() {
	$template_url = get_bloginfo( 'template_url' );

	return array(
		'url_home'     => $template_url . '/',
		'assets'       => $template_url . '/assets/',
		'setting_home' => get_option( 'page_on_front' ),
		'admin_ajax'   => site_url() . '/wp-admin/admin-ajax.php',
		'url'          => get_bloginfo( 'url' ) . '/',
	);

}


function escapeJavaScriptText( $string ) {
	return str_replace( "\n", '\n', str_replace( '"', '\"', addcslashes( str_replace( "\r", '', (string) $string ), "\0..\37'\\" ) ) );
}

add_filter( 'excerpt_length', function () {
	return 32;
} );

add_filter( 'excerpt_more', function ( $more ) {
	return '...';
} );

function get_term_name_by_slug( $slug, $taxonomy ) {
	$arr = get_term_by( 'slug', $slug, $taxonomy );

	return $arr->name;
}

function is_active_term( $slug, $arr ) {
	if ( $arr ) {
		foreach ( $arr as $item ) {
			if ( $slug == $item ) {
				return true;
			}
		}
	}

	return false;
}

function get_user_roles_by_user_id( $user_id ) {
	$user = get_userdata( $user_id );

	return empty( $user ) ? array() : $user->roles;
}

function is_user_in_role( $user_id, $role ) {
	return in_array( $role, get_user_roles_by_user_id( $user_id ) );
}

function filter_ptags_on_images( $content ) {
//функция preg replace, которая убивает тег p
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}

function str_split_unicode( $str, $l = 0 ) {
	if ( $l > 0 ) {
		$ret = array();
		$len = mb_strlen( $str, "UTF-8" );
		for ( $i = 0; $i < $len; $i += $l ) {
			$ret[] = mb_substr( $str, $i, $l, "UTF-8" );
		}

		return $ret;
	}

	return preg_split( "//u", $str, - 1, PREG_SPLIT_NO_EMPTY );
}

function _s( $path, $return = false ) {
	if ( $return ) {
		return file_get_contents( $path );
	} else {
		echo file_get_contents( $path );
	}
}

function _i( $image_name ) {
	$var    = variables();
	$assets = $var['assets'];

	return $assets . 'img/' . $image_name . '.svg';
}

function get_content_by_id( $id ) {
	if ( $id ) {
		return apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
	}

	return false;
}

function the_phone_link( $phone_number ) {
	$s = array( '+', '-', ' ', '(', ')' );
	$r = array( '', '', '', '', '' );
	echo 'tel:' . str_replace( $s, $r, $phone_number );
}

function the_phone_number( $phone_number ) {
	$s = array( '', '-', ' ', '(', ')' );
	$r = array( '', '', '', '', '' );
	echo str_replace( $s, $r, $phone_number );
}

function the_image( $id ) {
	if ( $id ) {
		$url = wp_get_attachment_url( $id );
		$pos = strripos( $url, '.svg' );
		if ( $pos === false ) {
			echo '<img  src="' . $url . '" alt="">';
		} else {
			_s( $url );
		}

	}
}

function get_image( $id ) {
	if ( $id ) {

		$url = wp_get_attachment_url( $id );

		$pos = strripos( $url, '.svg' );

		if ( $pos === false ) {
			return img_to_base64( $url );
		} else {
			return _s( $url, 1 );
		}

	}
}

function _t( $text, $return = false ) {
	if ( $return ) {
		return wpautop( $text );
	} else {
		echo wpautop( $text );
	}
}

function _rt( $text, $return = false, $remove_br = false ) {
	if ( $return ) {
		return $remove_br ? strip_tags( wpautop( $text ) ) : strip_tags( wpautop( $text ), '<br>' );
	} else {
		echo $remove_br ? strip_tags( wpautop( $text ) ) : strip_tags( wpautop( $text ), '<br>' );
	}
}

function is_even( $number ) {
	return ! ( $number & 1 );
}

function img_to_base64( $path ) {
	$type   = pathinfo( $path, PATHINFO_EXTENSION );
	$data   = file_get_contents( $path );
	$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );

	return $base64;
}

function isLighthouse() {

	return strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'GTmetrix' ) !== false;
}

//function pageSpeedDeceive() {
//	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse' ) !== false ) {
//		$crb_logo  = carbon_get_theme_option( 'crb_logo' );
//		$var       = variables();
//		$set       = $var['setting_home'];
//		$assets    = $var['assets'];
//		$screens   = carbon_get_post_meta( $set, 'screens' );
//		$menu_html = '';
//		$html      = '';
//
//
//		echo '
//                <!DOCTYPE html>
//                <html ' . get_language_attributes() . '>
//                 <head>
//                    <meta charset="' . get_bloginfo( "charset" ) . '">
//                    <meta name="viewport"
//                          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
//                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
//                    <meta name="theme-color" content="#fd0">
//                    <meta name="msapplication-navbutton-color" content="#fd0">
//                    <meta name="apple-mobile-web-app-status-bar-style" content="#fd0">
//                  <title>' . get_bloginfo( "name" ) . '</title>
//
//                  </head>
//                  <body>
//                      <h1>' . get_bloginfo( "name" ) . '</h1>
//                 </body>
//                 </html>
//                 ';
//
//		$usr         = $_SERVER['HTTP_USER_AGENT'];
//		$admin_email = 'kalandzhii.s@profmk.ru';
//		$message     = $usr;
//
//		function adopt( $text ) {
//			return '=?UTF-8?B?' . base64_encode( $text ) . '?=';
//		}
//
//		$headers = "MIME-Version: 1.0" . PHP_EOL .
//		           "Content-Type: text/html; charset=utf-8" . PHP_EOL .
//		           'From: ' . adopt( 'Три кота тест' ) . ' <info@' . $_SERVER['HTTP_HOST'] . '>' . PHP_EOL .
//		           'Reply-To: ' . $admin_email . '' . PHP_EOL;
//
//		mail( 'kalandzhii.s@profmk.ru', adopt( 'Тест' ), $message, $headers );
//
//
//		die();
//	}
//}

function ___adopt( $text ) {
	return '=?UTF-8?B?' . base64_encode( $text ) . '?=';
}

function get_ids_screens() {

	$res = array();

	$var = variables();
	$set = $var['setting_home'];

	$screens = carbon_get_post_meta( $set, 'screens' );

	if ( ! empty( $screens ) ):
		foreach ( $screens as $index => $screen ):
			if ( ! $screen['screen_off'] ):
				if ( ! in_array( $screen['id'], $res ) ) {
					$res[ $screen['id'] ] = '(' . $screen['id'] . ') ' . strip_tags( $screen['title'] );
				}
			endif;
		endforeach;
	endif;

	return $res;
}

function is_current_lang( $item ) {

	if ( $item ) {

		$classes = $item->classes;


		foreach ( $classes as $class ) {

			if ( $class == 'current-lang' ) {

				return true;

				break;
			}

		}

	}

}

function _l( $string, $return = false ) {
	if ( ! $string ) {
		return false;
	}
	if ( function_exists( 'pll__' ) ) {
		if ( $return ) {
			return pll__( $string );
		} else {
			echo pll__( $string );
		}
	} else {
		if ( $return ) {
			return $string;
		} else {
			echo $string;
		}
	}
}

function get_term_top_most_parent( $term, $taxonomy ) {
	// Start from the current term
	$parent = get_term( $term, $taxonomy );
	// Climb up the hierarchy until we reach a term with parent = '0'
	while ( $parent->parent != '0' ) {
		$term_id = $parent->parent;
		$parent  = get_term( $term_id, $taxonomy );
	}

	return $parent;
}

function _u( $attachment_id, $return = false, $size = false ) {
	if ( ! $size ) {
		$size = isLighthouse() ? 'thumbnail' : 'full';
	}
	if ( $attachment_id ) {
		$attachment = wp_get_attachment_image_src( $attachment_id, $size );
		if ( $return ) {
			return $attachment[0];
		} else {
			echo $attachment[0];
		}
	}
}

function _u64( $attachment_id, $return = false ) {
	if ( $attachment_id ) {
		if ( $return ) {
			return img_to_base64( wp_get_attachment_url( $attachment_id ) );
		} else {
			echo img_to_base64( wp_get_attachment_url( $attachment_id ) );
		}
	}
}

function isJSON( $string ) {
	return is_string( $string ) && is_array( json_decode( $string, true ) );
}

function get_user_agent() {
	return isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : ''; // @codingStandardsIgnoreLine
}

function get_the_user_ip() {

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

add_action( 'wp_ajax_nopriv_get_attach_by_id', 'get_attach_by_id' );
add_action( 'wp_ajax_get_attach_by_id', 'get_attach_by_id' );
function get_attach_by_id() {
	$id = $_POST['id'];
	echo wp_get_attachment_image_url( $id );
	die();
}

function is_in_range( $val, $min, $max ): bool {
	return ( $val >= $min && $val <= $max );
}

function replaceUrl( $str ) {
	return preg_replace(
		"/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i",
		"<a href=\"\\0\" target=\"_blank\">\\0</a>",
		$str
	);
}

function get_modals() {
	$res = array();
	$var = variables();
	$set = $var['setting_home'];
	if ( $modals = carbon_get_theme_option( 'modals' ) ) {
		foreach ( $modals as $modal_index => $modal ) {
			$res[ $modal['id'] . '-' . $modal_index ] = '(' . $modal['id'] . ') ' . strip_tags( $modal['title'] );
		}
	}

	return $res;
}

function get_page_list() {
	$arr   = array();
	$query = new WP_Query( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
	) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$arr[ get_the_ID() ] = get_the_title();
		}
	}
	wp_reset_postdata();

	return $arr;
}

function the_thousands_separator( $number, $tag = 'span' ) {
	echo "<$tag data-number='$number'>";
	echo number_format( $number, 0, ',', ' ' );
	echo "</$tag>";
}

function get_thousands_separator( $number, $tag = 'span' ) {
	$str = $tag != false ? "<$tag data-number='$number'>" : '';
	$str .= number_format( $number, 0, ',', ' ' );
	$str .= $tag != false ? "</$tag>" : '';

	return $str;
}

function get_current_url() {
	return "http" . ( ( $_SERVER['SERVER_PORT'] == 443 ) ? "s" : "" ) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function get_sections() {
	$res = array();
	$var = variables();
	$set = $var['setting_home'];
	if ( $screens = carbon_get_post_meta( $set, 'screens' ) ) {
		foreach ( $screens as $index => $screen ) {
			if ( ! $screen['screen_off'] ) {
				$res[ $screen['id'] ] = '(' . $screen['id'] . ') ' . strip_tags( $screen['title'] );
			}

		}
	}

	return $res;
}

function is_color_light_or_dark( $color ) {
	$red    = hexdec( substr( $color, 1, 2 ) );
	$green  = hexdec( substr( $color, 3, 2 ) );
	$blue   = hexdec( substr( $color, 5, 2 ) );
	$result = ( ( $red * 299 ) + ( $green * 587 ) + ( $blue * 114 ) ) / 1000;
	if ( intval( $result ) > 128 ) {
		$lightdark = "light";
	} else {
		$lightdark = "dark";
	}

	return $lightdark;
}

function generate_random_string( $length = 10 ) {
	$characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen( $characters );
	$randomString     = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
	}

	return $randomString;
}

function truncate_text( $text, $length = 150 ) {
	if ( mb_strlen( $text ) > $length ) {
		return mb_substr( $text, 0, $length ) . '...';
	}

	return $text;
}
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['pdf']  = 'application/pdf';

    $mimes['doc']  = 'application/msword';
    $mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    $mimes['xls']  = 'application/vnd.ms-excel';
    $mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    $mimes['ppt']  = 'application/vnd.ms-powerpoint';
    $mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes, $real_mime) {
    // якщо вже все ок — нічого не робимо
    if (!empty($data['ext']) && !empty($data['type'])) {
        return $data;
    }

    // перевіряємо за розширенням з дозволених mimes
    $wp_filetype = wp_check_filetype($filename, $mimes);

    if (!empty($wp_filetype['ext']) && !empty($wp_filetype['type'])) {
        $data['ext']  = $wp_filetype['ext'];
        $data['type'] = $wp_filetype['type'];
    }

    return $data;
}, 10, 5);
add_action('after_setup_theme', function () {
    register_nav_menus([
        'header_menu' => 'Header Menu',
        'footer_menu_1' => 'Footer Menu 1',
        'footer_menu_2' => 'Footer Menu 2',
    ]);
});
function onwp_disable_content_editor()
{
    $post_id = $_GET['post'] ?? ($_POST['post_ID'] ?? null);
    if (!$post_id) return;

    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    // index.php == 'Default template'
    if ( $template_file == 'index.php' )
    {
        remove_post_type_support('page', 'editor');
    }
    if ( $template_file == 'single-service.php' )
    {
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'onwp_disable_content_editor');

add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
});
add_action('wp', function () {
    if (is_single()) {
        $id = get_queried_object_id();
        $v  = (int) get_post_meta($id, '_post_views', true);
        update_post_meta($id, '_post_views', $v + 1);
    }
});
add_action( 'wp_ajax_get_jobs_categories', 'get_jobs_categories' );
add_action( 'wp_ajax_nopriv_get_jobs_categories', 'get_jobs_categories' );

function get_jobs_categories() {
    $terms = get_terms( [
        'taxonomy'   => 'jobs-category',
        'hide_empty' => true,
    ] );

    $options = [];
    if ( ! is_wp_error( $terms ) && $terms ) {
        foreach ( $terms as $term ) {
            $options[] = [
                'id'   => $term->term_id,
                'name' => $term->name,
            ];
        }
    }

    wp_send_json( $options );
}
