<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

	add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );
	

	// add Font Awesome library
	add_action( 'wp_enqueue_scripts', 'enqueue_load_fa' );
	function enqueue_load_fa() {
	    wp_enqueue_style( 'load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
	}

	add_action('wp_enqueue_scripts','custom_scripts');
	function custom_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/custom-scripts.js');

	}
	add_theme_support( 'post-thumbnails' ); 
	@ini_set( 'upload_max_size' , '64M' );

	/* custom excerpt */
	function new_excerpt_more($more) {
	  global $post;
	  remove_filter('excerpt_more', 'new_excerpt_more'); 
	  return '...';
	}
	add_filter('excerpt_more','new_excerpt_more',11);

	function custom_excerpt_length( $length ) {
	    return 25;
	}
	add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

	// shortcode for showing itinerary menu item
	add_shortcode( 'check_itinerary', 'check_itin' );

	function check_itin() {
		$user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
		$favorite_post_ids = wpfp_get_users_favorites( "$user");
		if ($favorite_post_ids) {
			$qry = array(
				'post__in' => $favorite_post_ids, 
				'posts_per_page'=> $post_per_page, 
				'orderby' => 'post__in', 
				'paged' => $page
			);
			$qry['post_type'] = array('post','listing', 'tribe_events');
			query_posts($qry);
			while ( have_posts() ) : the_post();
				echo '<a href="/plan-share/itinerary/" class="itinerary-menu"><i class="fa fa-calendar-check-o" title="Your Itinerary"></i></a>';
				break;
			endwhile;
		} 
		wp_reset_query();
	}


	// Register Custom Post Type
	function post_type_listings() {

		register_post_type( 'listing',
	        array(
	            'labels' => array(
	                'name' => __( 'Listings' ),
	                'singular_name' => __( 'listing' )
	            ),
	            'public' => true,
	            'has_archive' => true,
	            'rewrite' => array('slug' => 'listing'),
	            'taxonomies' => array( 'listing-type', 'interest' ),
	            'menu_icon' => 'dashicons-store',
	            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt')
	        )
	    );

	}
	add_action( 'init', 'post_type_listings', 0 );
	add_action( 'init', 'create_listing_taxonomies', 0 );

	// create two taxonomies, genres and writers for the post type "listing"
	function create_listing_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Listing Types', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Listing Type', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Search Listing Types', 'textdomain' ),
			'all_items'         => __( 'All Listing Types', 'textdomain' ),
			'parent_item'       => __( 'Parent Listing Type', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Listing Type:', 'textdomain' ),
			'edit_item'         => __( 'Edit Listing Type', 'textdomain' ),
			'update_item'       => __( 'Update Listing Type', 'textdomain' ),
			'add_new_item'      => __( 'Add New Listing Type', 'textdomain' ),
			'new_item_name'     => __( 'New Listing Type Name', 'textdomain' ),
			'menu_name'         => __( 'Listing Type', 'textdomain' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'listing-type' ),
		);

		register_taxonomy( 'listing-type','listing', $args );

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => _x( 'Interests', 'taxonomy general name', 'textdomain' ),
			'singular_name'              => _x( 'Interest', 'taxonomy singular name', 'textdomain' ),
			'search_items'               => __( 'Search Interests', 'textdomain' ),
			'popular_items'              => __( 'Popular Interests', 'textdomain' ),
			'all_items'                  => __( 'All Interests', 'textdomain' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Interest', 'textdomain' ),
			'update_item'                => __( 'Update Interest', 'textdomain' ),
			'add_new_item'               => __( 'Add New Interest', 'textdomain' ),
			'new_item_name'              => __( 'New Interest Name', 'textdomain' ),
			'separate_items_with_commas' => __( 'Separate writers with commas', 'textdomain' ),
			'add_or_remove_items'        => __( 'Add or remove writers', 'textdomain' ),
			'choose_from_most_used'      => __( 'Choose from the most used writers', 'textdomain' ),
			'not_found'                  => __( 'No writers found.', 'textdomain' ),
			'menu_name'                  => __( 'Interests', 'textdomain' ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'interests' ),
		);

		register_taxonomy( 'interest', 'listing', $args );
	}

	add_shortcode( 'get_listings', 'listing_shortcode' );


	function listing_shortcode( $atts, $cat ) {
	    $listingtype = '';
    	$interestname = '';
    	$exclude = '';
	    extract( shortcode_atts( array(
			"name" => false,
			"listingtype" => $listingtype,
			"interestname" => $interestname,
			"exclude" => $exclude
	    ), $atts ) );
	    global $cfs;
	    $ul_class = 'jumpoffs';
	    $ul_class .= ( $name )     ? '' : ' row';
	    $li_class = 'listing-anchor';
	    $col_class = ( $name )     ? '' : ' listing-item';
	    $lis = '';
	    $query = new WP_Query( array(
	        'post_type' => 'listing',
	        'orderby' => 'title',
	        'order' => 'ASC',
	        'posts_per_page' => -1,
	        'tax_query' => array(
	        	'relation' => 'OR',
		    	array(
		    		'relation' => 'AND',
	    			array(
			        	'taxonomy' => 'listing-type',
					    'field' => 'slug',
				        'include_children' => true,
				        'operator' => 'IN',
					    'terms' => $listingtype
			    	),
			    	array(
			        	'taxonomy' => 'listing-type',
					    'field' => 'id',
					    'terms' => array($exclude),
				        'operator' => 'NOT IN'
			    	),
			    ),
		    	array(
		        	'taxonomy' => 'interest',
				    'field' => 'slug',
			        'include_children' => true,
			        'operator' => 'IN',
				    'terms' => $interestname
		    	)
		    )
	    ) );
	    
	   while ( $query->have_posts() ) : $query->the_post();
	        $lis .= sprintf(
	            '<div class="%s"><div class="title">&nbsp;</div><a class="%s img-container ecs-thumbnail " href="%s"> %s </a><div class="desc"><a href="%s">%s</a><div class="excerpt">%s</div></div><a href="%s" class="moreinfo">More Info <i class="fa fa-angle-double-right"></i></a>%s</div>',
					esc_attr($col_class), // item class
					esc_attr($li_class), // anchor class
					esc_url(get_permalink()), // the link
					get_the_post_thumbnail( $post_id, 'full'),
					esc_url( get_permalink() ), // the link
					get_the_title(),
					get_the_excerpt(),
					esc_url(get_permalink()),
					wpfp_link(get_the_ID())
					
	        );
	        
	    endwhile;
	    
	    wp_reset_query();  
	    return sprintf( 
	          '<div class="%s">%s</div>',
	          esc_attr( $ul_class ),
	          $lis
	        );
	    next_posts_link();
		previous_posts_link();
	}
	// header assignments broke for pages so assign here (220 for Do, 178 for Plan, 225 for Stay)
	add_filter('cs_match_header_assignment', 'custom_search_header');
	function custom_search_header($match) {
		$user = wp_get_current_user();
		
		// Do (ID are due to page name conflicts)
		if (is_page(['Your Adventure','Museums & Attractions','Tours', 'Entertainment','Shop','Education & Spiritual Growth','Festivals','10 Free Things', 'Harbor Tours','Food & Drink','Ghost','Historical','Witch Trials','Receptive Tours','Modern Witch','Arcade','Cinema','Theatre','Clothing','Gifts','Witch','Art & Jewelry','Spirits','Snacks','Books',31917,'Pets','Outdoors','Parks','Beaches',31939,'Spiritual','Universities','Landmarks',31969,31973,31971,31975,'Maritime',31981,'Halloween',31983])) {
			$match = 31962; 
		}
		if(get_post_type() == 'listing'){
			$match = 31962; 
		}
		// Eat
		if (is_page(['Eat','Breakfast','Lunch','Dinner','Drinks','Dessert'])) {
			$match = 32001; 
		}
		if(get_post_type() == 'listing' && has_term('Eat','listing-type')){
			$match = 32001; 
		}

		// Stay
		if (is_page(['Stay','Hotels','Inns','Motels','B&Bs',31827,31829])) {
			$match = 32000; 
		}
		if(get_post_type() == 'listing' && has_term('Stay','listing-type')){
			$match = 32000; 
		}
		// Plan
		if (is_page(['Parking Map & Transportation','Free Guide','Map of Salem','Transportation','Seasonal Hours','LGBTQ','Itinerary','Group Tour'])){
			$match = 31999; 
		}
		if (is_page('Contest')) {
			$match = 31802; 
		}
		// Learn
		if (is_page(['Notable Locals',156,'FAQs', 'Salem Witch Trials'])){
			$match = 32002; 
		}
		// Blog
		if (is_single() && !tribe_is_event() && get_post_type() != 'listing'){
			$match = 32006; 
		}
		if (is_category()){
			$match = 32006; 
		}
		// Search
		if (is_search()) {
			$match = 32003; 
		}
		return $match;
	}

	// search tweaks
	function remove_post_type_page_from_search() {
    global $wp_post_types;
	    $wp_post_types['page']->exclude_from_search = true;
	    $wp_post_types['tribe-events']->exclude_from_search = true;
	    $wp_post_types['venues']->exclude_from_search = true;
	}
	add_action('init', 'remove_post_type_page_from_search');
	// Show only first instance of recurring events in search results
	add_filter( 'posts_groupby', 'my_posts_groupby', 10, 2);
	function my_posts_groupby ( $group_by, $query ) {
	  if (is_admin() || !is_search())  return $group_by;
	  
	  global $wpdb;
	  $group_by = " IF( {$wpdb->posts}.post_parent = 0, {$wpdb->posts}.ID, {$wpdb->posts}.post_parent )";
	  
	  return $group_by;
	}
	add_filter( 'posts_orderby', 'order_search_by_posttype', 10, 1 );
	function order_search_by_posttype( $orderby ){
	    if( ! is_admin() && is_search() ) :
	        global $wpdb;
	        $orderby =
	            "
	            CASE WHEN {$wpdb->prefix}posts.post_type = 'tribe-events' THEN '1' 
	                 WHEN {$wpdb->prefix}posts.post_type = 'post' THEN '2' 
	                 WHEN {$wpdb->prefix}posts.post_type = 'listing' THEN '3' 
	            ELSE {$wpdb->prefix}posts.post_type END ASC, 
	            {$wpdb->prefix}posts.post_title ASC";
	    endif;
	    return $orderby;
	}


?>