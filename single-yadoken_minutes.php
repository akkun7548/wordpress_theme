<?php
/**
 * このファイルは議事録の個別ページを表示するために使用されます。
 */
get_header();
if( have_posts() ) :
    while( have_posts() ) :
        the_post();
get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse main-wrapper">
    <article <?php post_class( 'col-lg-8 main-content' ); ?>>
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
        <div style="clear: both;"></div>
        <?php wp_link_pages(); ?>
        <?php comments_template(); ?>
    </article>
    <aside class="col-lg-4 align-self-lg-stretch main-sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
    <?php
    endwhile;
else:
    wp_safe_redirect( home_url(), 302 );
    exit;
endif;
get_footer(); ?>