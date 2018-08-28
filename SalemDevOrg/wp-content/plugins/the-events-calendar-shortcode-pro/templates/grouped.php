<?php
// see if we have a new start date
$display_start_date = false;
$ecs_date_format = ( 'month' === $atts['groupby'] ? 'Y-m' : 'Y-m-d' );
if ( ! isset( $GLOBALS['ecn_grouped_last_date'] ) or ( tribe_get_start_date( null, false, $ecs_date_format ) != $GLOBALS['ecn_grouped_last_date'] ) )
	$display_start_date = true;
$GLOBALS['ecn_grouped_last_date'] = tribe_get_start_date( null, false, $ecs_date_format );
?>

<?php $contentorder = array_map( 'trim', $atts['contentorder'] ); ?>

<?php if ( $display_start_date ): ?>
	<h3 class="ecs-date"><?php echo date_i18n( ( 'month' === $atts['groupby'] ? 'F Y' : get_option( 'date_format' ) ), strtotime( tribe_get_start_date( null, false, 'Y-m-d' ) ) ); ?></h3>
<?php endif; ?>

<div class="ecs-event">
    <?php if ( 'month' === $atts['groupby'] ): ?>
        <span class="date"><?php echo date_i18n( apply_filters( 'ecs_grouped_date_format', 'l j' ), strtotime( tribe_get_start_date( null, false, 'Y-m-d' ) ) ); ?>:</span>
    <?php endif; ?>
    <?php if ( Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ): ?>
	    <span class="time"><?php echo tribe_get_start_time() ?></span>
    <?php endif; ?>
	<span class="summary"><a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></a></span>
	<?php if ( Events_Calendar_Shortcode::isValid( $atts['venue'] ) and tribe_has_venue() ): ?>
		<span class="ecs-venue"><span class="ecs-venue-at">@ </span><?php echo tribe_get_venue(); ?></span>
	<?php endif; ?>
</div>
