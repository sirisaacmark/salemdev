<?php
/**
 * List View Loop
 * This file sets up the structure for the list loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/loop.php
 *
 * @version 4.4
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php
global $post;
global $more;
$more = false;
?>

<div class="tribe-events-loop jumpoffs">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php do_action( 'tribe_events_inside_before_loop' ); ?>

		<!-- Event  -->
		<?php
		$post_parent = '';
		if ( $post->post_parent ) {
			$post_parent = ' data-parent-post-id="' . absint( $post->post_parent ) . '"';
		}
		?>
		<div id="post-<?php the_ID() ?>" class="listing-item <?php tribe_events_event_classes() ?>" <?php echo $post_parent; ?>>
			<div class="title">
				<div class="month">
					<?php echo tribe_get_start_date( null, false, 'l' ) ?>, <?php echo tribe_get_start_date( null, false, 'M' ) ?> <?php echo tribe_get_start_date( null, false, 'j' ) ?>
				</div>
			</div>
			<div class="ecs-thumbnail">
				<a href="<?php echo tribe_get_event_link(); ?>"  class="img-container">
				<?php if ( isset( $atts['thumbwidth'], $atts['thumbheight'] ) and is_numeric( $atts['thumbwidth'] ) and is_numeric( $atts['thumbheight'] ) ): ?>
					<?php echo get_the_post_thumbnail( get_the_ID(), array( intval( $atts['thumbwidth'] ), intval( $atts['thumbheight'] ) ) ); ?>
				<?php elseif ( $event_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' ) ): ?>
					<img src="<?php echo esc_url( $event_image[0] ) ?>" />
				<?php endif; ?>

				</a>
				<?php 
					if(!tribe_is_past()){
						wpfp_link();
					}
				?>
			</div>
			<div class="desc">
				<a href="<?php echo tribe_get_event_link(); ?>"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></a>
				<?php if ( Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ): ?>
					<div class="duration time ecs_start_time">
						<?php echo tribe_get_start_time() ?>
					</div>
				<?php endif; ?>
				<?php the_excerpt(15); ?>
				<!--<div class="ecs-venue"><?php echo tribe_get_venue(); ?></div>-->
				<a class="readmore" href="<?php echo tribe_get_event_link(); ?>">Read More <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
			</div>
		</div>


		<?php do_action( 'tribe_events_inside_after_loop' ); ?>
		
	<?php endwhile; ?>

</div><!-- .tribe-events-loop -->
