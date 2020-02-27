<?php
/**
 * ショートコード
 * 
 * ショートコードの定義をまとめました。
 */

/**
 * クエリ生成
 * 
 * GETによる条件の指定でクエリ変数が上書きされるため、その値を用いてソートするかを
 * 引数で決めることができます。
 * 
 * 複数のソート対象を判別する機能は未実装のため、ソート有効のサブループ全てがソート対象になります。
 * 
 * @param array $atts ショートコードの引数
 */
add_shortcode( 'query', 'yadoken_query_shortcode' );
function yadoken_query_shortcode( $atts ) {
  global $yadoken_query;
  $args = array(
    'posts_per_page' => get_option( 'posts_per_page' ),
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'paged' => get_query_var( 'paged', 1 ),
  );
  //sortmenuによるソートをするかについての設定
  $add = array( 'sort' => false );
  $atts = shortcode_atts(
    //shortcode_atts()の引数に一つの配列として渡すため結合しています。
    array_merge( $args, $add ),
    $atts,
    'query'
  );
  //ソート対象のパラメーター
  if( $atts['sort'] ) {
    $atts['orderby'] = get_query_var( 'orderby', 'date' );
    $atts['order'] = get_query_var( 'order', 'DESC' );
  }
  //未ログインのアクセスに対して公開記事以外がクエリされるのを防止しています。
  if( $atts['post_status'] !== 'publish' && ! is_user_logged_in() ) {
    $atts['post_status'] = 'publish';
  }
  //WP_Queryに対して有効なクエリ変数に限定するために配列を分けています。
  $input = array_diff( $atts, $add );
  $yadoken_query = new WP_Query( $input );
}

/**
 * ループ出力
 * 
 * クエリのショートコードの下に記述することで、生成したクエリを元にサブループを作り各投稿を
 * リスト状に出力できます。
 * 
 * 'links'を削除しました。
 * 
 * @param array $atts ショートコードの引数
 * @return string  出力するHTML
 */
add_shortcode( 'loop', 'yadoken_loop_shortcode' );
function yadoken_loop_shortcode( $atts ) {
  global $yadoken_query;
  if( empty( $yadoken_query ) ) {
    return;
  }
  //出力するHTML
  $str = '';
  $atts = shortcode_atts(
    //取得した記事の表示方法
    array( '' ),
    $atts,
    'loop'
  );
  if( $yadoken_query->have_posts() ) {
    //バッファリングに出力しています。
    ob_start();
    while( $yadoken_query->have_posts() ) {
      $yadoken_query->the_post();
      switch( $atts[0] ) {
        case 'summary':
          get_template_part( 'template-parts/summary' );
          break;
        case 'title':
          get_template_part( 'template-parts/title' );
          break;
        case 'list':
          get_template_part( 'template-parts/list' );
          break;  
        case 'news': ?>
          <dt><?php the_time( 'Y/m/d' ); ?></dt><dd><a href="<?php the_permalink(); ?>"><?php the_title(); ?>を掲載しました。</a></dd> <?php
          break;
        default:
          yadoken_display_post();
      }
    }
    //バッファリングへの出力をまとめて変数に格納しています。
    $str = ob_get_clean();
    wp_reset_postdata(); 
  } else {
    //投稿タイプネームラベルを取得しています。
    $name = ( $obj = get_post_type_object( $yadoken_query->get( 'post_type' ) ) ) ? $obj->labels->name : '記事';
    //表示形式毎に、記事がなかった場合の出力を設定しています。
    switch( $atts[0] ) {
      case 'summary':
      case 'title':
      case 'list':
        $str = '<p>' . $name . 'はありません。</p>';
        break;
      default:
        $str = '';
    }
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
  /**
   * 個別ページの場合、ページネーションはサブループに用いられるものに限定されるため、
   * ショートコードqueryで生成した$yadoken_queryが存在しない場合はreturnしています。
   */
  if( is_singular() ) {
    global $yadoken_query;
    if( empty( $yadoken_query ) ) {
      return;
    } else {
      $query = $yadoken_query;
      $base = get_permalink();
    }
  /**
   * 個別ページ以外の全ページ(アーカイブページや検索結果ページなど)、メインループが複数記事を
   * クエリする場合はwp_queryからページネーションを生成しています。
   */
  } else {
    global $wp_query;
    $query = $wp_query;
    $base = get_pagenum_link( 1 );
  }
  $str = '';
  /**
   * ページ数が2以上になる時に出力するようにしています。
   * また、ページネーションを作成するために使用されるベースのURLは、個別ページの場合は
   * get_permalink()、その他はget_pagenum_link( 1 )で取得しています。
   */
  if ( $query->max_num_pages > 1 ) {
    $str .= '<div class="row justify-content-center pagination stripe">' . "\n";
    $str .= paginate_links( array(
      'base' => $base . '%_%',
      'format' => '/page/%#%',
      'current' => max( 1, get_query_var( 'paged', 1 ) ),
      'total' => $query->max_num_pages,
    ));
    $str .= '</div>' . "\n";
  }
  return $str;
}

/**
 * 検索フォーム出力
 * 
 * 検索フォームを出力します。
 * この時、検索フォームで使用したい変数を引数として与えることができます。
 * ショートコードは値をreturnする必要があるのですが、get_search_form()ではその場で出力して
 * しまうため、バッファリングに出力させて得られた値を返しています。
 * 
 * @param array $atts ショートコードの引数
 * @return string  出力するHTML
 */
add_shortcode( 'searchform', 'yadoken_searchform_shortcode' );
function yadoken_searchform_shortcode( $atts ) {
  global $yadoken_searchform;
  $atts = shortcode_atts(
    array(
      'post_type' => '',
    ),
    $atts,
    'searchform'
  );
  $yadoken_searchform = $atts;
  ob_start();
  get_search_form();
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
 * サイト内リンク出力
 * 
 * 単純なhome_url()のラッパーです。
 * 
 * @param array $atts ショートコードの引数
 * @return string  得られたURL
 */
add_shortcode( 'url', 'yadoken_url_shortcode' );
function yadoken_url_shortcode( $atts ) {
  $atts = shortcode_atts(
    array( '' ),
    $atts,
    'url'
  );
  return esc_url( home_url( $atts[0] ) );
}

/**
 * 記事内メニュー出力
 * 
 * メニューを出力するためのショートコードです。
 * 外観 > メニュー からメニューの位置として選択できます。
 * 但し、ショートコードとしていくつ設定しても単一のメニューしか表示できません。
 * 
 * @return string  出力するHTML
 */
add_shortcode( 'menu', 'yadoken_menu_shortcode' );
function yadoken_menu_shortcode() {
  $args = array(
    'theme_location' => 'content',
    'container' => '',
    'items_wrap' => '%3$s',
    'echo' => false
  );
  return wp_nav_menu( $args );
}

/**
 * functions.phpから変数を引き渡す
 * 
 * このファイルからショートコード実行箇所に変数を送ることができます。
 * 事前にエスケープなどの処理を済ませておいた変数を入れてください。
 * 
 * @param array $atts ショートコードの引数
 * @return string  出力する値(事前にエスケープされたもの)
 */
add_shortcode( 'pageinfo', 'yadoken_pageinfo_shortcode' );
function yadoken_pageinfo_shortcode( $atts ) {
  $atts = shortcode_atts(
    array( 'enc_url' ),
    $atts,
    'pageinfo'
  );
  global $yadoken_pageinfo;
  return $yadoken_pageinfo[$atts[0]];
}

?>