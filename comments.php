<?php
/**
 * このファイルはcomments_template()で呼び出されます。
 * コメントをログイン必須とした場合、非ログイン時にログイン画面へのリンクが表示される
 * のですが、セキュリティの都合上このリンクは非公開にしたいので削除しました。
 */
if( have_comments() ) :
$list_args = array(
    'avatar_size' => '36',
    'style' => 'ol'
); ?>
<h2 class="comment-title">コメント</h2>
<ol class="comment-list">
    <?php wp_list_comments( $list_args ); ?>
</ol>
<?php
endif;
$form_args = array(
    'must_log_in' => '<p class="must-log-in">コメントを投稿するにはログインしてください。</p>',
    'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
    'title_reply_after' => '</h2>'
);
comment_form( $form_args ); ?>
