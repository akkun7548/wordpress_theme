<?php
/**
 * ニュースのアーカイブは一部レイアウト変更のため、別テンプレートとしています。
 */
get_header();
$name = yadoken_post_type_name( 'yadoken_news' ); ?>
<div class="title">
    <h1><?php yadoken_archive_and_search_title( $name ); ?></h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse main-wrapper">
    <div class="col-lg-8 main-content">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                yadoken_display_post();
            endwhile;
            echo do_shortcode( '[pagination]' );
        else : ?>
            <P><?php echo esc_html( $name ); ?>はありません。</p>
        <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch main-sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>