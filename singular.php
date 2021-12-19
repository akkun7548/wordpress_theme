<?php
/**
 * 個別ページテンプレート
 * 
 * 固定ページ、投稿ページなど、単体の投稿がクエリされるページは、個別にテンプレートを作成している
 * ものを除きこちらのコードを利用して出力されるようになっています。
 */
get_header();
if( have_posts() ) :
    $date_format = get_option( 'date_format' );
    while( have_posts() ) :
        the_post(); ?>
<div class="title">
    <h1><?php the_title(); ?></h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row justify-content-end date">
    <p>公開日 <?php the_time( $date_format ); ?></p>
    <p>更新日 <?php the_modified_date( $date_format ); ?></p>
</div>
<div class="row d-lg-flex flex-row-reverse main-wrapper">
    <div <?php post_class( 'col-lg-8 main-content' ); ?>>
        <?php the_content(); ?>
        <div style="clear: both;"></div>
        <?php wp_link_pages(); ?>
        <?php comments_template(); ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch main-sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
    <?php
    endwhile;
else :
    wp_safe_redirect( home_url(), 302 );
    exit;
endif;
get_footer(); ?>