<?php
/*
Template Name: 最新ページ
Description: 投稿タイプ中で最新のページにリダイレクトするページ
*/
if( have_posts() ) :
    while( have_posts() ) :
        the_post();
        $slug = basename( get_the_permalink() );
        if( post_type_exists( $slug ) ) {
            $post_type = $slug;
        } else {
            $post_type = 'post';
        }
        $arg = array(
            'posts_per_page' => '1',
            'orderby' => 'post_date',
            'order' => 'DESC',
            'post_type' => $post_type,
            'post_status' => 'publish',
        );
        $la_query = new WP_Query( $arg );
        if( $la_query->have_posts() ) :
            while( $la_query->have_posts() ) :
                $la_query->the_post();
                wp_safe_redirect( get_permalink(), 302 );
            endwhile;
            wp_reset_postdata();
        else:
get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1><?php echo the_title(); ?></h1>
    <div class="row justify-content-end info_common_1">
        <p class="info_common_1">作成日 <?php the_time( 'Y年n月j日' ); ?></p>
        <p class="info_common_1">更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
    </div>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div <?php post_class( 'content col-lg-8 common_1 ' . esc_html( $post->post_name ) ); ?>>
            <?php the_content(); ?>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
<?php get_footer();
        endif;
    endwhile;
else:
    wp_safe_redirect( home_url(), 302 );
endif; ?>