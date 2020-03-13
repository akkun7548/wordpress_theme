<?php
/**
 * wordpress設定
 * 
 * 主にwordpressの動作をカスタマイズする関数をまとめたファイルです。
 */

/**
 * テーマでサポートする機能の設定
 * 
 * テーマでサポートする機能を設定している他、メニューの登録も行っています。
 */
add_action( 'after_setup_theme', 'yadoken_setup' );
function yadoken_setup() {
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'wp-block-styles' );
  add_theme_support( 'html5', array( 'caption' ) );
  register_nav_menus( array(
    'header-nav' => __( 'ヘッダーメニュー' ),
    'footer-nav' => __( 'フッターメニュー' ),
    'content' => __( 'ショートコード' )
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
add_filter( 'nav_menu_css_class', 'yadoken_menu_class_filter', 100, 3 );
function yadoken_menu_class_filter( $classes, $item, $args ) {
  if( $args->theme_location === 'header-nav' ) {
    $related = false;
    /**
     * 「表示中のページ」が固定ページ、カスタム投稿タイプアーカイブページ(archive-*.php)の
     * 場合は、「リンク先のページ」にあればcurrent-menu-itemクラスが追加され、なくても
     * 「リンク先のページ」いずれかとの関連を調べる必要はないため、対象から外しています。
     */
    if( ! is_404() && ! is_page() && ( ! is_archive() || is_date() || is_author() || is_category() || is_tag() || is_tax() ) ) {
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
add_filter( 'nav_menu_link_attributes', 'yadoken_menu_link_attributes', 100, 3 );
function yadoken_menu_link_attributes( $atts, $item, $args ) {
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
add_filter( 'nav_menu_item_id', 'yadoken_menu_id_filter', 100 );
function yadoken_menu_id_filter( $menu_id ) {
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
    $output .= '<a class="dropdown-toggle nav-link pl-1 mx-md-1 mx-0 my-0" data-toggle="dropdown" role="button"><span class="d-md-none pl-md-0 pl-3">展開</span></a>';
    $output .= "{$n}{$indent}<ul{$class_names} role='menu'>{$n}";
  }
}

/**
 * 「投稿」を「活動報告」に変更
 * 
 * wordpressデフォルトの投稿タイプであるpostのネームラベルを、「投稿」から「活動報告」に
 * 変更しています。
 */
add_action( 'admin_menu', 'yadoken_change_post_menu_label' );
function yadoken_change_post_menu_label() {
  global $menu;
  global $submenu;
  $name = '活動報告';
  $menu[5][0] = $name;
  $submenu['edit.php'][5][0] = $name . '一覧';
  $submenu['edit.php'][10][0] = '新しい' . $name;
}
add_action( 'init', 'yadoken_change_post_object_label' );
function yadoken_change_post_object_label() {
  global $wp_post_types;
  $name = '活動報告';
  $labels =& $wp_post_types['post']->labels;
  $labels->name = $name;
  $labels->singular_name = $name;
  $labels->add_new = _x('追加', $name);
  $labels->add_new_item = $name . 'の新規追加';
  $labels->edit_item = $name . 'の編集';
  $labels->new_item = '新規' . $name;
  $labels->view_item = $name . 'を表示';
  $labels->search_items = $name . 'を検索';
  $labels->not_found = '記事が見つかりませんでした';
  $labels->not_found_in_trash = 'ゴミ箱に記事は見つかりませんでした';
}

/**
 * カスタム投稿タイプ追加
 * 
 * カスタム投稿タイプを追加しています。
 * ここでは、当該の投稿タイプを編集できる人の権限や、パーマリンクの構造などが設定できます。
 */
add_action('init', 'yadoken_register_post_type');
function yadoken_register_post_type() {
  /**
   * お知らせ
   * 
   * 編集権限は固定ページと同一に設定されています。
   */
  register_post_type(
    'news',
    array(
      'labels' => array(
        'name' => __( 'お知らせ' )
      ),
      'description' => __( 'お知らせ用のカスタム投稿タイプです。' ),
      'public' => true,
      'exclude_from_search' => true,
      'menu_position' => 10,
      'menu_icon' => 'dashicons-megaphone',
      'capability_type' => 'page',
      'map_meta_cap' => true,
      'rewrite' => array(
        'with_front' => false
      ),
      'show_in_rest' => true,
      'has_archive' => true,
    )
  );
  /**
   * 議事録
   * 
   * 非ログインユーザーに対して、投稿タイプアーカイブはyadoken_template_redirect()で404エラーに、
   * その他のアーカイブはyadoken_change_main_loop()でクエリをpostに書き換えています。
   * また、議事録はyadoken_deny_publish_minutes()によって状態がpublishになることはありません。
   */
  register_post_type(
    'minutes',
    array(
      'labels' => array(
        'name' => __( '議事録' )
      ),
      'description' => __( '内部向け議事録のカスタム投稿タイプです。' ),
      'public' => true,
      'exclude_from_search' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-media-text',
      'capability_type' => 'post',
      'map_meta_cap' => true,
      'rewrite' => array(
        'with_front' => false
      ),
      'show_in_rest' => true,
      'has_archive' => true,
    )
  );
}

/**
 * 活動報告のリライトルールの先頭に投稿ページのスラッグが付くようにする。
 * 
 * 通常、パーマリンク構造や投稿ページのスラッグを変更した場合は、設定 > パーマリンク設定 > 変更を保存
 * を押下しないと反映されません。この操作をした時に$wp_rewrite->flush_rules( false )が実行される
 * のですが、これが実行される時にしか実行されないフィルターということです。
 * 投稿ページ、もしくはそのスラッグが変更された場合は、yadoken_page_for_posts()もしくは
 * yadoken_save_post_page()でこれを実行して更新されるようにしています。
 * 
 * @param array $rewrite  リライトルール
 * @return array  新しいリライトルール
 */
add_filter( 'post_rewrite_rules', 'yadoken_change_rewrite_rules' );
add_filter( 'category_rewrite_rules', 'yadoken_change_rewrite_rules' );
add_filter( 'post_tag_rewrite_rules', 'yadoken_change_rewrite_rules' );
function yadoken_change_rewrite_rules( $rewrite ) {
  if( $page_for_posts = get_post( get_option( 'page_for_posts' ) ) ) {
    global $wp_rewrite;
    $new_rewrite = array();
    foreach( $rewrite as $key => $value ) {
      $new_key = substr_replace( $key, $page_for_posts->post_name . '/', strlen( $wp_rewrite->front ) - 1, 0 );
      $new_rewrite[$new_key] = $value;
    }
    $rewrite = $new_rewrite;
  }
  return $rewrite;
}

/**
 * 投稿ページを変更した時にパーマリンクを更新する。
 * 
 * @param mixed $old_value  更新前の値(不使用)
 * @param mixed $value      更新後の値(不使用)
 * @param string $option    オプション名(不使用)
 */
add_action( 'update_option_page_for_posts', 'yadoken_page_for_posts', 20 );
function yadoken_page_for_posts() {
  global $wp_rewrite;
  $wp_rewrite->flush_rules( false );
}

/**
 * 投稿ページのスラッグを更新した時にパーマリンクを更新する。
 * 
 * @param int $post_ID   投稿ID
 * @param WP_Post $post  投稿オブジェクト(不使用)
 * @param bool $update   更新された投稿かどうか(不使用)
 */
add_action( 'save_post_page', 'yadoken_save_post_page', 20 );
function yadoken_save_post_page( $post_ID ) {
  if( $post_ID === (int) get_option( 'page_for_posts' ) ) {
    global $wp_rewrite;
    $wp_rewrite->flush_rules( false );
  }
}

/**
 * リライトルールの変更に個別ページへのリンクを対応させる。
 * 
 * 個別ページのリンクが取得される度に呼び出されるため、重くなる原因になるかもしれません。
 * 
 * @param string $link  取得したリンク構造
 * 
 * @param WP_Post $post    投稿オブジェクト(不使用)
 * @param bool $leavename  投稿名を残すかどうか(不使用)
 * 
 * @param WP_Term $term  タームオブジェクト(不使用)
 * 
 * @return string  新しいリンク
 */
add_filter( 'pre_post_link', 'yadoken_pre_link' );
add_filter( 'pre_term_link', 'yadoken_pre_link' );
function yadoken_pre_link( $link ) {
  $base = '';
  if( $page_for_posts = get_post( get_option( 'page_for_posts' ) ) ) {
    $base = '/' . $page_for_posts->post_name;
  }
  return $base . $link;
}

/**
 * ループの取得内容を変更(固定ページ・管理画面以外)
 * 
 * 固定ページではこれよりも先に投稿が取得されています。
 * 
 * @param WP_Query $this
 */
add_action( 'pre_get_posts', 'yadoken_change_main_loop' );
function yadoken_change_main_loop( $query ) {
  //メインループの場合
  if( $query->is_main_query() ) {
    if( is_admin() || $query->is_page() ) {
      return;
    }
    /**
     * ニュースのアーカイブページで最新の投稿一つのみが取得されるように設定しています。
     * タクソノミーを追加する場合はここを変更しないとアーカイブページが1記事しか取得できません。
     */
    if( $query->is_post_type_archive( 'news' ) && ! $query->is_date() && ! $query->is_author() ) {
      $query->set( 'posts_per_page', 1 );
    }
    /**
     * ログインしていない状態で議事録アーカイブにアクセスした場合、活動報告が取得されるように
     * 設定しています。
     */
    $post_type = $query->get( 'post_type' );
    if( ! is_user_logged_in() ) {
      /**
       * post_typeをを変更してしまうとyadoken_template_redirect()で区別出来なくなってしまうため、
       * 404エラーにしたい全記事用のアーカイブページ( home_url( '/minutes' ) )を対象外に
       * しています。
       */
      if( $post_type === 'minutes' && ( $query->is_search() || ! $query->is_archive() || $query->is_date() || $query->is_author() || $query->is_category() || $query->is_tag() || $query->is_tax() ) ) {
        $query->set( 'post_type', 'post' );
        $post_type = 'post';
      } elseif( is_array( $post_type ) ) {
        foreach( $post_type as $key => $type ) {
          if( $type === 'minutes' ) {
            unset( $post_type[$key] );
          }
        }
        $query->set( 'post_type', $post_type );
      }
    }
    //議事録がページ当たり20投稿分取得されるようにしています。
    if( $post_type === 'minutes' ) {
      $query->set( 'posts_per_page', 20 );
    }
  //サブループの場合
  } else {
    //ログインしている場合は議事録の非公開の記事を取得するようにしています。
    if( is_user_logged_in() && $query->get( 'post_type' ) === 'minutes' ) {
      $query->set( 'post_status', 'private' );
    }
  }
}

/**
 * ログインしている時、議事録のアーカイブで非公開記事を取得する。
 * 
 * @param string $sql_where   SQLクエリ
 * @param array $parsed_args  デフォルト引数
 */
if( is_user_logged_in() ) {
  add_filter( 'getarchives_where', 'yadoken_getarchives_where' );
}
function yadoken_getarchives_where( $sql_where ) {
  return str_replace( 'publish', 'private', $sql_where );
}

/**
 * テンプレートファイルが選択される直前に$wp_queryの中身を変更して動作を変える。
 */
add_action( 'template_redirect', 'yadoken_template_redirect' );
function yadoken_template_redirect() {
  /**
   * 議事録の個別ページは全て非公開になるようにされているため、ここでは議事録のアーカイブページ
   * に非ログインユーザーからアクセスがあった場合に404エラーを返すようにしています。
   */
  if( ! is_user_logged_in() && is_post_type_archive( 'minutes' ) ) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
  }
  /**
   * WP_Rewriteではsearch_baseが設定されているにも関わらず、検索結果ページはデフォルトで
   * GETの羅列になっているため、リダイレクトしています。
   * また、post_typeのGETの値だけは残すようにしています。
   */
  if( is_search() && $s = filter_input( INPUT_GET, 's', FILTER_SANITIZE_ENCODED ) ) {
    global $wp_rewrite;
    $get = ( $post_type = filter_input( INPUT_GET, 'post_type' ) ) ? '?post_type=' . $post_type : '';
    wp_safe_redirect( home_url( '/' . $wp_rewrite->search_base . '/' ) . $s . $get );
    exit;
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
add_filter( 'get_archives_link', 'yadoken_archive_link' );
function yadoken_archive_link( $html ) {
  $html = preg_replace( '/<\/a>(.+)<\/li>/', '$1</a></li>', $html );
  return $html;
}

?>