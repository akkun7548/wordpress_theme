<?php if( have_comments() ) : ?>
<h2 class="comment">コメント</h2>
<div class="comment-list">
    <?php wp_list_comments( array(
        'avatar_size' => '36',
        'style' => 'div',
        'format' => 'html5'
    ) ); ?>
</div>
<?php endif;
$commenter = wp_get_current_commenter();
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true' required" : '' );
$required_text = __( '* が付いている欄は必須項目です。' );
$args = array(
    'id_form' => 'commentform',
    'id_submit' => 'submit',
    'title_reply' => __( 'コメントを残す' ),
    'title_reply_to' => __( '%sにコメントを残す' ),
    'cancel_reply_link' => __( 'コメントをキャンセルする' ),
    'label_submit' => '送信',
    'comment_field' =>
        '<p class="comment-form-comment">' . "\n" .
        '<textarea id="comment" name="comment" aria-required="true" required placeholder="＊COMMENT" /></textarea>' . "\n" .
        '</p>',
    'must_log_in' =>
        '<p class="must-log-in">' . "\n" .
        sprintf(
            __( 'コメントを投稿するにはログインしてください。' ),
            wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
        ) . "\n" .
        '</p>',
    'logged_in_as' =>
        '<p class="logged-in-as">' . "\n" .
        sprintf(
            __( '<a href="%1$s">%2$s</a>さんとしてログイン中です。<a href="%3$s" title="ログアウト">ログアウト</a>しますか？' ),
            admin_url( 'profile.php' ),
            $user_identity,
            wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
        ) . "\n" .
        '</p>',
    'comment_notes_before' =>
        '<p class="comment-notes">' .
        __( 'メールアドレスが公開されることはありません。' ) . ( $req ? $required_text : '' ) .
        '</p>',
    'comment_notes_after'  => '<p>' . __( '内容を確認してから送信してください。' ) . '</p>',
    'fields' => apply_filters(
        'comment_form_default_fields',
        array(
            'author' =>
                '<p class="comment-form-author">' . "\n" .
                '<label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' . "\n" .
                ( $req ? '<span class="required">*</span>' . "\n" : '' ) .
                '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' . "\n" .
                '</p>',
            'email' =>
                '<p class="comment-form-email">' . "\n" .
                '<label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' . "\n" .
                ( $req ? '<span class="required">*</span>' . "\n" : '' ) .
                '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' . "\n" .
                '</p>',
            'url' =>
                '<p class="comment-form-url">' . "\n" .
                '<label for="url">' . __( 'Website', 'domainreference' ) . '</label>' . "\n" .
                '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' . "\n" .
                '</p>'
            )
        )
    );
comment_form( $args ); ?>
