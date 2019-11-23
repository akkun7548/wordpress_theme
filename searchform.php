<?php
global $yd_searchform;
$name = '';
$status = '';
$keys = array(
	'post_type' => 'post',
	'post_status' => 'publish'
);
foreach( $keys as $key => $value ) {
	if( $yd_searchform[$key] ) {
		$arr[$key] = $yd_searchform[$key];
	} else {
		if( is_singular() ) {
			global $yd_query;
			if( empty( $yd_query ) ) {
				$arr[$key] = $value;
			} else {
				$arr[$key] = $yd_query->get( $key );
			}
		} else {
			global $wp_query;
			$arr[$key] = $wp_query->get( $key );
			if( empty( $arr[$key] ) ) {
				$arr[$key] = $value;
			}
		}
	}
}
extract( $arr );
$obj = get_post_type_object( $post_type );
if( $obj ) {
	$name = $obj->labels->name . 'の';
}
if( $post_status === 'pending' && is_user_logged_in() ) {
	$status = '<input type="hidden" value="pending" name="post_status">' . "\n";
}
?>
<div class="row justify-content-end searchform stripe">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ); ?>">
		<input type="hidden" value="<?php echo esc_attr( $post_type ); ?>" name="post_type" id="post_type">
		<?php echo $status; ?>
		<label>
			<span class="screen-reader-text"><?php echo esc_html( $name ); ?>検索:</span>
			<input type="search" class="search-field" placeholder="検索 &hellip;" value="" name="s" />
		</label>
		<input type="submit" class="search-submit" value="検索" />
	</form>
</div>
