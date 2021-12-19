<?php
/**
 * wordpress設定
 * 
 * 主にwordpressの動作を変更する関数をまとめたファイルです。
 */


/**
 * テーマでサポートする機能の設定
 * 
 * テーマでサポートする機能を設定している他、メニューの登録も行っています。
 */
add_action( 'after_setup_theme', 'yadoken_after_setup_theme' );
function yadoken_after_setup_theme() {
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'post-thumbnails' );

  /**
   * 画像サイズpost-thumbnailを設定。yadoken_first_image()やget_post_thumbnail_url()
   * などで使用しているため、削除しないようにしてください。
   */
  set_post_thumbnail_size( 600, 600, true );
  add_theme_support( 'wp-block-styles' );
  add_theme_support( 'html5', array( 'gallery', 'caption', 'script', 'style', 'comment-list', 'comment-form' ) );
  register_nav_menus( array(
    'header-nav' => 'ヘッダーメニュー',
    'footer-nav' => 'フッターメニュー'
    )
  );
}


/**
 * ループの取得内容を変更(固定ページ・管理画面以外)
 * 
 * 固定ページではこれよりも先に投稿が取得されています。
 * 
 * ニュースアーカイブの記事の取得数変更を廃止しました。
 * 
 * @param WP_Query $this
 */
add_action( 'pre_get_posts', 'yadoken_pre_get_posts' );
function yadoken_pre_get_posts( $query ) {

  /**
   * 管理ページ、メインループ以外のクエリを対象外にしています。
   * また、個別ページの場合を対象にした変更は行っていないため個別ページも除外しています。
   */
  if( is_admin() || ! $query->is_main_query() || $query->is_singular() ) {
    return;
  }

  /**投稿タイプを取得 */
  $post_type = $query->get( 'post_type' );

  /**
   * 複数の投稿タイプがクエリされていた場合は、配列の先頭の一つのみが取得されるように変更
   * 
   * このテーマ内でget_query_var( 'post_type' )を使用している部分ではpost_typeが文字列
   * であることを前提としているため、このコードを変更する時はそちらの修正も行ってください。
   */
  if( is_array( $post_type ) ) {
    $query->set( 'post_type', reset( $post_type ) );
  }

  /**
   * デフォルトではpost_typeが空だと投稿ページだけではなく、固定ページまで検索対象になってしまう
   * ため、投稿ページのみが対象になるように変更しています。
   */
  if( $query->is_search() && $post_type === '' ) {
    $query->set( 'post_type', 'post' );
  }

  //議事録がページ当たり20投稿分取得されるようにしています。
  if( $post_type === 'yadoken_minutes' ) {
    $query->set( 'posts_per_page', 20 );
  }
}


/**
 * テンプレートファイルが選択される直前に$wp_queryの中身を変更して動作を変える。
 */
add_action( 'template_redirect', 'yadoken_template_redirect' );
function yadoken_template_redirect() {

  /**
   * WP_Rewriteではsearch_baseが設定されているにも関わらず、検索結果ページはデフォルトで
   * GETの羅列になっているため、リダイレクトしています。
   * また、post_typeのGETの値だけは残すようにしています。
   * パーマリンク構造が存在しないときはリライトルールも生成されないため無効化しています。
   * 検索文字列が空だった場合は、当該投稿タイプ全ての記事を表示するページにリダイレクトするように
   * 変更しています。
   */
  if( is_search() ) {

    /**
     * GETパラメーターにsが存在、つまりURLがリライトを受けていない場合かつ
     * GETパラメーターのsが空文字列ではない場合かつ
     * パーマリンク構造が設定されている場合
     */
    if( ! empty( $_GET['s'] ) && get_option( 'permalink_structure' ) ) {
      global $wp_rewrite;
      $s = urlencode( get_query_var( 's', '' ) );
      $get = isset( $_GET['post_type'] ) ? '?post_type=' . get_query_var( 'post_type', '' ) : '';
      wp_safe_redirect( home_url( '/' . $wp_rewrite->search_base . '/' . $s . $get ) );
      exit;
    }

    /**
     * クエリの検索文字列が空文字列だった場合 
     * 
     * search_baseを利用したURLにリダイレクト後の状態では検索文字列はGETパラメーター
     * ではなくなるため、その有無に関わらず$wp_queryからクエリ変数として取得しています。
     */
    if( empty( get_query_var( 's', '' ) ) ) {

      /**投稿タイプ毎に全記事アーカイブにリダイレクト */
      if( isset( $_GET['post_type'] ) ) {
        $url = get_post_type_archive_link( get_query_var( 'post_type', '' ) );

      //投稿タイプ未設定の場合はpostの記事が指定されるため、postの全記事アーカイブにリダイレクト
      } else {
        $url = get_post_type_archive_link( 'post' );
      }
      wp_safe_redirect( $url );
      exit;
    }
  }
}


/**
 * Gutenbergの「クラシック」から使用出来るTinyMCEエディタにテーブル編集機能を追加する。
 * 
 * 編集画面のソースからWordpressに同梱されているTinyMCEのバージョンを確認して、更新があった
 * 場合は以下のサイトから適切なバージョンを探してcdnのリンクを更新しましょう。
 * 
 * https://cdnjs.com/libraries/tinymce/4.9.6
 * 
 * @param array $external_plugins  追加プラグインの配列
 * @return array  追加プラグインの配列
 */
add_filter( 'mce_external_plugins', 'yadoken_mce_external_plugins' );
function yadoken_mce_external_plugins( $plugins ) {
  $plugins['table'] = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.6/plugins/table/plugin.min.js';
  return $plugins;
}


/**
 * TinyMCEエディタににテーブル編集用のボタンを追加します。
 * 
 * @param array $buttons     ボタンの要素の配列
 * @param string $editor_id  エディタの識別子
 * @return array  ボタンの要素の配列
 */
add_filter( 'mce_buttons', 'yadoken_mce_buttons' );
function yadoken_mce_buttons( $buttons ) {
  $buttons[] = 'table';
  return $buttons;
}

?>