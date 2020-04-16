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
  add_theme_support( 'html5', array( 'gallery', 'caption', 'script', 'style' ) );
  register_nav_menus( array(
    'header-nav' => 'ヘッダーメニュー',
    'footer-nav' => 'フッターメニュー'
    )
  );
}

/**
 * メニューliタグのclass削除・追加
 * 
 * ヘッダーメニューと表示中のページが関係している時に当該の項目にactiveというクラスを追加して
 * 色を変更しています。
 * active時の色はstyle.cssで設定しています。
 * また、yadoken_walker_nav_menuクラスと合わせてbootstrap4の対応を行っています。
 * クラス定義よりは簡便であるため、HTML属性のクラスの削除・追加・変更で対応可能な変更はこちらで
 * 行っていて、構造を変更する必要などがある場合にのみyadoken_walker_nav_menu で変更しています。
 * 
 * カスタマイズする時は、「表示中のページ」と「リンク先のページ」を分けて把握出来るようにしましょう。
 * 
 * @param string[] $classes  liタグに付くcssクラスの配列
 * @param WP_Post  $item     メニューアイテムオブジェクト
 * @param stdClass $args     wp_nav_menu()の引数オブジェクト
 * @param int      $depth    メニューアイテムの深度(不使用)
 * @return string[]  liタグに付くcssクラスの配列
 */
add_filter( 'nav_menu_css_class', 'yadoken_nav_menu_css_class', 100, 3 );
function yadoken_nav_menu_css_class( $classes, $item, $args ) {
  if( $args->theme_location === 'header-nav' ) {
    $related = false;
    /**
     * 「表示中のページ」が固定ページ、カスタム投稿タイプアーカイブページ(archive-*.php)の
     * 場合は、「リンク先のページ」にあればcurrent-menu-itemクラスが追加され、なくても
     * 「リンク先のページ」いずれかとの関連を調べる必要はないため、対象から外しています。
     */
    if( ! is_404() && ! is_page() && ! yadoken_is_post_type_archive() ) {
      //「リンク先のページ」が個別ページの場合
      if( $item->type === 'post_type' ) {
        //「リンク先のページ」投稿ページを「表示中のページ」で投稿タイプがpostのものと関連付けています。
        if( $item->object_id === get_option( 'page_for_posts' ) ) {
          $related = in_array( 'post', (array) yadoken_post_type(), true );
        }
      //「リンク先のページ」がアーカイブページの場合
      } elseif( $item->type === 'post_type_archive' ) {
        //「リンク先のページ」の投稿タイプが「表現中のページ」の投稿タイプと一致する場合に関連付けています。
        $related = in_array( $item->object, (array) yadoken_post_type(), true );
      }
    }
    $current_menu_item = in_array( 'current-menu-item', $classes, true );
    $current_menu_parent = in_array( 'current-menu-parent', $classes, true );
    //bootstrap4に対応するためcurrent-menu-*をactiveに変換しつつ、上記で判断した関連性も反映しています。
    if( $current_menu_item || $current_menu_parent || $related ) {
      $classes = array( 'active' );
    } else {
      $classes = array();
    }
    //yadoken_walker_nav_menuと合わせて、bootstrap4対応のためのクラスを追加しています。
    $classes[] = 'nav-item';
    if( $args->walker->has_children ) {
      $classes[] = 'dropdown';
      $classes[] = 'd-md-inline-flex';
    }
    if( (int) $item->menu_item_parent ) {
      $classes[] = 'pl-md-0';
      $classes[] = 'pl-3';
    }
  //header-nav以外ではクラスを全消去しています。
  } else {
    $classes = array();
  }
  return $classes;
}

/**
 * メニューaタグのattribute追加
 * 
 * bootstrap4のnavのaタグに対応するためのクラスを追加しています。
 * 上記「メニューliタグのclass削除・追加」と同様の目的です。
 * 
 * @param array    $atts   aタグの属性、属性値の連想配列
 * @param WP_Post  $item   メニューアイテムオブジェクト
 * @param stdClass $args   wp_nav_menu()の引数オブジェクト
 * @param int      $depth  メニューアイテムの深度(不使用)
 * @return array  aタグの属性、属性値の連想配列
 */
add_filter( 'nav_menu_link_attributes', 'yadoken_nav_menu_link_attributes', 100, 3 );
function yadoken_nav_menu_link_attributes( $atts, $item, $args ) {
  if( $args->theme_location === 'header-nav' ) {
    $atts['class'] = 'nav-link';
  }
  return $atts;
}

/**
 * メニューliタグのid 削除
 * 
 * 不要であるidを削除しています。
 * 
 * @param string   $menu_id  liタグのid
 * @param WP_Post  $item     メニューアイテムオブジェクト(不使用)
 * @param stdClass $args     wp_nav_menu()の引数オブジェクト(不使用)
 * @param int      $depth    メニューアイテムの深度(不使用)
 * @return string  liタグのid
 */
add_filter( 'nav_menu_item_id', 'yadoken_nav_menu_item_id', 100 );
function yadoken_nav_menu_item_id( $menu_id ) {
  return '';
}

/**
 * メニュー生成のための walker 継承クラス定義
 * 
 * 通常のメニューの生成にはwalker_nav_menuクラスが使用されていますが、bootstrap4の
 * navbar対応で構造を変更する必要があった箇所のメゾットだけ上書きしています。
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
    //用途が分かれると判断したためフィルターの名前を変更しました。
    $class_names = join( ' ', apply_filters( 'yadoken_nav_menu_submenu_css_class', $classes, $args, $depth ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
    //margin, padding、レスポンシブ対応もbootstrapを利用してここで行っています。
    $output .= '<a class="dropdown-toggle nav-link pl-1 mx-md-1 m-0" data-toggle="dropdown" role="button"><span class="d-md-none pl-md-0 pl-3">展開</span></a>';
    $output .= "{$n}{$indent}<ul{$class_names} role='menu'>{$n}";
  }
}

/**
 * ループの取得内容を変更(固定ページ・管理画面以外)
 * 
 * 固定ページではこれよりも先に投稿が取得されています。
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
  //ニュースのアーカイブページで最新の投稿一つのみが取得されるように設定しています。
  if( $query->is_post_type_archive( 'yadoken_news' ) && ! $query->is_date() && ! $query->is_author() && ! $query->is_tax() ) {
    $query->set( 'posts_per_page', 1 );
  }
  //議事録がページ当たり20投稿分取得されるようにしています。
  if( $query->get( 'post_type' ) === 'yadoken_minutes' ) {
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
    if( $s = wp_unslash( filter_input( INPUT_GET, 's', FILTER_SANITIZE_ENCODED ) ) ) {
      //パーマリンク構造存在判定
      if( get_option( 'permalink_structure' ) ) {
        global $wp_rewrite;
        $get = ( $post_type = wp_unslash( filter_input( INPUT_GET, 'post_type' ) ) ) ? '?post_type=' . $post_type : '';
        wp_safe_redirect( home_url( '/' . $wp_rewrite->search_base . '/' ) . $s . $get );
        exit;
      }
    //検索文字列が空だった場合
    } elseif( ! get_query_var( 's' ) ) {
      /**
       * filter_inputは配列のGETに対してfalseを返します。配列が必要なクエリ変数があった場合は
       * 適宜変更をお願いします。
       */
      if( $post_type = wp_unslash( filter_input( INPUT_GET, 'post_type' ) ) ) {
        $url = get_post_type_archive_link( $post_type );
      //投稿タイプも取得出来なかった場合はホームページにリダイレクト
      } else {
        $url = home_url();
      }
      wp_safe_redirect( $url );
      exit;
    }
  }
}

/**
 * 月別アーカイブリンクで件数表示をaタグ内部に移動
 * 
 * liタグの要素でaタグ外のものがあると表示が崩れるため、変更しました。
 * 
 * @param string $link_html  liタグ、aタグを含むHTMLの文字列
 * @param string $url        リンクのURL(不使用)
 * @param string $text       アーカイブの名前(不使用)
 * @param string $format     リンクのフォーマット(link|option|custom)(不使用)
 * @param string $before     アーカイブの名前の前に表示する文字列(不使用)
 * @param string $after      アーカイブの名前の後に表示する文字列(不使用)
 * @param bool   $selected   当該ページが現在のページだった場合にtrue(不使用)
 * @return string  liタグ、aタグを含むHTMLの文字列
 */
add_filter( 'get_archives_link', 'yadoken_get_archive_link' );
function yadoken_get_archive_link( $html ) {
  $html = preg_replace( '/<\/a>(.+)<\/li>/', '$1</a></li>', $html );
  return $html;
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