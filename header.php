<?php status_header( 200 ); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg md-scrollbar">

<head>
	<?php
	global $wp;
	$abs_url = home_url( $wp->request );

	$blog_description = htmlspecialchars( get_bloginfo( 'description' ), ENT_COMPAT, 'UTF-8', false );
	$blog_name        = htmlspecialchars( get_bloginfo( 'blogname' ), ENT_COMPAT, 'UTF-8', false );
	$current_site_title            = htmlspecialchars( get_the_title(), ENT_COMPAT, 'UTF-8', false );

	echo "
		<title>$blog_name - $blog_description</title>
		<meta name='description' content='$blog_description' />
		<meta property='og:site_name' content='$blog_name'>
		<meta property='og:title' content='$current_site_title'>
		<meta property='og:description' content='$blog_description'>
		<meta property='og:url' content='$abs_url'>
		<meta property='og:type' content='website'>
		<meta name='twitter:card' content='summary_large_image'>

		<!-- Script for polyfilling Promises on IE9 and 10 -->
		<script src='https://cdn.polyfill.io/v2/polyfill.min.js'></script>
	";

	$url = ( 'home' == $wp->request ) ? site_url() : site_url( $wp->request );
	$pid = url_to_postid( $url );
	if ( has_post_thumbnail( $pid ) ) {
		$thumb_url = get_the_post_thumbnail_url( $pid, 'large' );
		echo "<meta property='og:image' content='$thumb_url'>";
		echo "<meta property='og:image:secure_url' content='$thumb_url'>";
	}

	?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body>
