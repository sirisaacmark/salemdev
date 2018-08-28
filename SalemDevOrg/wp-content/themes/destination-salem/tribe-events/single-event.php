<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.3
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();

$event_id = get_the_ID();

?>

<div id="tribe-events-content" class="tribe-events-single">

	<!-- Notices -->
	<?php tribe_the_notices() ?>

	
	<!-- #tribe-events-header -->

	<?php while ( have_posts() ) :  the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="flex">
				<div class="twocol">
					<?php the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ); ?>

					<div class="tribe-events-schedule tribe-clearfix">
						<?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
						<?php if ( tribe_get_cost() ) : ?>
							<span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ) ?></span>
						<?php endif; ?>
					</div>
					
					<!-- Event header -->
					<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
						<!-- Navigation -->
						<h3 class="tribe-events-visuallyhidden"><?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?></h3>
						<ul class="tribe-events-sub-nav">
							<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
							<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
						</ul>
						<!-- .tribe-events-sub-nav -->
					</div>
					
					<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
					
					
					<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>
					<div class="tribe-events-single-event-description tribe-events-content">
						
						<?php the_content(); ?>

						
					</div>
					<hr>
					<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
					
					<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
					
				</div>
				<div>
					<?php wpfp_link(); ?>
					<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
					<?php tribe_get_template_part( 'modules/meta' ); ?>
					
				</div>
			</div>
			<!-- Event featured image, but exclude link -->
			

			<!-- Event content -->
			
		</div> <!-- #post-x -->
		<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
	<?php endwhile; ?>

	<!-- Event footer -->
	<div id="tribe-events-footer">
		<!-- Navigation -->
		<h3 class="tribe-events-visuallyhidden"><?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?></h3>
		<ul class="tribe-events-sub-nav">
			<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
			<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
		</ul>
		<!-- .tribe-events-sub-nav -->
	</div>
	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
