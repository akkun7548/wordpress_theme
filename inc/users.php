<?php
/**
 * ユーザー設定
 * 
 * ユーザー関係の設定をまとめたファイルです。ユーザー関係以外の要素も含まれる関数は元の
 * こちらにはまとめていません。
 */

/**
 * adminbarの「こんにちは、〇〇さん」の「こんにちは、」を消去
 * 
 * ログイン時にサイト上部に表示されるバーの名前の部分に「こんにちは、」が表示されていると、
 * 画面の幅によっては文字が折り返したりはみ出したりしてしまうため、削除しました。
 * 
 * @param WP_Admin_Bar $wp_admin_bar  WP_Admin_Barオブジェクト
 */
add_filter( 'admin_bar_menu', 'yadoken_delete_howdy', 25 );
function yadoken_delete_howdy( $wp_admin_bar ) {
  $my_account = $wp_admin_bar->get_node( 'my-account' );
  $newtitle = str_replace( 'こんにちは、', '', $my_account->title );
  $wp_admin_bar->add_node(
    array(
      'id' => 'my-account',
      'title' => $newtitle
    )
  );
}

/**
 * ログイン後にリダイレクト
 * 
 * @param string $user_login  ユーザー名
 * @param WP_User $user       ユーザーオブジェクト
 */
add_action( 'wp_login', 'yadoken_login', 10, 2 );
function yadoken_login( $user_login, $user ) {
  if( $user->roles[0] !== 'administrator' && $user->roles[0] !== 'editor' ) {
    if( $page_for_posts = get_option( 'page_for_posts' ) ) {
        $permalink = get_permalink( $page_for_posts );
    } else {
        $permalink = home_url();
    }
    wp_safe_redirect( $permalink );
    exit;
  }
}

/**
 * メディアライブラリの取得画像を変更
 * 
 * 本人がアップロードした画像以外がメディアライブラリで見れないようにしています。
 * 
 * @param array $query  クエリ変数の配列
 * @return array  クエリ変数の配列
 */
add_action( 'ajax_query_attachments_args', 'yadoken_display_only_self_uploaded_medias' );
function yadoken_display_only_self_uploaded_medias( $query ) {
  if ( $user = wp_get_current_user() ) {
      $query['author'] = $user->ID;
  }
  return $query;
}

/**
 * 登録フォームリンク無効化
 * 
 * ユーザー登録可能にした時、メタ情報のウィジェットで登録ページへのリンクが表示されるのは
 * 思わぬ事故に繋がる恐れがあったため、絶対に表示されないように変更しました。
 * 
 * @param string $link  登録ページへのリンク
 * @return string  変更後のリンク
 */
add_filter( 'register', 'yadoken_register' );
function yadoken_register( $link ) {
  return '';
}

/**
 * ログイン/ログアウトリンク無効化
 * 
 * メタ情報のウィジェットでログインページへのリンクが表示されないようにしました。
 * 
 * @param string $link  ログイン/ログアウトページへのリンク
 * @return string  変更後のリンク
 */
add_filter( 'loginout', 'yadoken_loginout' );
function yadoken_loginout( $link ) {
  return preg_replace( '/<a.+?>(.*?)<\/a>/', '$1 (無効化中)', $link );
}

?>