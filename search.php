<?php
/**
 * このファイルは検索結果を表示する際に使用されます。
 */
$result = get_search_query() . ' (' . $wp_query->found_posts . '件)';
get_header();
?>
<div class="title_common_1">
    <h1>検索結果：<?php echo esc_html( $result ); ?></h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content common_1">
        <?php
        get_search_form();
        get_template_part( 'template-parts/sortmenu' );
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                yadoken_display_post();
            endwhile;
            echo do_shortcode( '[pagination]' );
        else : ?>
            <p>該当する<?php echo yadoken_post_type_name( yadoken_post_type(), '、' ); ?>はありません。</p>
            <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>