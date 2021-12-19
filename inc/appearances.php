<?php
/**
 * 外観
 * 
 * HTMLの構造やスタイリングなど、見た目に関係する出力を編集しています。
 * 
 * - nav_menu_css_class
 *  - ヘッダーメニューにbootstrap4対応のクラス属性を追加
 *  - ヘッダーメニュー以外のメニューのクラス属性を削除
 * 
 * - nav_menu_link_attributes
 *  - ヘッダーメニューにbootstrap4対応のクラスを追加
 * 
 * - nav_menu_item_id
 *  - 全メニューのliタグのidを削除
 * 
 * - class yadoken_walker_nav_menu
 *  - ヘッダーメニューのhtml構造、クラスを変更
 * 
 * - body_class
 *  - 共通1のスタイリングを適用するページのbodyタグにクラスを追加
 */


/**
 * メニューliタグのclass削除・追加
 * 
 * ヘッダーメニューと表示中のページが関係している時に当該の項目にactiveというクラスを追加して
 * 色を変更しています。active時の色はカスタマイザーで設定可能です。
 * 
 * yadoken_walker_nav_menuクラスと合わせてbootstrap4の対応を行っています。
 * クラス定義より簡便であるため、HTML属性のクラスの削除・追加・変更で対応可能な変更はこちらで
 * 行っていて、構造を変更する必要がある場合にのみ class yadoken_walker_nav_menu で変更しています。
 * 
 * 「表示中のページ」と「リンク先のページ」を分けて意識すると動作が分かりやすいと思います。
 * 
 * 別のフックでクラスが追加されているため、上書きするために優先度を下げています。
 * 
 * @param string[] $classes  liタグに付くcssクラスの配列
 * @param WP_Post  $item     メニューアイテムオブジェクト
 * @param stdClass $args     wp_nav_menu()の引数オブジェクト
 * @param int      $depth    メニューアイテムの深度
 * @return string[]  liタグに付くcssクラスの配列
 */
add_filter( 'nav_menu_css_class', function( $classes, $item, $args, $depth ) {

  // 配列を保存
  $_classes = $classes;

  // クラス属性値の配列を初期化
  $classes = array();

  // ヘッダーメニュー
  if( $args->theme_location === 'header-nav' ) {

    // 現在のページとメニューのリンク先の関係
    $related = false;

    /**
     * 「表示中のページ」が固定ページ、カスタム投稿タイプアーカイブページ(archive-*.php)の
     * 場合は、「リンク先のページ」にあればcurrent-menu-itemクラスが追加され、
     * 「リンク先のページ」いずれかとの関連を調べる必要はないため、対象から外しています。
     */
    if( ! is_404() && ! is_page() && ! yadoken_is_post_type_archive() ) {

      //「リンク先のページ」が個別ページの場合
      if( $item->type === 'post_type' ) {

        //「リンク先のページ」が投稿ページかつ「表示中のページ」が投稿(post)に関係するページの場合
        if( $item->object_id === get_option( 'page_for_posts' ) ) {
          $related = get_query_var( 'post_type', '' ) === '';
        }

      //「リンク先のページ」がアーカイブページの場合
      } elseif( $item->type === 'post_type_archive' ) {

        //「リンク先のページ」の投稿タイプが「表現中のページ」の投稿タイプと一致する場合
        $related = $item->object === get_query_var( 'post_type', '' );
      }

    }

    // 現在のページとリンク先のページの関係を示すクラス(他のフックにより自動追加)
    $current_menu_item = in_array( 'current-menu-item', $_classes, true );
    $current_menu_parent = in_array( 'current-menu-parent', $_classes, true );

    //bootstrap4に対応するためcurrent-menu-*をactiveに変換しつつ、上記で判断した関連性も反映しています。
    if( $related || $current_menu_item || $current_menu_parent ) {
      $classes[] = 'active';
    }

    //yadoken_walker_nav_menuと合わせて、bootstrap4対応のためのクラスを追加しています。
    $classes[] = 'nav-item';

    //$args->depthは最大階層数、$depthは現在の階層数です。
    if( $args->walker->has_children && ( ! (int) $args->depth || $args->depth > $depth + 1 ) ) {
      $classes[] = 'dropdown';
      $classes[] = 'd-md-inline-flex';
    }

    // メニュー内で階層が2以上
    if( (int) $item->menu_item_parent ) {
      $classes[] = 'pl-md-0';
      $classes[] = 'pl-3';
    }

  }

  return $classes;

}, 100, 4 );


/**
 * メニューaタグのattribute追加
 * 
 * bootstrap4のnavのaタグに対応するためのクラスを追加しています。
 * 上記「メニューliタグのclass削除・追加」と同様の目的です。
 * 
 * 別のフックでクラスが追加されているため、上書きするために優先度を下げています。
 * 
 * @param array    $atts   aタグの属性、属性値の連想配列
 * @param WP_Post  $item   メニューアイテムオブジェクト
 * @param stdClass $args   wp_nav_menu()の引数オブジェクト
 * @param int      $depth  メニューアイテムの深度(不使用)
 * @return array  aタグの属性、属性値の連想配列
 */
add_filter( 'nav_menu_link_attributes', function( $atts, $item, $args ) {

  // ヘッダーメニュー
  if( $args->theme_location === 'header-nav' ) {
    $atts['class'] = 'nav-link';
  }

  return $atts;

}, 100, 3 );


/**
 * メニューliタグのid 削除
 * 
 * 不要であるidを削除しています。
 * 
 * 別のフックでクラスが追加されているため、全削除するために優先度を下げています。
 * 
 * @param string   $menu_id  liタグのid
 * @param WP_Post  $item     メニューアイテムオブジェクト(不使用)
 * @param stdClass $args     wp_nav_menu()の引数オブジェクト(不使用)
 * @param int      $depth    メニューアイテムの深度(不使用)
 * @return string  liタグのid
 */
add_filter( 'nav_menu_item_id', function( $menu_id ) {

  return '';

}, 100 );


/**
 * メニュー生成のための walker 継承クラス定義
 * 
 * 通常のメニューの生成にはwalker_nav_menuクラスが使用されていますが、bootstrap4の
 * navbar対応で構造を変更する必要があった箇所のメゾットだけ上書きしています。
 * 
 * 参照：https://developer.wordpress.org/reference/classes/walker_nav_menu/
 */
class yadoken_walker_nav_menu extends walker_nav_menu {

  /**
   * 副項目を持つメニューのタグの開始側を生成する関数
   * 
   * 子ページのドロップダウンメニューを開くボタンを、親ページのリンクから分離しています。
   * 強引な実装のため、可能であれば代替案の検討をお願いします。
   * 
   * @param string   &$output  出力するHTML(参照渡し)
   * @param int      $depth    メニューアイテムの深度
   * @param stdClass $args     wp_nav_menu()の引数オブジェクト
   */
  public function start_lvl( &$output, $depth = 0, $args = null ) {

    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }

    $indent = str_repeat( $t, $depth );

    $classes = array( 'dropdown-menu' );

    //元のフィルターと用途が分かれると判断したため、名前を変更しました。
    $class_names = join( ' ', apply_filters( 'yadoken_nav_menu_submenu_css_class', $classes, $args, $depth ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

    //margin, padding、レスポンシブ対応もbootstrapを利用してここで行っています。
    $output .= '<a class="dropdown-toggle nav-link pl-1 mx-md-1 m-0" data-toggle="dropdown" role="button">
                <span class="d-md-none pl-md-0 pl-3">展開</span>
                </a>';
    $output .= "{$n}{$indent}<ul{$class_names} role='menu'>{$n}";
  }

}


/**
 * 共通スタイリング用のクラスをbodyタグに追加
 * 
 * style.cssを参照してください。
 * 
 * @param string[] $classes  bodyタグのクラス
 * @return string[]  bodyタグのクラス
 */
add_filter( 'body_class', function( $classes ) {

  // 共通1のスタイリングを適用しているページ
  if( is_singular( 'yadoken_news' ) || is_page() || is_home() || is_archive() || is_search() || is_404() ) {
    $classes[] = 'common_1';
  }

  return $classes;

});

?>