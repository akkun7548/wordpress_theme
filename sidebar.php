<?php
/**
 * このファイルはサイドバーを表示するために使用されます。
 * 固定ウィジェットとスクロール時に追従してくるウィジェットを表示します。
 * 外観 > ウィジェット から設定できます。
 */
if( is_active_sidebar( 'main-sidebar' ) ) {
    dynamic_sidebar( 'main-sidebar' );
}
if( is_active_sidebar( 'sticky-sidebar' ) ) { ?>
    <div class="sticky"> <?php
        dynamic_sidebar( 'sticky-sidebar' ); ?>
    </div><?php
}
?>
