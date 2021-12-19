<?php
/**
 * このファイルは活動報告の個別ページを表示するために使用されます。
 */
$date_format = get_option( 'date_format' );
get_header();
if( have_posts() ) :
    while( have_posts() ) :
    the_post();
get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse main-wrapper">
    <div class="col-lg-8 main-content">
        <article <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="row justify-content-end date">
                <p>カテゴリー：<object><?php the_category( '、' ) ?></object></p>
                <?php the_tags('<p>タグ ',', ','</p>'); ?>
                <p>投稿日 <?php yadoken_date_link( $date_format ); ?></p>
                <p>更新日 <?php the_modified_date( $date_format ); ?></p>
            </div>
            <?php the_content(); ?>
            <div style="clear: both;"></div>
        </article>
        <?php wp_link_pages(); ?>
        <?php comments_template(); ?>
    </div>
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