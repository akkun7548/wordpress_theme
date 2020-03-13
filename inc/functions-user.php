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
 * 寄稿者の画像アップロード
 */
if ( current_user_can( 'contributor' ) && ! current_user_can( 'upload_files' ) ) {
  add_action( 'admin_init', 'yadoken_allow_contributor_uploads' );
}
function yadoken_allow_contributor_uploads() {
  $contributor = get_role( 'contributor' );
  $contributor->add_cap( 'upload_files' );
}

/**
 * 寄稿者の非公開記事表示権限追加
 */
if( current_user_can( 'contributor' ) && ( ! current_user_can( 'read_private_posts' ) || ! current_user_can( 'read_private_pages' ) )  ) {
  add_action( 'admin_init', 'yadoken_allow_contributor_read_privates' );
}
function yadoken_allow_contributor_read_privates() {
  $contributor = get_role( 'contributor' );
  $contributor->add_cap( 'read_private_posts' );
  $contributor->add_cap( 'read_private_pages' );
}

/**
 * 投稿者の非公開記事表示権限追加
 */
if( current_user_can( 'author' ) && ( ! current_user_can( 'read_private_posts' ) || ! current_user_can( 'read_private_pages' ) ) ) {
  add_action( 'admin_init', 'yadoken_allow_author_read_privates' );
}
function yadoken_allow_author_read_privates() {
  $author = get_role( 'author' );
  $author->add_cap( 'read_private_posts' );
  $author->add_cap( 'read_private_pages' );
}

/**
 * 投稿者の固定ページ下書き作成許可
 */
if( current_user_can( 'author' ) && ( ! current_user_can( 'edit_pages' ) || ! current_user_can( 'delete_pages' ) ) ) {
  add_action( 'admin_init', 'yadoken_allow_author_edit_and_delete_pages' );
}
function yadoken_allow_author_edit_and_delete_pages() {
  $author = get_role( 'author' );
  $author->add_cap( 'edit_pages' );
  $author->add_cap( 'delete_pages' );
}

/**
 * 投稿者の公開記事を全て非公開記事にする。
 * 
 * フィルター名は{$new_status}_{$post->post_type}です。
 * 
 * @param int $post_id   投稿ID
 * @param WP_Post $post  投稿オブジェクト
 */
if( current_user_can( 'author' ) ) {
  add_action( 'publish_post', 'yadoken_force_author_private_posts', 10, 2 );
}
function yadoken_force_author_private_posts( $ID, $post ) {
  $post->post_status = 'private';
  wp_update_post( $post );
}

/**
 * 全ての議事録を非公開にする。
 * 
 * フィルター名は{$new_status}_{$post->post_type}です。
 * 
 * @param int $post_id   投稿ID
 * @param WP_Post $post  投稿オブジェクト
 */
add_action( 'publish_minutes', 'yadoken_force_private_minutes', 10, 2 );
function yadoken_force_private_minutes( $post_id, $post ) {
  $post->post_status = 'private';
  wp_update_post( $post );
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
 * 内部ページのメニューをログインユーザーのみに表示
 * 
 * 投稿の状態がprivate(非公開)だった場合、その投稿を示すメニューアイテムを配列から消去しています。
 * 親項目が削除された場合はその状態に関わらず当該のメニューアイテムも削除されます。
 * 
 * @param array    $sorted_menu_items  ソート済みのメニューオブジェクトの配列
 * @param stdClass $args               wp_nav_menu()の引数オブジェクト
 * @return array  ソート済みのメニューオブジェクトの配列
 */
add_filter( 'wp_nav_menu_objects', 'yadoken_remove_private_post_menu' );
function yadoken_remove_private_post_menu( $sorted_menu_items ) {
  $unset_ids = array();
  foreach( $sorted_menu_items as $key => $item ) {
    if( $item->type == 'post_type' ) {
      $post = get_post( $item->object_id );
      $post_type = get_post_type_object( $post->post_type );
      if( $post->post_status == 'private' && ! current_user_can( $post_type->cap->read_private_posts ) ) {
        unset( $sorted_menu_items[$key] );
        $unset_ids[] = $item->ID;
        //この時点で配列から取り除かれるため、foreach内の残りの処理をスキップしています。
        continue;
      }
    }
    if( in_array( $item->menu_item_parent, $unset_ids ) ) {
      unset( $sorted_menu_items[$key] );
      $unset_ids[] = $item->ID;
    }
    //議事録アーカイブのアクセス権限に合わせてログイン判定としています。
    if( $item->type === 'post_type_archive' ) {
      if( $item->object === 'minutes' && ! is_user_logged_in() ) {
        unset( $sorted_menu_items[$key] );
        $unset_ids[] = $item->ID;
      }
    }
  }
  return $sorted_menu_items;
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