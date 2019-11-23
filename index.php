<?php
if( $wp_query->found_posts > 1 ) :
    get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>アーカイブ</h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <?php
            echo do_shortcode( '[searchform]' );
            get_template_part( 'sortmenu' );
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    yd_display_post();
                endwhile;
                echo do_shortcode( '[pagination]' );
            else : ?>
                <P>該当する記事はありません。</p>                
            <?php
            endif; ?>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
    <?php get_footer();
elseif( $wp_query->found_posts == 1 ) :
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
    else :
        wp_safe_redirect( home_url(), 302 );
        exit;
    endif;
    get_footer();
else :
    get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>エラー</h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <p>ご指定されたページは見つかりませんでした。</p>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
    <?php get_footer();
endif; ?>