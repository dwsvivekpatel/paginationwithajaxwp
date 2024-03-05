<?php
/*
 * Template Name: Directory Listing Template
 */
get_header();

$tax_args   = array (
    'taxonomy'   => 'dir-category',
    'hide_empty' => true,
);
$categories = get_terms( $tax_args );
?>
<h1>Category Listing</h1>
<section class="direcory-listing-top-section">
    <div class="container">
            <div class="directory-cats">
                <div class="directory-filter">
                    <ul id="directory_tax">
                        <li class="cat-item active"  data-item=""><?php _e( 'All', 'wptest' ); ?></li>
                        <?php foreach ( $categories as $key => $category ) { ?>
                            <li class="cat-item" data-item="<?php echo $category->slug; ?>"><?php echo $category->name; ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
    </div>
</section>

<section class="direcory-listing">
<div id="load-directories-content" class="row">
   <?php fun_directory_filter(); ?>
 </div>
</section>

<?php get_footer(); ?>
