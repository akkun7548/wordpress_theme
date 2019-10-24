<?php
if( have_posts() ) :
    while( have_posts() ) :
    the_post();
    get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1><?php the_title(); ?></h1>
    <?php if( ! is_page( array( 'report', 'internalreport', 'minutes' ) ) ) : ?>
    <div class="row justify-content-end info_common_1">
        <p class="info_common_1">作成日 <?php the_time( 'Y年n月j日' ); ?></p>
        <p class="info_common_1">更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
    </div>
    <?php endif; ?>
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
    <?php
    get_footer();
    endwhile;
else:
    wp_safe_redirect( home_url(), 302 );
endif; ?>