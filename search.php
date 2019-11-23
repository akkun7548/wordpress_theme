<?php
$result = get_search_query() . ' (' . $wp_query->found_posts . '件)';
get_header();
?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>検索結果：<?php echo esc_html( $result ); ?></h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <?php
            get_search_form();
            get_template_part( 'sortmenu' );
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    yd_display_post();
                endwhile;
                echo do_shortcode( '[pagination]' );
            else : ?>
                <p>該当する<?php yd_post_type_name( yd_post_type() ); ?>はありません。</p>
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