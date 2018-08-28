<?php
	$head = '
		<style>
			body {
				font-family:sans-serif;
			}
		</style>
	';
	
	echo "<table cellspacing='5' cellpadding='5' id='itinerary_body' width='100%' style='background-color:#eeeeee'>";
	echo "<tr><td colspan='2'><h1 style='font-size:30px;'>Your Salem Itinerary</h1></td></tr>";
	if ($favorite_post_ids) {
	
			$favorite_post_ids = array_reverse($favorite_post_ids);
			$post_per_page = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'display_options', 'post_per_page' ), 'default' );
			
			$page = intval(get_query_var('paged'));

			$thumbnail_show = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'thumbnail_options', 'thumbnail_show' ), 'default' );
			$thumbnail_default = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'thumbnail_options', 'thumbnail_default' ), 'default' );
			$thumbnail_alignment = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'thumbnail_options', 'thumbnail_alignment' ), 'default' );
			$thumbnail_width = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'thumbnail_options', 'thumbnail_width' ), 'default' );
			$thumbnail_height = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'thumbnail_options', 'thumbnail_height' ), 'default' );

			$qry = array(
				'post__in' => $favorite_post_ids, 
				'posts_per_page'=> $post_per_page, 
				'orderby' => 'post__in', 
				'paged' => $page
			);
			// custom post type support can easily be added with a line of code like below.
			$qry['post_type'] = array('post','listing', 'tribe_events');
			query_posts($qry);
			
			echo "<tbody>";
			while ( have_posts() ) : the_post();
				
				$post_id = get_the_ID();
				$post_type = get_post_type($post_id);
				$long_address = get_field('address_1',$post_id) .', '.get_field('city',$post_id).', '.get_field('state', $post_id);
				echo "<tr style='background-color:#ffffff;border-bottom:1px solid #666666'>" . "<td><a style='display:inline;float:left;margin-right:15px;margin-bottom:10px;text-decoration:none' href='" . get_permalink($post_id) . "'>" . get_the_post_thumbnail( "$post_id", array(100, 100)) . "</a>";
				if($post_type != 'listing'){
					echo "<a  href='".get_permalink($post_id)."' title='". get_the_title($post_id) ."'><div class='desc' style='display:inline;font-size:16px;text-decoration:none'>" . get_the_title($post_id) . "</a><br>" . tribe_get_start_date($post_id) . '<br>' . tribe_get_venue_single_line_address( $post_id, false ) . "<div style='clear:both'></div></div>";
				}
				else {
					echo "<a href='".get_permalink($post_id)."' title='". get_the_title($post_id) ."' style='font-size:16px;text-decoration:none'><div class='desc' style='display:inline;font-size:14px;text-decoration:none'>" . get_the_title($post_id) . "</a><br/>". $long_address ."<div style='clear:both'></div></div>";

				}
				echo "</td></tr>";
			endwhile;
			echo "</tbody>";

			wpfp_cookie_warning();


			wp_reset_query();
	} else {
			$favorites_empty = WPFavoritePostsAdminPageFramework::getOption( 'WPFavoritePosts', array( 'label_options', 'favorites_empty' ), 'default' );
			echo "$favorites_empty";
	}
	
echo "</table></body></html>";