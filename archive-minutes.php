<?php get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>
        <?php
        $name = esc_html( get_post_type_object( 'minutes' )->labels->name );
        if( is_tax() ) {
            single_term_title();
        } elseif( is_day() ) {
            echo get_the_time( 'Y年n月j日' ) . "の" . $name;
        } elseif( is_month() ) {
            echo get_the_time( 'Y年n月' ) . "の" . $name;
        } elseif( is_year() ) {
            echo get_the_time( 'Y年' ) . "の" . $name;
        } elseif( is_author() ) {
            echo $name . "　編集者：" . esc_html( get_queried_object()->data->display_name );
        } else {
            echo $name . "アーカイブ";
        }
        ?>
    </h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <?php
            echo do_shortcode( '[searchform post_type="minutes"]' );
            if( have_posts() ) :
                while( have_posts() ) :
                    the_post();
                    get_template_part( 'list' );
                endwhile; wp_reset_postdata();
            else : ?>
                <p>該当するお知らせはありません。</p>
            <?php
            endif;
            echo do_shortcode( '[pagination]' ); ?>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
<?php get_footer(); ?>