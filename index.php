<?php
/**
 * このファイルはどのテンプレートファイルにも分岐しなかった場合に用いられます。
 * 投稿の数もなし、単体、複数の場合がそれぞれ考えられるため、条件分岐させています。
 */
get_header();
if( $wp_query->post_count > 1 ) : ?>
<div class="title_common_1">
    <h1>アーカイブ</h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content common_1">
        <?php
        echo do_shortcode( '[searchform]' );
        get_template_part( 'template-parts/sortmenu' );
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                yadoken_display_post();
            endwhile;
            echo do_shortcode( '[pagination]' );
        else : ?>
            <P>該当する記事はありません。</p>                
        <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php
elseif( $wp_query->post_count === 1 ) :
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
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
        <?php
        endwhile;
    else :
        wp_safe_redirect( home_url(), 302 );
        exit;
    endif;
else : ?>
<div class="title_common_1">
    <h1>エラー</h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content common_1">
        <p>ご指定されたページは見つかりませんでした。</p>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php
endif;
get_footer(); ?>