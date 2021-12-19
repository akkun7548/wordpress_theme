<?php
/**
 * このファイルは検索フォームを出力する際に使用されます。
 * wordpressデフォルトの検索フォームに投稿タイプのGETを送信する機能を追加するために作成しました。
 * 
 * 他の部品テンプレートと異なりtemplate-partsディレクトリに移動していないのは、
 * get_search_form()というwordpress関数で呼び出されるテンプレートの場所がここに限定されて
 * いるためです。
 */

 /**
  * $argsはget_search_form()の引数であり、呼び出し元の変数となっています。
  */
 if( isset( $args['aria_label'] ) && $args['aria_label'] ) {
	 $aria_label = 'aria-label="' . esc_attr( $args['aria_label'] ) . '" ';
 } else {
	 $aria_label = '';
 }
/**
 * 個別ページの場合、サブループが生成されていればその投稿タイプ、されていなければ空配列を指定
 * しています。
 */
if( is_singular() ) {
	global $yadoken_query;
	$post_type = isset( $yadoken_query ) ? $yadoken_query->get( 'post_type' ) : '' ;
} else {
	global $wp_query;
	$post_type = $wp_query->get( 'post_type' );
}
/**
 * 投稿タイプ名取得
 * 
 * 空配列の時はpostのネームラベルが取得されるようになっています。
 */
$name = yadoken_post_type_name( $post_type );
/**
 * 投稿タイプGETパラメーター送信
 * 
 * 空配列の時はpostがクエリされるようになっているため、GETパラメーターは送信しません。
 */
$input = in_array( $post_type, array( '', 'post' ), true ) ? '' : '<input type="hidden" value="' . esc_attr( $post_type ) . '" name="post_type">';
?>
<form role="search" <?php echo $aria_label; ?>method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo esc_html( $name ); ?>の検索</span>
		<input type="search" class="search-field" placeholder="<?php echo esc_html( $name ); ?>の検索 &hellip;" value="" name="s" />
	</label>
	<?php echo $input; ?>
	<input type="submit" class="search-submit" value="検索" />
</form>
