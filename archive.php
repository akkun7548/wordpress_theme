<?php get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>
        <?php
        if( get_post_type() === 'post' ) {
            $name = esc_html( get_post_type_object( 'post' )->labels->name );
            if( is_category() ) {
                single_cat_title( 'カテゴリー：' );
            } elseif( is_tag() ) {
                single_tag_title( 'タグ：' );
            } elseif( is_tax() ) {
                single_term_title( 'ターム：' );
            } elseif( is_day() ) {
                echo get_the_time( 'Y年n月j日' ) . 'の' . $name;
            } elseif( is_month() ) {
                echo get_the_time( 'Y年n月' ) . 'の' . $name;
            } elseif( is_year() ) {
                echo get_the_time( 'Y年' ) . 'の' . $name;
            } elseif( is_author() ) {
                echo esc_html( get_queried_object()->data->display_name ) . 'さんの' . $name;
            } else {
                echo $name . "アーカイブ";
            }
        } else {
            if( is_tax() ) {
                single_term_title();
            } elseif( is_day() ) {
                echo "日別アーカイブ：" . get_the_time( 'Y年n月j日' );
            } elseif( is_month() ) {
                echo "月別アーカイブ：" . get_the_time( 'Y年n月' );
            } elseif( is_year() ) {
                echo "年別アーカイブ：" . get_the_time( 'Y年' );
            } elseif( is_author() ) {
                echo "投稿者アーカイブ：" . esc_html( get_queried_object()->data->display_name );
            } else {
                echo "アーカイブ";
            }
        }
        ?>
    </h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    if( get_post_type() === 'post' ) :
                        get_template_part( 'summary' );
                    else :
                        get_template_part( 'title' );
                    endif;
                endwhile; wp_reset_postdata();
            else : ?>
                <p>該当する記事がありません。</p>
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