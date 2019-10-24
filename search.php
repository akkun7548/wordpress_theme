<?php get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>検索結果：<?php echo get_search_query(); echo ' (' . esc_html( $wp_query->found_posts ) . '件)' ; ?></h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <?php
            echo do_shortcode( '[searchform post_type="' . get_query_var( 'post_type' ) . '"]' );
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    if( get_post_type() === 'post' ) :
                        get_template_part( 'summary' );
                    elseif( get_post_type() === 'news' ) :
                        get_template_part( 'title' );
                    elseif( get_post_type() === 'minutes' ) :
                        get_template_part( 'list' );
                    endif;
                endwhile; wp_reset_postdata();
            else :
                $name = esc_html( get_post_type_object( get_query_var( 'post_type' ) )->labels->name ); ?>
                <p>該当する<?php echo $name; ?>はありません。</p>
                <?php
            endif;
            echo do_shortcode( '[pagination 
                yd_format="&paged=%#%" 
                yd_esc_url=false
            ]' ); ?>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
<?php get_footer(); ?>