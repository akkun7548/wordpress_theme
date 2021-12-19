<?php
/**
 * このファイルは投稿がリスト形式で出力されるとき、summaryを指定された場合に出力に
 * 使用されます。
 * 現状ではサムネイルが正方形である前提でスタイリングしているため、要改善です。
 */
if ( has_post_thumbnail() ) {
    $thumb = get_the_post_thumbnail();
} else {
    $thumb = '<img src="' . esc_url( yadoken_first_image() ) . '" alt="' . esc_attr( get_the_title() ) . '">';
}
?>
<article class="summary stripe">
    <a href="<?php the_permalink(); ?>" class="row d-sm-flex">
        <div class="col-sm-3 d-sm-flex align-items-center thumb">
            <figure>
                <?php echo $thumb; ?>
            </figure>
        </div>
        <div class="col-sm-9 d-sm-flex align-items-start flex-column">
            <h3><?php the_title(); ?></h3>
            <div class="my-auto excerpt">
                <?php the_excerpt(); ?>
            </div>
            <div class="row info">
                <p>投稿日：<?php yadoken_date_link( get_option( 'date_format' ), '<object>', '</object>' ); ?></p>
                <?php yadoken_author_link('<p>著者：<object>', '、', '</object></p>'); ?>
                <?php the_tags( '<p>タグ：<object>', '、', '</object></p>' ); ?>
                <p>カテゴリー：<object><?php the_category( '、' ) ?></object></p>
            </div>
        </div>
    </a>
</article>
