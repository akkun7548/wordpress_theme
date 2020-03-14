<?php
/**
 * このファイルは固定ページを表示する際に使用されます。
 */
get_header();
if( have_posts() ) :
    while( have_posts() ) :
    the_post();
    ?>
<div class="title_common_1">
    <h1><?php the_title(); ?></h1>
</div>
<div class="row justify-content-end info_common_1">
    <p>作成日 <?php the_time( 'Y年n月j日' ); ?></p>
    <p>更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
</div>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div <?php post_class( 'col-lg-8 content common_1' ); ?>>
        <?php the_content(); ?>
        <div style="clear: both;"></div>
        <?php wp_link_pages(); ?>
        <?php comments_template(); ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
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