<?php
/**
 * 添付ファイルは投稿タイプattachmentとしてpostやpageと同様に扱われるため、その一つ一つに
 * 個別ページが存在しています。
 * このテーマではその必要がないと判断したため、親投稿であるそのファイルが含まれる投稿のページに
 * リダイレクトしています。
 * 尚、ここでの添付ファイルは画像、動画、pdfといったメディアライブラリで管理されるコンテンツ
 * のことです。
 */
if( $post->post_parent ) {
    wp_safe_redirect( get_permalink( $post->post_parent ), 301 );
    exit;
} else {
    wp_safe_redirect( home_url(), 302 );
    exit;
}
?>