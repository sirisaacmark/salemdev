<?php

// =============================================================================
// VIEWS/HEADER/BASE.PHP
// -----------------------------------------------------------------------------
// Declares the DOCTYPE for the site, includes the <head>, opens the <body>
// element as well as the .x-root <div> and .x-site <div>.
// =============================================================================

$x_root_atts = x_atts( apply_filters( 'x_root_atts', array( 'id' => 'x-root', 'class' => 'x-root' ) ) );
$x_site_atts = x_atts( apply_filters( 'x_site_atts', array( 'id' => 'x-site', 'class' => 'x-site site' ) ) );

?>

<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-W547V5');</script>
	<!-- End Google Tag Manager -->
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W547V5" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
  <div <?php echo $x_root_atts; ?>>

    <?php do_action( 'x_before_site_begin' ); ?>

    <div <?php echo $x_site_atts; ?>>

    <?php do_action( 'x_after_site_begin' ); ?>