<?php
/**
 * このファイルはニュースの個別ページを表示するために使用されます。
 */
$args = array(
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => 'yadoken_news',
    'no_found_rows' => true,
);
$r = new WP_Query( $args );
if( $r->posts[0]->ID === get_the_ID() ) {
    wp_safe_redirect( get_post_type_archive_link( 'yadoken_news' ) );
    exit;
}
get_header();
if( have_posts() ) :
    while( have_posts() ) :
        the_post(); ?>
<div class="title_common_1">
    <h1><?php the_title(); ?></h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row justify-content-end info_common_1">
    <p>作成日 <?php the_time( 'Y年n月j日' ); ?></p>
    <p>更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
</div>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div <?php post_class( 'col-lg-8 content common_1' ); ?>>
        <?php the_content(); ?>
        <div style="clear: both;"></div>
        <?php wp_link_pages(); ?>
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