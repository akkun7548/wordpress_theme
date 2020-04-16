<?php
/**
 * functions.phpでニュースのアーカイブの内、日付、著者アーカイブ以外は posts_per_page が
 * １になるように設定し、このファイルで最新の投稿と全く同じページが表示されるように
 * 設定しています。($wp_query->post_count == 1)
 * また、投稿数が２以上もしくは日付、著者アーカイブの場合はアーカイブページと同様に機能します。
 */
get_header();
if( is_date() || is_author() || $wp_query->post_count > 1 ) :
    $name = yadoken_post_type_name( 'yadoken_news' );
    ?>
<div class="title_common_1">
    <h1><?php yadoken_archive_title( $name ); ?></h1>
</div>
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
            <P><?php echo esc_html( $name ); ?>はありません。</p>                
        <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
    <?php
elseif( $wp_query->post_count === 1 ) :
    while( have_posts() ) :
        the_post();
        ?>
<div class="title_common_1">
    <h1><?php the_title(); ?></h1>
</div>
<div class="row justify-content-end info_common_1">
    <p>作成日 <?php the_time( 'Y年n月j日' ); ?></p>
    <p>更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
</div>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div <?php post_class( 'col-lg-8 content common_1' ); ?>>
        <?php the_content(); ?>
        <div style="clear: both;"></div>
        <?php wp_link_pages(); ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
        <?php
    endwhile;
else :
    $name = yadoken_post_type_name( 'yadoken_news' );
    ?>
<div class="title_common_1">
    <h1><?php echo esc_html( $name ); ?></h1>
</div>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content common_1">
        <p><?php echo esc_html( $name ); ?>はありません。</p>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
    <?php
endif;
get_footer(); ?>