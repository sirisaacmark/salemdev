<?php

// =============================================================================
// VIEWS/SITE/GOOGLE-ANALYTICS.PHP
// -----------------------------------------------------------------------------
// Plugin site output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Require Options
//   02. Output
// =============================================================================

// Require Options
// =============================================================================

require( TCO_GOOGLE_ANALYTICS_PATH . '/functions/options.php' );



// Output
// =============================================================================

// Check if has admin capabilities
if ( ! ( is_user_logged_in() && current_user_can( 'update_core' ) ) ) {
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $tco_google_analytics_id; ?>"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '<?php echo $tco_google_analytics_id; ?>');
</script>
<?php
	echo $tco_meta_tag;
}
