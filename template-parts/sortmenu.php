<?php
/**
 * このファイルはソートするためのプルダウンメニューを出力するのに使用されます。
 * ソートはGETでパラメーターを送信することにより行っています。
 */

/**
 * ソート後のリンクに反映されるGETパラメーター
 * 
 * クエリ変数として有効なものを指定してください。
 */
$keys = array( 's', 'post_type' );
/**
 * 現在のソートの状態を取得
 * 
 * 可変変数として利用しているため、変数名を変更する際は注意してください。
 */
$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : '';
$order   = isset( $_GET['order'] ) ? $_GET['order'] : '';
/**ソートに利用するGETパラメーターとその値 */
$select  = array(
    'orderby' => array(
        'date'     => '投稿日',
        'modified' => '更新日',
        'title'    => 'タイトル'    
    ),
    'order'   => array(
        'DESC' => '降順',
        'ASC'  => '昇順'    
    )
);
/**get_pagenum_link()は内部でエスケープを行っています。 */ ?>
<form role="select" method="get" action="<?php echo get_pagenum_link( get_query_var( 'paged', 1 ) ); ?>">
    <?php
    /**取得したGETパラメーターをinputタグとして出力 */
    foreach( $keys as $key ) {
        if( isset( $_GET[$key] ) ) {
            $value = get_query_var( $key, '' );
            echo "\t" . '<input type="hidden" value="' . esc_attr( $value ) . '" name="' . esc_attr( $key ) . '">' . "\n";
        }
    }
    /**ソートに利用するプルダウンメニューを出力 */
    foreach( $select as $name => $array ) {
        echo "\t" . '<select name="' . esc_attr( $name ) . '">' . "\n";
        foreach( $array as $value => $title ) {
            /**可変変数${}を利用しています。 */
            echo "\t\t" . '<option value="' . esc_attr( $value ) . '"' . selected( ${$name}, $value, false ) . '>' . esc_html( $title ) . '</option>' . "\n";
        }
        echo "\t" . '</select>' . "\n";
    } ?>
    <input type="submit" value="並べ替え">
</form>
