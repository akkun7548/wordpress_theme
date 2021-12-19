<?php
/**
 * このファイルは取得された記事の件数などの情報を表示するのに利用されます。
 */

/**
 * ページネーションと同様に個別ページではサブループが存在しない場合は記事件数の必要性が
 * ないため何も出力しません。
 */
if( is_singular() ) {
    global $yadoken_query;
    if( empty( $yadoken_query ) ) {
        return;
    }
    $query = $yadoken_query;
} else {
    $query = $wp_query;
}
/**表示中の記事の中で一番上の記事 */
$from = ( max( 1, $query->get( 'paged' ) ) - 1 ) * $query->get( 'posts_per_page', get_option( 'posts_per_page' ) ) + 1;
/**表示中の記事の中で一番下の記事 */
$to   = $from + $query->post_count - 1;
echo '<p>' . esc_html( $query->found_posts ) . '件中' . esc_html( $from ) . '～' . esc_html( $to ) . '件目を表示中</p>' . "\n";
?>