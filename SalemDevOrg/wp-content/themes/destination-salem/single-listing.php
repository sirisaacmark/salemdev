<?php

// =============================================================================
// VIEWS/INTEGRITY/TEMPLATE-LAYOUT-FULL-WIDTH.PHP
// -----------------------------------------------------------------------------
// Fullwidth page output for Integrity.
// =============================================================================

?>

<?php get_header(); ?>

  <div class="x-container max width offset">
    <div class="tribe-events-single single-tribe_events">
        <div class="<?php x_main_content_class(); ?>" role="main">
    	<?php $term = get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') ); ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <div>
                    <?php 
                        /*
                        echo '<h1>' . get_the_title() . '</h1>';
                        echo $term->name;
                        echo '<div class="content">' . get_the_content() . '</div>';
                        */
                    ?>
                    <div class="flex">
                        <div class="twocol">
                            <?php the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ); ?>

                            
                            <?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
                            
                            
                            
                            <div class="tribe-events-single-event-description tribe-events-content">
                                <?php
                                    // variable for location
                                    $term_list = '';
                                    $terms     = get_the_terms( $post->ID, 'listing-type' );
                                    $prefix    = '';

                                        foreach( $terms as $term ) {
                                             $parent_term = get_term_children( $term->parent, 'listing-type' );
                                             if($term->name != 'Eat'){
                                                if($term->name != 'Stay'){
                                                    if($term->name != 'Do'){
                                                        if($term->name != 'Learn'){
                                                        $term_list  .= $prefix . $term->name;
                                                        $prefix      = ', ';
                                                        }
                                                    }
                                                }
                                             }
                                        }

                                        // output
                                    echo  '<h4 class="tribe-events-single-event-categories">' . $term_list . '</h4>';
                                ?>

                                <?php the_content(); ?>

                            </div>
                            <!-- .tribe-events-single-event-description -->
                            
                            <!-- Event meta -->
                            <?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
                            
                            <?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
                        </div>
                        <div>
                            <?php wpfp_link(); ?>
                            <?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>
                            <div class="listing-details">
                                <h3 class="tribe-events-single-section-title">Details</h3>
                                <dl>
                                    <dt> Address: <br>
                                        <abbr class="tribe-events-abbr tribe-events-start-datetime published dtstart" title="2018-08-03"> 
                                            <?php $address2 = get_field( "address_2" ); ?>
                                            <?php echo $address1 = get_field( "address_1" ); ?> <br>
                                            <?php 
                                                if($address2):
                                                    echo $address2 . '<br>'; 
                                                endif
                                            ?> 
                                            <?php echo $city = get_field( "city" ); ?>,  <?php echo $state = get_field( "state" ); ?> <?php echo $zip = get_field( "zip" ); ?><br>
                                            
                                        </abbr>
                                    </dt>
                                </dl>
                                <dl>
                                    <dt>
                                        Phone:<br>
                                        <?php $phone = get_field('phone')?>
                                        <abbr>
                                            <?php echo '<a href="tel:' . $phone . '">' . $phone . '</a>' ?> 
                                        </abbr>
                                    </dt>
                                </dl>
                                <dl>
                                    <dt>
                                        Website:<br>
                                        <abbr>
                                            <?php echo $website = '<a href="' . get_field( "website" ) . '" target="_blank">' . get_field('website_text') . '</a>' ?> <br>
                                        </abbr>
                                    </dt>
                                </dl>
                            </div>
                            
                        </div>
                    </div>
                <?php x_get_view( 'global', '_comments-template' ); ?>
                </div>
            <?php endwhile; ?>

        </div>
      </div>
</div>

<?php get_footer(); ?>