<?php
/**
 * このファイルは投稿がリスト形式で表示されたときに、titleを指定された場合使用されます。
 */
$str = '';
$obj = get_post_type_object( get_post_type() );
if( $obj ) {
    $str = $obj->labels->name . '：';
} ?>
<a href="<?php the_permalink(); ?>" class="row title">
    <div class="col-12 col_title">
        <div class="row align-items-end row_title">
            <h2 class="mr-auto"><?php echo esc_html( $str ); the_title(); ?></h2>
            <p>掲載日: <?php the_time( 'Y年n月j日' ); ?></p>
            <P>更新日: <?php the_modified_date( 'Y年n月j日' ); ?></P>
        </div>
    </div>
</a>
