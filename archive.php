<?php
/**
 * 通常投稿(post)や複数の投稿タイプがクエリされた著者アーカイブやカテゴリー、タグアーカイブ、
 * 日付アーカイブはこのファイルを用いて表示されます。
 */
$name = yadoken_post_type_name( yadoken_post_type(), ' & ' );
get_header();
?>
<div class="title_common_1">
    <h1><?php yadoken_archive_title( $name );?></h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content common_1">
        <?php
        get_search_form();
        get_template_part( 'template-parts/sortmenu' );
        if( have_posts() ) :
            while( have_posts() ) :
                the_post();
                yadoken_display_post();
            endwhile;
            echo do_shortcode( '[pagination]' );
        else : ?>
            <p>該当する<?php echo esc_html( $name ); ?>はありません。</p>
        <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>