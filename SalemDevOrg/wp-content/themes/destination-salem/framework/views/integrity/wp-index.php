<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-INDEX.PHP
// -----------------------------------------------------------------------------
// Index page output for Integrity.
// =============================================================================

?>

<?php get_header(); ?>
  <div class="x-container max width offset">
    <?php 
        if(!is_single() && !is_search()){
      ?>
    	<ul class="cat-list">
        <li>
          <?php 
            if(!is_front_page() && is_home() ){
              echo '<a href="/blog" class="active">All</a>';
            } else {
              echo '<a href="/blog">All</a>';
            }
          ?>
          
        </li>
        <?php wp_list_categories( array(
          'orderby' => 'name',
          'title_li' => ''
        )); ?> 
      </ul>
    <?php } ?>
    <br>
    <div class="<?php x_main_content_class(); ?> blog-container jumpoffs row" role="main">

      <?php x_get_view( 'global', '_index' ); ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>