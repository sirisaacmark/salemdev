<?php

// =============================================================================
// VIEWS/INTEGRITY/CONTENT.PHP
// -----------------------------------------------------------------------------
// Standard post output for Integrity.
// =============================================================================

?>


<?php if(is_single()): ?>
	<div id="post-<?php the_ID(); ?>">
		
		<div class="desc">
			<h1><?php the_title(); ?></h1>
			<?php x_integrity_entry_meta(); ?>
			<?php the_content(); ?>
		</div>
	</div>
<?php else: ?>
	<div id="post-<?php the_ID(); ?>" class="listing-item">
		<div class="entry-featured">
			<?php x_featured_image(); ?>
		</div>
		<div class="desc">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php the_excerpt(); ?>
		</div>
		<a class="readmore" href="<?php the_permalink(); ?>">Read More <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
	</div>

<?php endif; ?>