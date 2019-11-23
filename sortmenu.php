<?php
global $_GET;
$str = '';
if( is_singular() ) {
    global $yd_query;
    if( empty( $yd_query ) ) {
        return;
    } else {
        $post_count = '表示件数：' . $yd_query->post_count . '件';
    }
} else {
    $post_count = '表示件数：' . $wp_query->post_count . '件';
}
$action = strtok( get_pagenum_link( get_query_var( 'paged', 1 ) ), '?' );
$keys = array( 's', 'post_type', 'post_status' );
foreach( $keys as $key ) {
    if( isset( $_GET[$key] ) ) {
        $value = wp_unslash( $_GET[$key] );
        if( is_array( $value ) ) {
            $value = '';
        }
        $str .= '<input type="hidden" value="' . esc_attr( $value ) . '" name="' . esc_attr( $key ) . '">' . "\n";
    }
}
?>
<div class="row justify-content-between searchform stripe">
    <p><?php echo esc_html( $post_count ); ?></p>
    <form role="select" method="get" action="<?php echo esc_url( $action ); ?>">
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
