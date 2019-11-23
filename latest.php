<?php
/*
Template Name: 最新ページ
Description: 投稿タイプ中で最新のページにリダイレクトするページ
*/
$slug = get_post()->post_name;
if( post_type_exists( $slug ) ) {
    $post_type = $slug;
} else {
    $post_type = 'post';
}
$args = array(
    'posts_per_page' => '1',
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => $post_type,
    'post_status' => 'publish',
);
$objs = get_posts( $args );
if( $objs ) :
    wp_safe_redirect( get_permalink( $objs[0]->ID ), 302 );
    exit;
else :
    get_header();
    if( have_posts() ) :
        while( have_posts() ) :
            the_post(); ?>
<main>
<div class="main_visual container-fluid">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1><?php the_title(); ?></h1>
    <div class="row justify-content-end info_common_1">
        <p class="info_common_1">作成日 <?php the_time( 'Y年n月j日' ); ?></p>
        <p class="info_common_1">更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
    </div>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div <?php post_class( 'content col-lg-8 common_1 ' . esc_attr( $post->post_name ) ); ?>>
            <?php the_content(); ?>
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
    get_footer();
endif; ?>