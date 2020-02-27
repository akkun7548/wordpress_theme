<?php
/**
 * このファイルは検索フォームを出力する際に使用されます。
 * wordpressデフォルトの検索フォームをカスタマイズする必要があったため作成しました。
 * また他の部品テンプレートと異なりtemplate-partsディレクトリに移動していないのは、
 * get_search_form()というwordpress関数で呼び出されるテンプレートの場所がここに限定されて
 * いるためです。
 */
global $yadoken_searchform;
$keys = array(
	'post_type' => 'post',
);
$arr = array();
foreach( $keys as $key => $value ) {
	if( $yadoken_searchform[$key] ) {
		$arr[$key] = $yadoken_searchform[$key];
	} else {
		if( is_singular() ) {
			global $yadoken_query;
			if( empty( $yadoken_query ) ) {
				$arr[$key] = $value;
			} else {
				$arr[$key] = $yadoken_query->get( $key );
			}
		} else {
			global $wp_query;
			$arr[$key] = $wp_query->get( $key );
			//デフォルト値を設定した場合でも、空文字列が返される場合があったためempty()での判定に変更しています。
			if( empty( $arr[$key] ) ) {
				$arr[$key] = $value;
			}
		}
	}
}
//配列を展開
extract( $arr );
if( is_array( $post_type ) ) {
	$post_type = reset( $post_type );
}
$name = '';
if( $obj = get_post_type_object( $post_type ) ) {
	$name = $obj->labels->name . 'の';
}
?>
<div class="row justify-content-end searchform stripe">
	<form role="search" method="get" action="<?php echo esc_url( home_url() ); ?>">
		<input type="hidden" value="<?php echo esc_attr( $post_type ); ?>" name="post_type">
		<label>
			<span class="screen-reader-text"><?php echo esc_html( $name ); ?>検索</span>
			<input type="search" class="search-field" placeholder="<?php echo esc_html( $name ); ?>検索 &hellip;" value="" name="s" />
		</label>
		<input type="submit" class="search-submit" value="検索" />
	</form>
</div>
