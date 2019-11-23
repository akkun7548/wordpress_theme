<?php
$post_type = yd_post_type();
if( $post_type === 'post' ) {
    $title = get_queried_object()->post_title;
} else {
    $name = yd_post_type_name( $post_type, false );
    $title = $name . '一覧';
}
get_header();
?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1><?php echo esc_html( $title ); ?></h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <?php
            get_search_form();
            get_template_part( 'sortmenu' );
            if( have_posts() ) :
                while( have_posts() ) :
                    the_post();
                    yd_display_post();
                endwhile;
                echo do_shortcode( '[pagination]' );
            else:
                if( empty( $name ) ) {
                    $name = yd_post_type_name( $post_type, false );
                } ?>
                <p>該当する<?php echo esc_html( $name ); ?>はありません。</p>
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
<?php get_footer(); ?>