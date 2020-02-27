<?php
/**
 * このファイルはソートするためのプルダウンメニューを出力するのに使用されます。
 * ソートはGETでパラメーターを送信することにより行っています。
 */
/**
 * ページネーションと同様に個別ページではサブループが存在しない場合はソート対象、記事件数が
 * ないため何も出力しません。
 * 
 */
if( is_singular() ) {
    global $yadoken_query;
    if( empty( $yadoken_query ) ) {
        return;
    } else {
        $post_count = '表示件数：' . $yadoken_query->post_count . '件';
    }
} else {
    $post_count = '表示件数：' . $wp_query->post_count . '件';
}
//ソート後のリンクに反映されるGETパラメーター
$keys = array( 's', 'post_type' );
//出力するHTML
$str = '';
/**
 * filter_inputは配列のGETに対してfalseを返します。配列が必要なクエリ変数があった場合は
 * 適宜変更をお願いします。
 */
foreach( $keys as $key ) {
    if( $value = wp_unslash( filter_input( INPUT_GET, $key ) ) ) {
        $str .= '<input type="hidden" value="' . esc_attr( $value ) . '" name="' . esc_attr( $key ) . '">' . "\n";
    }
}
?>
<div class="row justify-content-between align-items-center searchform stripe">
    <p><?php echo esc_html( $post_count ); ?></p>
    <form role="select" method="get" class="ml-auto" action="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>">
        <?php echo $str; ?>
        <select name="orderby">
            <option value="date">投稿日</option>
            <option value="modified">更新日</option>
            <option value="title">タイトル</option>
        </select>
        <select name="order">
            <option value="DESC">降順</option>
            <option value="ASC">昇順</option>
        </select>
        <input type="submit" value="並べ替え">
    </form>
</div>
