<?php
if( $post->post_parent ) {
    wp_safe_redirect( get_permalink( $post->post_parent ), 301 );
    exit;
} else {
    wp_safe_redirect( home_url(), 302 );
    exit;
}
?>