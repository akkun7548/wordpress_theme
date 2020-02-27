<?php
/**
 * head内のタグを制御
 * 
 * head/footer下部への出力を変更する関数です。
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
 * get_pagenum_link()は内部で使っているremove_query_argが$_SERVER['REQUEST_URI']を
 * 使用しているため、どのページでもイイ感じに当該ページ通りのリンクを取得してくれます。
 * できるだけwordpressの関数を使用したいため$_SERVER['REQUEST_URI']は使用していませんが、
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
  //現在のページの抜粋文(先頭から10字程度です。)
  $ogp_descr = '';
  //共有先で表示される画像
  $ogp_img = '';
  //トップページだけがwebsite、その他がarticle(検索エンジン向け)
  $ogp_type = '';
  //ツイッターID
  $twitter_account = '';
  //出力するHTML
  $str = '';
  //サイトの名前
  $blog_name = get_bloginfo( 'name' );
  /**
   * og:title, og:descr, og:urlを設定しています。
   * 全てのページに設定しているつもりですが、出力がおかしかったりなかったりした場合は
   * ここを見直した方がいいかもしれません。
   */
  //フロントページの場合
  if( is_front_page() ) {
    $ogp_url = home_url();
    $ogp_title = $blog_name;
    $ogp_descr = get_bloginfo( 'description' );
  /**
   * 投稿ページ(postの一覧)の場合
   * 基本的にはカスタム投稿タイプアーカイブの部分と同じように扱っています。
   * 参照：以下のis_post_type_archive( *** )が使用されている部分
   */
  } elseif( is_home() ) {
    $name = yadoken_post_type_name( 'post', false );
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ), false );
    $ogp_title = $name . ' | ' . $blog_name;
    $ogp_descr = $name . 'の一覧ページです。';
  //個別ページの場合
  } elseif( is_singular() ) {
    $ogp_url = get_permalink();
    $ogp_descr = get_the_excerpt();
    //投稿タイプごとにサイト名を入れるかを分けています。
    switch( get_post_type() ) {
      case 'page':
      case 'news':
      case 'minutes':
        $ogp_title = get_the_title() . ' | ' . $blog_name;
        break;
      case 'post':
        $ogp_title = get_the_title();
        break;
      default:
        $ogp_title = get_the_title();
    }
  } elseif( is_archive() ) {
    if( $obj = get_post_type_object( get_post_type() ) ) {
      $name = $obj->labels->name;
    } else {
      $name = 'アーカイブ';
    }
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ), false );
    if( is_category() ) {
      $cat_title = single_cat_title( '', false );
      $ogp_title = $cat_title . ' | ' . $blog_name;
      $ogp_descr = $cat_title . 'のカテゴリーの' . $name . '一覧です';
    } elseif( is_tag() ) {
      $tag_title = single_tag_title( '', false );
      $ogp_title = $tag_title . ' | ' . $blog_name;
      $ogp_descr = $tag_title . 'のタグが付いた' . $name . '一覧です';
    /**
     * カスタムタクソノミーは作成していないため未検証のコードになります。
     * カスタムタクソノミーを作成した際は、一度正しく出力されているか確認してください。
     * また、get_queried_object()で取得したオブジェクトがWP_Termのインスタンスでない場合は
     * こちらに分岐しないようにしてあります。
     */
    } elseif( is_tax() ) {
      $term_title = single_term_title( '', false );
      $ogp_title = $term_title . ' | ' . $blog_name;
      $ogp_descr = 'ターム:' . $term_title . 'の' . $name . '一覧です';
    /**
     * get_queried_object()で取得したオブジェクトがWP_Userのインスタンスでない場合は
     * こちらに分岐しないようにしてあります。
     */
    } elseif( is_author() && $obj = get_userdata( get_query_var( 'author', false ) ) ) {
      $ogp_title = $obj->data->display_name . 'さんの' . $name;
      $ogp_descr = $obj->data->display_name . 'さんの' . $name . '一覧です';
    } elseif( is_day() ) {
      $day = get_the_time( 'Y年n月j日' );
      $ogp_title = $day . 'の' . $name;
      $ogp_descr = $day . 'の' . $name . '一覧です';
    } elseif( is_month() ) {
      $month = get_the_time( 'Y年n月' );
      $ogp_title = $month . 'の' . $name;
      $ogp_descr = $month . 'の' . $name . '一覧です';
    } elseif( is_year() ) {
      $year = get_the_time( 'Y年' );
      $ogp_title = $year . 'の' . $name;
      $ogp_descr = $year . 'の' . $name . '一覧です';
    /**
     * archive-news.phpで１記事のみをクエリして、個別ページと同じように見せています。
     * このため、投稿が存在しない場合のデフォルト値はこちらで設定しています。
     * また、is_post_type_archiveはnewsの全てのアーカイブでtrueになってしまうため、
     * 他のアーカイブより後に分岐させています。
     */
    } elseif( is_post_type_archive( 'news' ) ) {
      if( $get_post = get_post() ) {
        $ogp_title = $get_post->post_title . ' | ' . $blog_name;
        $ogp_descr = $get_post->post_excerpt;
      } else {
        $name = yadoken_post_type_name( 'news', false );
        $ogp_title = $name . ' | ' . $blog_name;
        $ogp_descr = 'やどけんからの' . $name . 'のページです。';
      }
    } elseif( is_post_type_archive( 'minutes' ) ) {
      $name = yadoken_post_type_name( 'minutes', false );
      $ogp_title = $name . ' | ' . $blog_name;
      $ogp_descr = '内部用' . $name . 'ページです。';
    } else {
      $ogp_title = 'アーカイブ';
      $ogp_descr = 'アーカイブページです';
    }
  } elseif( is_search() ) {
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ) );
    $s = get_search_query();
    $ogp_title = '検索結果：' . $s . ' | ' . $blog_name;
    $ogp_descr = 'キーワード：「' . $s . '」の検索結果ページです';
  } elseif( is_404() ) {
    $ogp_url = home_url();
    $ogp_title = '404 not found' . ' | ' . $blog_name;
    $ogp_descr = '404 not found';
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
      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
      $ogp_img = $thumb[0];
    } else {
      $ogp_img = yadoken_first_image( false );
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
  wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', array(), '4.4.1', 'all' );
  wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css', array(), '5.6.1', 'all' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css', array(), '1.1.3', 'all' );
  wp_enqueue_style( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1', 'all' );
  wp_enqueue_style( 'slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array(), '1.8.1', 'all' );
  wp_enqueue_script( 'popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array( 'jquery' ), '1.0.0', true );
  wp_enqueue_script( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), '1.0.0', true );
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery', 'slick' ), '1.0.0', true );
  wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array( 'jquery', 'popper' ), '1.0.0', true );
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
    $html = '<link rel="stylesheet" id="' . $handle . '-css" href="' . $href . '" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" type="text/css" media="' . $media . '">' . "\n";
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
  if( $handle === 'popper' ) {
    $tag = '<script type="text/javascript" src="' . $src . '" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>' . "\n";
  } elseif( $handle === 'bootstrap' ) {
    $tag = '<script type="text/javascript" src="' . $src . '" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>' . "\n";
  }
  return $tag;
}

?>