<?php
/**
 * Includes functions that handle registration/enqueuing of meta tags, styles,
 * and scripts in the document head and footer.
 **/

/**
 * Enqueue front-end css and js.
 **/
function enqueue_frontend_assets() {
	wp_enqueue_style( 'style', THEME_CSS_URL . '/style.min.css' );

	if ( $fontkey = get_theme_mod( 'cloud_typography_key' ) ) {
		wp_enqueue_style( 'webfont', $fontkey );
	}

	// Deregister jquery and re-register newer version in the document head.
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', '//code.jquery.com/jquery-3.2.1.min.js', null, null, false );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'ucf-header', '//universityheader.ucf.edu/bar/js/university-header.js?use-1200-breakpoint=1', null, null, true );
	wp_enqueue_script( 'tether', 'https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js', null, null, true );
	wp_enqueue_script( 'script', THEME_JS_URL . '/script.min.js', array( 'jquery', 'tether' ), null, true );

	// Add localized script variables to the document
	$site_url = parse_url( get_site_url() );
	wp_localize_script( 'script', 'UCFCOLLEGE', array(
		'domain' => $site_url['host']
	) );
}

add_action( 'wp_enqueue_scripts', 'enqueue_frontend_assets' );


/**
 * Meta tags to insert into the document head.
 **/
function add_meta_tags() {
?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<?php
$gw_verify = get_theme_mod( 'gw_verify' );
if ( $gw_verify ):
?>
<meta name="google-site-verification" content="<?php echo htmlentities( $gw_verify ); ?>">
<?php endif; ?>
<?php
}

add_action( 'wp_head', 'add_meta_tags', 1 );


/**
 * Removed unneeded meta tags generated by WordPress.
 * Some of these may already be handled by security plugins.
 **/
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );


/**
 * Adds ID attribute to UCF Header script.
 **/
function add_id_to_ucfhb( $url ) {
	if (
		( false !== strpos($url, 'bar/js/university-header.js' ) )
		|| ( false !== strpos( $url, 'bar/js/university-header-full.js' ) )
	) {
      remove_filter( 'clean_url', 'add_id_to_ucfhb', 10, 3 );
      return "$url' id='ucfhb-script";
    }
    return $url;
}

add_filter( 'clean_url', 'add_id_to_ucfhb', 10, 1 );


/**
 * Adds Google Analytics script to the document head.
 **/
function add_google_analytics() {
	$ga_account = get_theme_mod( 'ga_account' );
	if ( $ga_account ):
?>
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', '<?php echo $ga_account; ?>', 'auto');
	ga('send', 'pageview');
</script>
<?php
	endif;
}

add_action( 'wp_head', 'add_google_analytics' );


/**
 * Generates a title based on context page is viewed.  Stolen from Thematic
 **/
function header_title( $title, $separator ) {
	$site_name = get_bloginfo( 'name' );
	if ( is_single() ) {
		$content = single_post_title( '', false );
	}
	elseif ( is_home() || is_front_page() ) {
		$content = get_bloginfo( 'description' );
	}
	elseif ( is_page() ) {
		$content = single_post_title( '', false );
	}
	elseif ( is_search() ) {
		$content = __( 'Search Results for:' );
		$content .= ' ' . esc_html( stripslashes( get_search_query() ) );
	}
	elseif ( is_category() ) {
		$content = __( 'Category Archives:' );
		$content .= ' ' . single_cat_title( '', false );
	}
	elseif ( is_404() ) {
		$content = __( 'Not Found' );
	}
	else {
		$content = get_bloginfo( 'description' );
	}
	if ( get_query_var( 'paged' ) ) {
		$content .= ' ' .$separator. ' ';
		$content .= 'Page';
		$content .= ' ';
		$content .= get_query_var( 'paged' );
	}
	if ( $content ) {
		if ( is_home() || is_front_page() ) {
			$elements = array(
				'site_name' => $site_name,
				'separator' => $separator,
				'content' => $content,
			);
		} else {
			$elements = array(
				'content' => $content,
			);
		}
	} else {
		$elements = array(
			'site_name' => $site_name,
		);
	}
	// But if they don't, it won't try to implode
	if ( is_array( $elements ) ) {
		$doctitle = implode( ' ', $elements );
	}
	else {
		$doctitle = $elements;
	}
	return $doctitle;
}

add_filter( 'wp_title', 'header_title', 10, 2 );
