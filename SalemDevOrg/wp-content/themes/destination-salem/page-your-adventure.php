<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-INDEX.PHP
// -----------------------------------------------------------------------------
// Index page output for Integrity.
// =============================================================================

?>

<?php get_header(); ?>
  <div class="x-container max width offset text-center">
    <h1 class="text-center"><span style="color:#70a3be">Your</span> <span style="color:#176278">Adventure</span></h1>
    <div style="font-size:1.1em">
        <br>
        <p>You’ve told us a little about yourself, and we’ve curated a list of activities just for you. How will you make Salem your best story yet? 
        </p>
        <p>Don’t forget to add any items to your <a href="/itinerary"><strong>saveable itinerary</strong></a>.</p>
        <p>
        Are you a self-proclaimed Halloween enthusiast? Plan your visit during Salem Haunted Happenings! Click here to learn more about visiting Salem in October.</p>
        <br>
    </div>
    <div class="<?php x_main_content_class(); ?> blog-container" role="main">
      <?php $term = get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') ); ?>
      <?php
        // grab interests from query string in URL
        if (get_query_var('interest')){ 
            $interest = get_query_var('interest');
            $interest_array = explode(',', $interest); 
            $query = new WP_Query( 
                array( 
                    'post_type' => array('listing', 'events'),
                    'showposts' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'tax_query' => array(
                    'relation' => 'OR',
                        array(
                            'taxonomy' => 'interest',
                            'field' => 'slug',
                            'terms' => $interest_array
                        )
                    )
                ) 
            );
            if ( $query->have_posts() ) : ?>
                <div class="jumpoffs row adv">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?> 
                        <div class="listing-item">
                            <div class="title">&nbsp;</div>
                            <div class="ecs-thumbnail">
                                <a href="<?php the_permalink(); ?>" class="img-container">
                                    <?php the_post_thumbnail('small'); ?>
                                </a>
                                <?php wpfp_link(); ?>
                            </div>
                            <div class="desc">
                                <?php 
                                    echo '<a href="' .get_the_permalink() . '">';
                                    the_title();
                                    echo '</a>';
                                    the_excerpt(20);
                                ?>
                            </div>
                            <a class="readmore" href="<?php the_permalink(); ?>">Read More <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                Nothing to display
            <?php endif; ?>
        <?php } ?>
        
    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>