<?php
get_header();
if( have_posts() ) :
    while( have_posts() ) :
        the_post(); ?>
<main>
<div class="main_visual">
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div <?php post_class( 'content col-lg-8' ) ?>>
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
            <div class="clear-both"></div>
            <?php wp_link_pages(); ?>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
    <?php
    endwhile;
else:
    wp_safe_redirect( home_url(), 302 );
    exit;
endif;
get_footer(); ?>