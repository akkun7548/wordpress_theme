<div class="row justify-content-end searchform stripe">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ); ?>">
		<input type="hidden" value="<?php global $yd_post_type; echo esc_html( $yd_post_type ); ?>" name="post_type" id="post_type">
		<label>
			<span class="screen-reader-text"><?php echo esc_html( get_post_type_object( $yd_post_type )->labels->name ); ?>の検索:</span>
			<input type="search" class="search-field" placeholder="検索 &hellip;" value="" name="s" />
		</label>
		<input type="submit" class="search-submit" value="検索" />
	</form>
</div>
