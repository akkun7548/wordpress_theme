<?php
/**
 * HTMLのメタ情報を出力
 * 
 * head内/footer下部への出力を変更する関数です。
 */

/**
 * wordpressを使用していることやそのバージョンを伝えるタグの削除
 * 
 * 不要であったため、以下のようなタグを削除しました。
 * <meta name="generator" content="WordPress 5.3.2" />
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * metaタグなどの出力
 * 
 * twitterやfacebook、その他SNS等で共有された場合にそこで表示される内容は、この関数で
 * 設定した内容になります。
 * また、SEO対策は基本的にこちらで行います。
 * これらのタグは実際にアクセスした人には見えませんが、google検索エンジンのクローラー
 * などbotでアクセスした時には見えるので、検索ワードとの関連性を示して検索結果で上位に
 * 表示してもらえるように適切に設定しましょう。
 * 
 * get_pagenum_link()は内部で使っているremove_query_argが$_SERVER['REQUEST_URI']を
 * 使用しているため、どのページでもイイ感じに当該ページ通りのリンクを取得してくれます。
 * できるだけwordpressの関数を使用したいため$_SERVER['REQUEST_URI']は直接使用していませんが、
 * この変数はリクエストそのもののため、基本的にはそのページのURLです。(書き換えられる可能性アリ)
 * また、個別ページはページネーションを設定しているものでも先頭から読んでもらいたいものであると
 * 判断したため、ページネーションは反映されません。
 */
add_action( 'wp_head', 'yadoken_head' );
function yadoken_head() {
  //現在のページのurl
  $ogp_url = '';
  //現在のページのタイトル(ブラウザのタブなどに表示されます。)
  $ogp_title = '';
  //現在のページの抜粋文
  $ogp_descr = '';
  //共有先で表示される画像
  $ogp_img = '';
  //ページタイプ。トップページだけがwebsite、その他がarticle(検索エンジン向け)
  $ogp_type = '';
  //ツイッターID
  $twitter_account = '';
  //出力するHTML
  $str = '';
  //サイトの名前
  $blog_name = get_bloginfo( 'name' );
  /**
   * og:title, og:descr, og:urlを設定しています。
   * 全てのページに設定しているつもりですが、出力がおかしかったりなかったりする場合は
   * ここを見直した方がいいかもしれません。
   */
  //フロントページ(ホームページ)の場合
  if( is_front_page() ) {
    $ogp_url = home_url();
    $ogp_title = $blog_name;
    $ogp_descr = get_bloginfo( 'description' );
  //個別ページの場合
  } elseif( is_singular() ) {
    $ogp_url = get_permalink();
    $title = get_the_title();
    //投稿、その他でタイトルの表示方法を変更しています。
    if( is_singular( 'post' ) ) {
      $ogp_title = $title;
    } else {
      $ogp_title = sprintf( "%s | %s", $title, $blog_name );
    }
    $ogp_descr = get_the_excerpt();
  /**
   * 投稿タイプアーカイブページの場合(yadoken_news以外)
   * 
   * それぞれの投稿タイプの全記事が表示されるページのみを設定しています。
   */
  } elseif( is_home() || yadoken_is_post_type_archive( 'yadoken_minutes' ) ) {
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ), false );
    $post_type = yadoken_post_type();
    /**
     * is_home()が有効なページは原則として 設定 > 表示設定 > ホームページの表示 > 投稿ページ
     * で指定されている固定ページのため、WP_Postオブジェクトがクエリされています。
     */
    if( ( $obj = get_queried_object() ) instanceof WP_Post ) {
      $title = $obj->post_title;
    } else {
      $title = yadoken_post_type_name( $post_type, ' ' );
    }
    $ogp_title = sprintf( "%s | %s", $title, $blog_name );
    $ogp_descr = sprintf( "%sの一覧ページです。", yadoken_post_type_name( $post_type, '、' ) );
  /**
   * archive-yadoken_news.phpで１記事のみをクエリして、個別ページと同じように見せています。
   * このため、投稿が存在しない場合のデフォルト値はこちらで設定しています。
   */
  } elseif( yadoken_is_post_type_archive( 'yadoken_news' ) ) {
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ), false );
    if( $post = get_post() ) {
      $title = $post->post_title;
      $ogp_descr = $post->post_excerpt;
    } else {
      $title = yadoken_post_type_name( 'yadoken_news' );
      $ogp_descr = 'やどけんからのお知らせのページです。';
    }
    $ogp_title = sprintf( "%s | %s", $title, $blog_name );
  /**
   * アーカイブ、検索結果ページの場合
   * 
   * 表示される記事が条件で絞られるページを設定しています。
   */
  } elseif( is_archive() || is_search() ) {
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ), false );
    if( is_search() ) {
      $s = get_search_query();
      $title = '検索結果：' . $s;
      $part = 'キーワード：' . $s . ' で検索した';
    } elseif( is_category() && $title = single_cat_title( '', false ) ) {
      $part = $title . 'のカテゴリーの';
    } elseif( is_tag() && $title = single_tag_title( '', false ) ) {
      $part = $title . 'のタグが付いた';
    /**
     * カスタムタクソノミーは作成していないため未検証のコードになります。
     * カスタムタクソノミーを作成した際は、一度正しく出力されているか確認してください。
     */
    } elseif( is_tax() && $title = single_term_title( '', false ) ) {
      $part = 'ターム:' . $title . 'の';
    /**
     * get_queried_object()で取得したオブジェクトがWP_Userのインスタンスでない場合は
     * こちらに分岐しないようにしてあります。
     */
    } elseif( is_author() && $obj = get_userdata( get_query_var( 'author', false ) ) ) {
      $title = $obj->data->display_name;
      $part = $title . 'さんの';
    } elseif( is_day() && $title = get_the_time( 'Y年n月j日' ) ) {
      $part = $title . 'の';
    } elseif( is_month() && $title = get_the_time( 'Y年n月' ) ) {
      $part = $title . 'の';
    } elseif( is_year() && $title = get_the_time( 'Y年' ) ) {
      $part = $title . 'の';
    } else {
      $title = 'アーカイブ';
      $part = '';
    }
    $post_type = yadoken_post_type();
    $ogp_title = sprintf( "%s | %s", $title, yadoken_post_type_name( $post_type, ' ' ) );
    $ogp_descr = sprintf( "%s%s一覧ページです。", $part, yadoken_post_type_name( $post_type, '、' ) );
  //404エラーページの場合
  } elseif( is_404() ) {
    $ogp_url = home_url();
    $ogp_title = sprintf( "404 not found | %s", $blog_name );
    $ogp_descr = 'お探しのページは見つかりませんでした。';
  //その他の場合
  } else {
    $ogp_url = home_url();
    $ogp_title = $blog_name;
    $ogp_descr = get_bloginfo( 'description' );
  }
  
  // og:typeの設定
  $ogp_type = is_front_page() ? 'website' : 'article';
  /**
   * og:imageの設定
   * postではサムネイルか記事内先頭の画像、その他ではサイトアイコンにしています。
   */
  if ( is_singular( 'post' ) ) {
    if( has_post_thumbnail() ) {
      $ogp_img = get_the_post_thumbnail_url();
    } else {
      $ogp_img = yadoken_first_image();
    }
  } else {
   $ogp_img = get_site_icon_url();
  }
  // ツイッターアカウントID
  $twitter_account = 'yadoken_tsukuba';
  /**
   * 他のファイルで使う変数
   * サイドバー、カスタムHTML内のショートコードで使用しています。
   */
  global $yadoken_pageinfo;
  $yadoken_pageinfo['enc_url'] = urlencode( esc_url( $ogp_url ) );
  $yadoken_pageinfo['enc_title'] = urlencode( esc_attr( $ogp_title ) );
  $yadoken_pageinfo['esc_twitter'] = esc_attr( $twitter_account );
  // metaタグ、title
  $str .= '<title>' . esc_attr( $ogp_title ) . '</title>' . "\n";
  $str .= '<meta name="description" content="' . esc_attr( $ogp_descr ) . '">' . "\n";
  $str .= '<meta name="thumbnail" content="' . esc_url( $ogp_img ) . '">' . "\n";
  // OGP
  $str .= '<meta property="og:title" content="' . esc_attr( $ogp_title ) . '">' . "\n";
  $str .= '<meta property="og:description" content="' . esc_attr( $ogp_descr ) . '">' . "\n";
  $str .= '<meta property="og:type" content="' . $ogp_type . '">' . "\n";
  $str .= '<meta property="og:url" content="' . esc_url( $ogp_url ) . '">' . "\n";
  $str .= '<meta property="og:image" content="' . esc_url( $ogp_img ) . '">' . "\n";
  $str .= '<meta property="og:site_name" content="' . esc_attr( $blog_name ) . '">' . "\n";
  $str .= '<meta property="og:locale" content="ja_JP">' . "\n";
  // twitter
  $str .= '<meta name="twitter:card" content="summary">' . "\n";
  $str .= '<meta name="twitter:site" content="@' . esc_attr( $twitter_account ) . '">' . "\n";
  echo $str;
}

/**
 * stylesheet, javascriptファイルの読み込み
 * 
 * style.cssを更新した際にはwp_enqueue_style()のversionを上げておくとブラウザのキャッシュが
 * 更新されます。
 */
add_action( 'wp_enqueue_scripts', 'yadoken_enqueue_scripts' );
function yadoken_enqueue_scripts() {
  $version = wp_get_theme()->get( 'Version' );
  wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' );
  wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css' );
  wp_enqueue_style( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css' );
  wp_enqueue_style( 'slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css', array(), $version );
  wp_enqueue_script( 'popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array( 'jquery' ), false, true );
  wp_enqueue_script( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), false, true );
  wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array( 'jquery', 'popper' ), false, true );
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery', 'slick' ), $version, true );
}

/**
 * bootstrapのcssをcdn化する対応
 * 
 * integrityを挿入するために変更しました。
 * 
 * @param string $html    出力するリンクタグ全体
 * @param string $handle  登録されたハンドル名
 * @param string $href    スタイルシートのソースurl
 * @param string $media   タグのメディア属性値
 * @return string  出力するリンクタグ全体
 */
add_filter( 'style_loader_tag', 'yadoken_add_attributes_to_styles', 10, 4 );
function yadoken_add_attributes_to_styles( $html, $handle, $href, $media ) {
  if( $handle === 'bootstrap' ) {
    $type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
    $html = sprintf(
      "<link rel='stylesheet' id='%s-css' href='%s'%s media='%s' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous' />\n",
      $handle,
      $href,
      $type_attr,
      $media
    );
  }
  return $html;
}

/**
 * bootstrapのjs,popperをcdn化する対応
 * 
 * integrityを挿入するために変更しました。
 * 
 * @param string $tag     scriptタグ
 * @param string $handle  登録されたハンドル名
 * @param string $src     スクリプトのソースurl
 * @return string  scriptタグ
 */
add_filter( 'script_loader_tag', 'yadoken_add_attributes_to_scripts', 10, 3 );
function yadoken_add_attributes_to_scripts( $tag, $handle, $src ) {
  switch( $handle ) {
    case 'popper':
      $type_attr = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
      $tag = sprintf( "<script%s src='%s' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>\n", $type_attr, $src );
      break;
    case 'bootstrap':
      $type_attr = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
      $tag = sprintf( "<script%s src='%s' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>\n", $type_attr, $src );
      break;
  }
  return $tag;
}

?>