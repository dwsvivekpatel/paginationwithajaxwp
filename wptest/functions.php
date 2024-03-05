<?php
/**
 * enqueue all scripts and styles
 */
add_action( 'wp_enqueue_scripts', 'wptest_public_scripts' );

function wptest_public_scripts() {
    wp_enqueue_style( 'wptest-style', get_stylesheet_uri(), array (), NULL );
    wp_enqueue_script( 'jquery', array (), NULL );
    wp_enqueue_script( 'wptest-script', get_template_directory_uri() . '/js/script.js', array (), NULL );
    //localize public script
    wp_localize_script( 'wptest-script','WPTESTPUBLIC', array (
        'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
    ) );
}

/**
 * Registe Direcory Post and Directory taxonomy
 */
add_action( 'init', 'wptest_register_post_types' );

function wptest_register_post_types() {
    $directory_labels = array(
                            'name'               => _x( 'Direcory', 'directory', 'wptest' ),
                            'singular_name'      => _x( 'Direcory', 'directory', 'wptest' ),
                            'menu_name'          => _x( 'Direcory', 'directory', 'wptest' ),
                            'name_admin_bar'     => _x( 'Direcory', 'directory', 'wptest' ),
                            'add_new'            => _x( 'Add New', 'directory', 'wptest' ),
                            'add_new_item'       => __( 'Add New Direcory', 'wptest' ),
                            'new_item'           => __( 'New Direcory', 'wptest' ),
                            'edit_item'          => __( 'Edit Direcory', 'wptest' ),
                            'view_item'          => __( 'View Direcory', 'wptest' ),
                            'all_items'          => __( 'All Direcory', 'wptest' ),
                            'search_items'       => __( 'Search Direcory', 'wptest' ),
                            'parent_item_colon'  => __( 'Parent Direcory:', 'wptest' ),
                            'not_found'          => __( 'No Direcory Found.', 'wptest' ),
                            'not_found_in_trash' => __( 'No Direcory Found In Trash.', 'wptest' ),
                        );

    $directory_args = array(
                            'labels'             => $directory_labels,
                            'public'             => true,
                            'publicly_queryable' => true,
                            'show_ui'            => true,
                            'show_in_menu'       => true,
                            'query_var'          => true,
                            'rewrite'            => array( 'slug'=> 'directory', 'with_front' => false ),
                            'capability_type'    => 'post',
                            'has_archive'        => false,
                            'hierarchical'       => false,
                            'menu_position'      => null,
                            'menu_icon'          => 'dashicons-pressthis',
                            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' )
                        );

    register_post_type( 'directory' , $directory_args );
    
    $labels = array(
                    'name'              => _x( 'Directory Categories', 'taxonomy general name', 'wptest'),
                    'singular_name'     => _x( 'Directory Category', 'taxonomy singular name','wptest' ),
                    'search_items'      => __( 'Search Directory Categories','wptest' ),
                    'all_items'         => __( 'All Directory Categories','wptest' ),
                    'parent_item'       => __( 'Parent Directory Category','wptest' ),
                    'parent_item_colon' => __( 'Parent Directory Category:','wptest' ),
                    'edit_item'         => __( 'Edit Directory Category' ,'wptest'), 
                    'update_item'       => __( 'Update Directory Category' ,'wptest'),
                    'add_new_item'      => __( 'Add New Directory Category' ,'wptest'),
                    'new_item_name'     => __( 'New Directory Category Name' ,'wptest'),
                    'menu_name'         => __( 'Directory Categories' ,'wptest')
                );

    $args = array(
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'rewrite'           => array( 'slug'=> 'dir-category' )
                );
	
    register_taxonomy( 'dir-category', 'directory', $args );
     
    //flush rewrite rules
    flush_rewrite_rules();
}
/**
 * ajax function of filter
 */

add_action( 'wp_ajax_directory_filter', 'fun_directory_filter' );
add_action( 'wp_ajax_nopriv_directory_filter', 'fun_directory_filter' );

    function fun_directory_filter() {
        $clicked_page    = $_POST[ 'page' ] ? $_POST[ 'page' ] : 1;
        $direct_type     = isset( $_POST[ 'direct_type' ] ) ? $_POST[ 'direct_type' ] : '';
        $clicked_page    = ! empty( $clicked_page ) ? $clicked_page : 1;
        $args            = array (
            'post_type'      => 'directory',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'posts_per_page' => 4,
            'paged'          => $clicked_page,
        );
        if ( ! empty( $direct_type) ) {
            $args[ 'tax_query' ][] = array (
                'taxonomy' => 'dir-category',
                'field'    => 'slug',
                'terms'    => $direct_type,
            );
        }
        $direct_query   = new WP_Query( $args );
        $big            = 999999999;
        $paginate_links = array (
            'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'total'        => $direct_query->max_num_pages,
            'current'      => $clicked_page,
            'format'       => '?paged=%#%',
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( '< Previous Page', 'text-domain' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Next Page >', 'text-domain' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        );
        ob_start();
        if ( $direct_query->have_posts() ) { ?>
        <div class="direct-row">
        <?php
            while ( $direct_query->have_posts() ) {
                $direct_query->the_post();
                $direct_id        = get_the_ID();
                $direct_title     = get_the_title();
                $direct_date      = get_the_date();
                $direct_content   =  wp_trim_words( get_the_content(), 10 );
                $img_id           = get_post_thumbnail_id( $direct_id );
                $direct_img_alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                $direct_img_url   = get_the_post_thumbnail_url($direct_id,'medium');
                $direct_permalink = get_the_permalink();
                $taxString = array ();
                $cat       = get_the_terms( $direct_id, 'dir-category' );
                if ( ! empty( $cat ) ) {
                     foreach ( $cat as $key => $category ) {
                        $taxString[] = $category->name;
                     }
                }
                $tax_id_string = implode( ' ,', $taxString );
                ?>
               <div class="directory-item">
                   <a class="directory-link" href="<?php echo $direct_permalink; ?>"></a>
                   <div class="directory-image">
                        <img src="<?php echo $direct_img_url; ?>" alt="<?php echo $direct_img_alt; ?>">
                   </div>
                    <div class="directory-detail">
                        <h2><?php echo $direct_title; ?></h2>
                        <div class="content">
                            <?php echo $direct_content; ?>
                        </div>
                        <h3><?php echo $tax_id_string; ?></h3>
                    </div>
                </div>
                <?php
            }
            wp_reset_query();
            wp_reset_postdata();
            ?>
            </div>
           <div class="pagination col-100"><?php echo paginate_links( $paginate_links ); ?></div>
            <?php
        } else {
            ?>
            <div class="directory-error">
                <p><?php echo _e( 'No Directory Found', 'wptest' ); ?></p>
            </div>
            <?php
        }
        $html_data      = ob_get_contents();
        ob_end_clean();
        ?>
    <?php
        if ( wp_doing_ajax() ) {
            $return_data = array (
                'html_data'      => $html_data,
            );
            echo json_encode( $return_data );
            exit();
        } else {
            echo $html_data;
        }
    }
