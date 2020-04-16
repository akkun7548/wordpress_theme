<?php
/**
 * このファイルは活動報告の個別ページを表示するために使用されます。
 */
get_header();
if( have_posts() ) :
    while( have_posts() ) :
    the_post(); ?>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content">
        <article <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="row justify-content-end info_post">
                <?php the_tags('<p>タグ ',', ','</p>'); ?>
                <p>投稿日 <?php yadoken_date_link( false ); ?></p>
                <p>更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
            </div>
            <?php the_content(); ?>
            <div style="clear: both;"></div>
        </article>
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