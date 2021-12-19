<?php
/**
 * ショートコード
 * 
 * ショートコードの定義をまとめました。
 */

 
/**
 * クエリ生成
 * 
 * このショートコードは記事を取得するだけで、何も出力しません。
 * このショートコードの下に取得した記事の情報を利用するショートコードを記述することで
 * 使用します。
 * 
 * GETによる条件の指定でクエリ変数が上書きされるため、その値を用いてソートするかを
 * 引数で決めることができます。
 * 
 * 複数のソート対象を判別する機能は未実装のため、ソート有効のサブループ全てがソート対象になります。
 * 
 * 投稿ステータスの指定を廃止しました。
 * 
 * @param array $atts {
 *   ショートコードの引数
 *   @type string|int $posts_per_page  １ページ当たりの表示記事数
 *   @type string     $orderby         何を使って並べるか
 *   @type string     $order           降順/昇順
 *   @type string     $post_type       投稿タイプ
 *   @type string|int $paged           現在のページ
 *   @type bool       $sort            ソート有効/無効
 * }
 */
add_shortcode( 'query', 'yadoken_query_shortcode' );
function yadoken_query_shortcode( $atts ) {
  global $yadoken_query;
  $args = array(
    'posts_per_page' => get_option( 'posts_per_page' ),
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => 'post',
    'paged' => get_query_var( 'paged', 1 ),
    /**sortmenuによるソートをするかについての設定を追加 */
    'sort' => false
  );
  extract( shortcode_atts( $args, $atts, 'query' ) );
  /**ソート対象のパラメーター */
  if( $sort ) {
    $orderby = get_query_var( 'orderby', 'date' );
    $order = get_query_var( 'order', 'DESC' );
  }
  /**WP_Queryに対して有効なクエリ変数に限定して配列化しています。 */
  $input = compact( 'posts_per_page', 'orderby', 'order', 'post_type', 'paged' );
  $yadoken_query = new WP_Query( $input );
}

/**
 * 検索フォーム出力
 * 
 * 検索フォームを出力します。
 * ショートコードは値をreturnする必要があるのですが、get_search_form()ではその場で出力して
 * しまうため、バッファリングに出力させて得られた値を返しています。
 * 
 * 引数を削除しました。
 * 
 * @return string  出力するHTML
 */
add_shortcode( 'searchform', 'yadoken_searchform_shortcode' );
function yadoken_searchform_shortcode() {
  ob_start();
  get_search_form();
  return ob_get_clean();
}

/**
 * 取得した記事に関する情報を表示
 * 
 * @return string  出力するHTML
 */
add_shortcode( 'count', 'yadoken_count_shortcode' );
function yadoken_count_shortcode() {
  ob_start();
  get_template_part( 'template-parts/count' );
  return ob_get_clean();
}

/**
 * ソートメニュー出力
 * 
 * ソートを有効にしているサブループの投稿を並べ替えるフォームを出力します。
 * ショートコードは値をreturnする必要があるのですが、get_template_part()ではその場で出力して
 * しまうため、バッファリングに出力させて得られた値を返しています。
 * 
 * @return string  出力するHTML
 */
add_shortcode( 'sortmenu', 'yadoken_sortmenu_shortcode' );
function yadoken_sortmenu_shortcode() {
  ob_start();
  get_template_part( 'template-parts/sortmenu' );
  return ob_get_clean();
}

/**
 * ループ出力
 * 
 * クエリのショートコードの下に記述することで、生成したクエリを元にサブループを作り各投稿を
 * リスト状に出力できます。
 * 
 * 'links'を削除しました。
 * 
 * @param array $atts {
 *   ショートコードの引数
 *   @type string $format  出力形式
 * }
 * @return string  出力するHTML
 */
add_shortcode( 'loop', 'yadoken_loop_shortcode' );
function yadoken_loop_shortcode( $atts ) {
  global $yadoken_query;
  /**表示する情報が存在しない場合 */
  if( empty( $yadoken_query ) ) {
    return;
  }
  //出力するHTML
  $str = '';
  /**初期値では、yadoken_display_post()により出力するようにしています。 */
  extract( shortcode_atts( array( 'format' => '' ), $atts, 'loop' ) );
  if( $yadoken_query->have_posts() ) {
    /**それぞれ前後に挿入する文字列 */
    $before = '';
    $after  = '';
    switch( $format ) {
      case 'news':
        $before = '<dl class="update_info">' . "\n";
        $after  = '</dl>' . "\n";
        function yadoken_news() { ?>
          <dt><?php the_time( 'Y/m/d' ); ?></dt>
          <dd><a href="<?php the_permalink(); ?>"><?php the_title(); ?>を公開しました。</a></dd> <?php
        }
        $func = 'yadoken_news';
        break;
      default:
        $func = 'yadoken_display_post';
    }
    //バッファリングに出力しています。
    ob_start();
    while( $yadoken_query->have_posts() ) {
      $yadoken_query->the_post();
      /**可変関数 */
      $func();
    }
    //バッファリングへの出力をまとめて変数に格納しています。
    $str = $before . ob_get_clean() . $after;
    wp_reset_postdata();
  /**記事がなかった場合 */
  } else {
    //投稿タイプネームラベルを取得しています。
    $name = yadoken_post_type_name( $yadoken_query->get( 'post_type' ) );
    $str = '<p>' . $name . 'はありません。</p>';
  }
  return $str;
}

/**
 * ページネーション
 * 
 * クエリのショートコードの下に記述することで、投稿の数がクエリの１ページ当たりの表示数を
 * 超過した場合にページネーションを出力します。
 * 
 * @return string  出力するHTML
 */
add_shortcode( 'pagination', 'yadoken_pagination_shortcode' );
function yadoken_pagination_shortcode() {
  /**paginate_links()に渡す引数 */
  $args = array();
  /**
   * 個別ページの場合、ページネーションはサブループに用いられるものに限定されるため、
   * ショートコードqueryで生成した$yadoken_queryが存在しない場合はreturnしています。
   */
  if( is_singular() ) {
    global $yadoken_query;
    if( empty( $yadoken_query ) ) {
      return;
    }
    $query = $yadoken_query;
    $args = array( 'total' => $query->max_num_pages );
  /**
   * 個別ページ以外の全ページ(アーカイブページや検索結果ページなど)、メインループが複数記事を
   * クエリする場合はwp_queryからページネーションを生成しています。
   */
  } else {
    global $wp_query;
    $query = $wp_query;
  }
  $str = '';
  /**
   * ページ数が2以上になる時に出力するようにしています。
   * また、ページネーションを作成するために使用されるベースのURLは、個別ページの場合は
   * get_permalink()、その他はget_pagenum_link()で取得しています。
   */
  if ( $query->max_num_pages > 1 ) {
    $str .= '<div class="row justify-content-center pagination stripe">' . "\n";
    $str .= paginate_links( $args ) . "\n";
    $str .= '</div>' . "\n";
  }
  return $str;
}

/**
 * サイト内リンク出力
 * 
 * 単純なhome_url()のラッパーです。
 * 
 * @param array $atts {
 *   ショートコードの引数
 *   @type string $path  リンクのパス
 * }
 * @return string  得られたURL
 */
add_shortcode( 'url', 'yadoken_url_shortcode' );
function yadoken_url_shortcode( $atts ) {
  extract( shortcode_atts( array( 'path' => '' ), $atts, 'url' ) );
  return esc_url( home_url( $path ) );
}

/**
 * 記事内メニュー出力
 * 
 * メニューを出力するためのショートコードです。
 * 外観 > メニュー > メニュー名 をショートコードの引数として指定することで、該当するメニュー
 * を表示することができます。
 * 
 * @param array $atts {
 *   ショートコードの引数
 *   @type string $menu  メニューの名前
 * }
 * @return string  出力するHTML
 */
add_shortcode( 'menu', 'yadoken_menu_shortcode' );
function yadoken_menu_shortcode( $atts ) {
  extract( shortcode_atts( array( 'menu' => '' ), $atts, 'menu' ) );
  $args = array(
    'menu' => $menu,
    'container' => '',
    'fallback_cb' => false,
    'echo' => false,
    'theme_location' => '__no_such_location',
    'items_wrap' => '%3$s'
  );
  return wp_nav_menu( $args );
}

/**
 * metadata.phpから変数を引き渡す
 * 
 * metadata.phpで取得した当該ページの情報をショートコードから出力することができます。
 * 事前にエスケープなどの処理を済ませておいた変数を入れてください。
 * 
 * @param array $atts {
 *   ショートコードの引数
 *   @type string $key  ページ情報の配列のキー
 * }
 * @return string  出力する値(事前にエスケープされたもの)/存在しない場合は空配列
 */
add_shortcode( 'pageinfo', 'yadoken_pageinfo_shortcode' );
function yadoken_pageinfo_shortcode( $atts ) {
  /**参照：inc/metadata.php yadoken_head() */
  global $yadoken_pageinfo;
  extract( shortcode_atts( array( 'key' => '' ), $atts, 'pageinfo' ) );
  if( isset( $yadoken_pageinfo[$key] ) ) {
    return $yadoken_pageinfo[$key];
  } else {
    return '';
  }
}

?>