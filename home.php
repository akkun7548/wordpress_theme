<?php
/**
 * このファイルはデフォルト投稿タイプのpostの投稿一覧を表示するために使用されます。
 * 設定 > 表示設定 > ホームページの設定 のラジオボタンで固定ページを選択しつつ、
 * 投稿ページの方で選択したページはこのファイルを元に表示されます。
 */
$post_type = yadoken_post_type();
$name = yadoken_post_type_name( $post_type, false, ' & ' );
if( $post_type === 'post' && ( $obj = get_queried_object() ) instanceof WP_Post ) {
    $title = $obj->post_title;
} else {
    $title = $name . '一覧';
}
get_header();
?>
<div class="title_common_1">
    <h1><?php echo esc_html( $title ); ?></h1>
</div>
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
        else: ?>
            <p><?php echo esc_html( $name ); ?>はありません。</p>
        <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>