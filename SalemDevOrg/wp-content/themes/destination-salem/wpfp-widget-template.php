<?php

$user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
$favorite_post_ids = wpfp_get_users_favorites( "$user");

echo "<ul class='wpfp-widget-ul'>";
if ($favorite_post_ids) {
	$_sTitle = $this->oUtil->getElement( $aFormData, 'title' );
	$_sNumber = $this->oUtil->getElement( $aFormData, 'number' );
	$_sThumbnailShow = $this->oUtil->getElement( $aFormData, 'thumbnail_show' );
	$_sThumbnailDefault = esc_url( $this->oUtil->getElement( $aFormData, 'thumbnail_default' ) );
	$_sThumbnailAlignment = $this->oUtil->getElement( $aFormData, 'thumbnail_alignment' );
	$_sThumbnailWidth = $this->oUtil->getElement( $aFormData, 'thumbnail_width' );
	$_sThumbnailHeight = $this->oUtil->getElement( $aFormData, 'thumbnail_height' );
	$_sClear = $this->oUtil->getElement( $aFormData, 'clear' );
	$_sCleared = $this->oUtil->getElement( $aFormData, 'cleared' );
	$_sCookieWarning = $this->oUtil->getElement( $aFormData, 'cookie_warning' );
	$c = 0;
	$favorite_post_ids = array_reverse($favorite_post_ids);
		foreach ($favorite_post_ids as $post_id) {
			$post_type = get_post_type($post_id);
			$long_address = get_field('address_1',$post_id) .', '.get_field('city',$post_id).', '.get_field('state', $post_id);
			if ($c++ == $_sNumber) break;
			echo "<li style='list-style-type: none;'>";
			if ( $_sThumbnailShow == '1' && has_post_thumbnail($post_id) ) {
				$thumbnail = get_the_post_thumbnail( "$post_id", array( "$_sThumbnailWidth", "$_sThumbnailHeight" ), array( 'class' => "$_sThumbnailAlignment" ) );
			} else {
				$thumbnail = '<img src="' . $_sThumbnailDefault . '" class="' . $_sThumbnailAlignment . '" style="width:' .$_sThumbnailWidth. ';height="' .$_sThumbnailHeight. '" />';
			}
			if ( $_sThumbnailShow == '0' ) {
				$thumbnail = '';
			}
			if($post_type != 'listing'){
				echo "<a href='".get_permalink($post_id)."' title='". get_the_title($post_id) ."'>$thumbnail <div class='desc'>" . get_the_title($post_id) . "</a><br>" . tribe_get_start_date($post_id) . '<br>' . tribe_get_venue_single_line_address( $post_id, false ) . wpfp_remove_favorite_link($post_id)."</div>";
			}
			else {
				echo "<a href='".get_permalink($post_id)."' title='". get_the_title($post_id) ."'>$thumbnail <div class='desc'>" . get_the_title($post_id) . "</a><br>". $long_address ."</div>";

			}
			
			
			echo "<div style='clear:both;'></div></li>";
			wp_reset_query();
		}
		wpfp_clear_list_link();
		wpfp_cookie_warning();			
} else {

		$_sFavoritesEmpty = $this->oUtil->getElement( $aFormData, 'favorites_empty' );
		
		echo "<li class='empty' style='list-style-type: none;'>";
		echo "$_sFavoritesEmpty";
		echo "<div style='clear:both;'></div></li>";
		
}

echo "</ul>";


wp_reset_query();

?>
