<?php
////////////////////////////////////////全体で使用する関数////////////////////////////////////////
//最初の画像を取得する関数
function yd_first_image( $echo = true ) {
  $first_img = '';
  preg_match( '{<img.+src="' . home_url( '/' ) . '(\S+)"\s.+>}', get_post()->post_content, $match );
  if( empty( $match[1] ) ) { //no images
    $first_img = get_template_directory_uri() . '/images/noimages.png';
  } else {
    $file = preg_replace( '/-\d{1,4}x\d{1,4}\./', '-600x600.', $match[1] );
    if( file_exists( $file ) ) {
      $first_img = home_url( '/' ) . $file;
    } else {
      $first_img = $match[1];
    }
  }
  if( $echo ) {
    echo esc_url( $first_img );
  } else {
    return $first_img;
  }
}

//投稿タイプを取得する関数
//archive, singleで投稿タイプpostを表示する時、get_query_var( 'post_type' )の値は空になるため、
//そのような場合に統一的にpostを返すための関数です。
function yd_post_type() {
  $post_type = get_query_var( 'post_type' ); //$wp_query->query_vars->post_typeを取得
  if( empty( $post_type ) ) {
    $post_type = 'post';
  }
  return $post_type;
}

//投稿タイプのネームラベルを取得する関数
function yd_post_type_name( $post_type = '', $echo = true ) {
  $post_type = (array) $post_type;
  $name = '';
  $amp = '';
  $i = 0;
  foreach( $post_type as $type ) {
    $obj = get_post_type_object( $type );
    if( $i ) {
      $amp = ' & ';
    }
    if( $obj ) {
      $name .= $amp . $obj->labels->name;
      ++$i;
    }
  }
  if( empty( $name ) ) {
    $name = '記事';
  }
  if( $echo ) {
    echo esc_html( $name );
  } else {
    return $name;
  }
}

//アーカイブページのタイトルを取得する関数
//投稿タイプ毎のアーカイブテンプレートを作成する時の利便性のために作成しました。
function yd_archive_title( $name ) {
  if( is_category() ) {
      $title = single_cat_title( 'カテゴリー：', false );
  } elseif( is_tag() ) {
      $title = single_tag_title( 'タグ：', false );
  } elseif( is_tax() ) {
      $title = single_term_title( 'ターム：', false );
  } elseif( is_day() ) {
      $title = get_the_time( 'Y年n月j日' ) . 'の' . $name;
  } elseif( is_month() ) {
      $title = get_the_time( 'Y年n月' ) . 'の' . $name;
  } elseif( is_year() ) {
      $title = get_the_time( 'Y年' ) . 'の' . $name;
  } elseif( is_author() ) {
      $display_name = get_queried_object()->data->display_name;
      $title = $display_name . 'さんの' . $name;
  } else {
      $title = $name . "アーカイブ";
  }
  echo esc_html( $title );
}

//ループ内で各投稿を表示する関数
//投稿をリスト表示する時の形式を投稿タイプ毎に統一する為の関数です。
function yd_display_post() {
  switch( get_post_type() ) {
    case 'post':
      get_template_part( 'summary' );
      break;
    case 'news':
      get_template_part( 'title' );
      break;
    case 'minutes':
      get_template_part( 'list' );
      break;
    default:
      get_template_part( 'title' );
  }
}

////////////////////////////////////////wordpress設定////////////////////////////////////////
//adminbarの「こんにちは、」を消去
function delete_howdy( $wp_admin_bar ) {
  $my_account = $wp_admin_bar -> get_node( 'my-account' );
  $newtitle = str_replace( 'こんにちは、', '', $my_account->title );
  $wp_admin_bar -> add_node(
    array(
      'id' => 'my-account',
      'title' => $newtitle
    )
  );
}
add_filter( 'admin_bar_menu', 'delete_howdy', 25 );

//テーマ設定
function yd_setup() {
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'wp-block-styles' );
  register_nav_menus( array(
    'header-nav' => __( 'ヘッダーメニュー' ),
    'sidebar-internal' => __( '内部メニュー' ),
    'sidebar-links' => __( '旧サイトリンク' ),
    'footer-nav' => __( 'フッターメニュー' ),
    'content' => __( 'ショートコード' )
    ) 
  );
}
add_action( 'after_setup_theme', 'yd_setup' );

//メニューliタグのclass削除
function yd_menu_class_filter( $var ) {
  return is_array( $var ) ? array_intersect( $var, array() ) : '';
}
add_filter( 'nav_menu_css_class', 'yd_menu_class_filter', 100, 1 );
add_filter( 'nav_menu_item_id', 'yd_menu_class_filter', 100, 1 );
add_filter( 'page_css_class', 'yd_menu_class_filter', 100, 1 );

//"投稿"を"活動報告"に変更
function yd_change_post_menu_label() {
  global $menu;
  global $submenu;
  $name = '活動報告';
  $menu[5][0] = $name;
  $submenu['edit.php'][5][0] = $name . '一覧';
  $submenu['edit.php'][10][0] = '新しい' . $name;
  $submenu['edit.php'][16][0] = 'タグ';
}
add_action( 'init', 'yd_change_post_object_label' );

function yd_change_post_object_label() {
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
add_action( 'admin_menu', 'yd_change_post_menu_label' );

//カスタム投稿タイプ追加
function yd_register_post_type() {
  //お知らせ
  register_post_type(
    'news',
    array(
      'labels' => array(
        'name' => __('お知らせ')
      ),
      'description' => __('お知らせ用のカスタム投稿タイプです。'),
      'public' => true,
      'menu_position' => 10,
      'menu_icon' => 'dashicons-megaphone',
      'capability_type' => "page",
      'map_meta_cap' => true,
      'rewrite' => array(
        'with_front' => false
      ),
      'show_in_rest' => true,
    )
  );
  //議事録
  register_post_type(
    'minutes',
    array(
      'labels' => array(
        'name' => __('議事録')
      ),
      'description' => __('内部向け議事録のカスタム投稿タイプです。'),
      'public' => false,
      'publicly_queryable' => true,
      'show_ui' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-media-text',
      'capability_type' => "post",
      'map_meta_cap' => true,
      'rewrite' => array(
        'with_front' => false
      ),
      'show_in_rest' => true,
    )
  );
}
add_action('init', 'yd_register_post_type');

//寄稿者の画像アップロード
function allow_contributor_uploads() {
  $contributor = get_role( 'contributor' );
  $contributor->add_cap( 'upload_files' );
}
if ( current_user_can( 'contributor' ) && ! current_user_can( 'upload_files' ) ) {
  add_action( 'admin_init', 'allow_contributor_uploads' );
}

//メインループの取得内容を変更(固定ページ以外)
function yd_change_main_loop( $query ) {
  if ( is_admin() || ! $query->is_main_query() || $query->is_page() ) {
    return;
  }
  $post_type = $query->get( 'post_type' );
  if( is_user_logged_in() ) {
    global $_GET;
    if( isset( $_GET['post_status'] ) ) {
      $post_status = wp_unslash( $_GET['post_status'] );
      if( $post_status === 'pending' ) {
        $query->set( 'post_status', 'pending' );
      }
    }
  } else {
    if( $post_type === 'minutes' ) {
      $query->set( 'post_type', 'post' );
      $post_type = 'post';
    } elseif( is_array( $post_type ) ) {
      foreach( $post_type as $key => $type ) {
        if( $type === 'minutes' ) {
          unset( $post_type[$key] );
          $query->set( 'post_type', $post_type );
        }
      }
    }
  }
  if( $post_type === 'minutes' ) {
    $query->set( 'posts_per_page', 20 );
  }
}
add_action( 'pre_get_posts', 'yd_change_main_loop' );

//内部ページで非ログインユーザーのみ404にする対応
function yd_404() {
  if( is_page_template( 'internal.php' ) && ! is_user_logged_in() ) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
  }
}
add_action( 'template_redirect', 'yd_404' );

//月別アーカイブリンクで件数表示をaタグ内部に移動
function yd_archive_link( $html ) {
  $html = preg_replace( '/<\/a>(.*)<\/li>/', '$1</a></li>', $html );
  return $html;
}
add_filter( 'get_archives_link', 'yd_archive_link' );

////////////////////////////////////////head内のタグを制御////////////////////////////////////////
//タグ削除
remove_action( 'wp_head', 'wp_generator' );

//metaタグなどの出力
function yd_head() {
  global $yd_ogp;
  $ogp_url = '';
  $ogp_title = '';
  $ogp_descr = '';
  $ogp_img = '';
  $ogp_type = '';
  $twitter_account = '';
  $str = '';
  $yd_ogp['url'] =& $ogp_url;
  $yd_ogp['title'] =& $ogp_title;
  $yd_ogp['twitter'] =& $twitter_account;
  $home_url = home_url();
  $blog_name = get_bloginfo( 'name' );
  $blog_descr = get_bloginfo( 'description' );
  //og:title, og:descr, og:url
  if( is_front_page() ) {
    $ogp_url = $home_url;
    $ogp_title = $blog_name;
    $ogp_descr = $blog_descr;
  } elseif( is_singular() ) {
    $ogp_url = get_permalink();
    if( is_page() ) {
      $ogp_title = get_the_title() . ' | ' . $blog_name;
      switch( get_post()->post_name ) {
        case 'news':
          $ogp_descr = 'やどけんからのお知らせのページです。';
          break;
        case 'report':
          $ogp_descr = '部員の活動報告をブログ形式で掲載しています。';
          break;
        case 'contact':
          $ogp_descr = '活動拠点や連絡先についてのページです。';
          break;
        case 'links':
          $ogp_descr = '会員の個人ページや古いページへのリンク集です。';
          break;
        default:
          $ogp_descr = $blog_descr;
      }
    } elseif( is_single() ) {
      $ogp_descr = get_the_excerpt();
      switch( get_post_type() ) {
        case 'news':
          $ogp_title = get_the_title() . ' | ' . $blog_name;
          break;
        case 'post':
          $ogp_title = get_the_title();
          break;
        case 'minutes':
          $ogp_title = get_the_title() . ' | ' . $blog_name;
          break;
        default:
          $ogp_title = get_the_title();
      }
    }
  } elseif( is_archive() ) {
    $obj = get_post_type_object( get_post_type() );
    if( $obj ) {
      $name = $obj->labels->name;
    } else {
      $name = 'アーカイブ';
    }
    if( is_category() ) {
      $cat_title = single_cat_title( '', false );
      $ogp_url = get_category_link( get_query_var( 'cat' ) );
      $ogp_title = $cat_title . ' | ' . $blog_name;
      $ogp_descr = $cat_title . 'のカテゴリーの' . $name . '一覧です';
    } elseif( is_tag() ) {
      $tag_title = single_tag_title( '', false );
      $ogp_url = get_tag_link( get_query_var( 'tag' ) );
      $ogp_title = $tag_title . ' | ' . $blog_name;
      $ogp_descr = $tag_title . 'のタグが付いた' . $name . '一覧です';
    } elseif( is_tax() ) {
      $term_title = single_term_title( '', false );
      $ogp_url = get_term_link( get_queried_object_id(), get_query_var( 'taxonomy' ) );
      $ogp_title = $term_title . ' | ' . $blog_name;
      $ogp_descr = 'ターム:' . $term_title . 'の' . $name . '一覧です';
    } elseif( is_author() ) {
      $display_name = get_queried_object()->data->display_name;
      $ogp_url = get_author_posts_url( get_query_var( 'author' ) );
      $ogp_title = $display_name . 'さんの' . $name;
      $ogp_descr = $display_name . 'さんの' . $name . '一覧です';
    } elseif( is_day() ) {
      $day = get_the_time( 'Y年n月j日' );
      $ogp_url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
      $ogp_title = $day . 'の' . $name;
      $ogp_descr = $day . 'の' . $name . '一覧です';
    } elseif( is_month() ) {
      $month = get_the_time( 'Y年n月' );
      $ogp_url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
      $ogp_title = $month . 'の' . $name;
      $ogp_descr = $month . 'の' . $name . '一覧です';
    } elseif( is_year() ) {
      $year = get_the_time( 'Y年' );
      $ogp_url = get_year_link( get_query_var( 'year' ) );
      $ogp_title = $year . 'の' . $name;
      $ogp_descr = $year . 'の' . $name . '一覧です';
    } else {
      $ogp_url = $home_url;
      $ogp_title = 'アーカイブ';
      $ogp_descr = 'アーカイブページです';
    }
  } elseif( is_search() ) {
    $ogp_url = get_pagenum_link( get_query_var( 'paged', 1 ) );
    $ogp_title = '検索結果：' . get_search_query() . ' | ' . $blog_name;
    $ogp_descr = 'キーワード：「' . get_search_query() . '」の検索結果ページです';
  } elseif( is_404() ) {
    $ogp_url = $home_url;
    $ogp_title = '404 not found' . ' | ' . $blog_name;
    $ogp_descr = '404 not found';
  } else {
    $ogp_url = $home_url;
    $ogp_title = $blog_name;
    $ogp_descr = $blog_descr;
  }
  //og:type
  if( is_front_page() ) {
    $ogp_type = 'website';
  } else {
    $ogp_type = 'article';
  }
  //og:image
  if ( is_singular( 'post' ) ) {
    if( has_post_thumbnail() ) {
      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
      $ogp_img = $thumb[0];
    } else {
      $ogp_img = yd_first_image( false );
    }
  } else {
   $ogp_img = get_template_directory_uri() . '/images/logo.png';
  }
  //ツイッターアカウントID
  $twitter_account = 'yadoken_tsukuba';
  //metaタグ、title
  $str .= '<title>' . esc_attr( $ogp_title ) . '</title>' . "\n";
  $str .= '<meta name="description" content="' . esc_attr( $ogp_descr ) . '">' . "\n";
  $str .= '<meta name="thumbnail" content="' . esc_url( $ogp_img ) . '">' . "\n";
  //OGP
  $str .= '<meta property="og:title" content="' . esc_attr( $ogp_title ) . '">' . "\n";
  $str .= '<meta property="og:description" content="' . esc_attr( $ogp_descr ) . '">' . "\n";
  $str .= '<meta property="og:type" content="' . $ogp_type . '">' . "\n";
  $str .= '<meta property="og:url" content="' . esc_url( $ogp_url ) . '">' . "\n";
  $str .= '<meta property="og:image" content="' . esc_url( $ogp_img ) . '">' . "\n";
  $str .= '<meta property="og:site_name" content="' . esc_attr( $blog_name ) . '">' . "\n";
  $str .= '<meta property="og:locale" content="ja_JP">' . "\n";
  //twitter
  $str .= '<meta name="twitter:card" content="summary">' . "\n";
  $str .= '<meta name="twitter:site" content="@' . esc_attr( $twitter_account ) . '">' . "\n";
  echo $str;
}
add_action( 'wp_head', 'yd_head' );

//stylesheet, javascriptファイルの読み込み
function yd_scripts() {
  wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
  wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css', array(), '1.1.3', 'all' );
  wp_enqueue_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '1.0.0', true );
  wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery', 'popper' ), '1.0.0', true );
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), '1.0.1', true );
}
add_action( 'wp_enqueue_scripts', 'yd_scripts' );

////////////////////////////////////////ショートコード////////////////////////////////////////
//クエリ生成
function yd_query_shortcode( $atts ) {
  global $yd_query;
  $args = array(
    'posts_per_page' => get_option( 'posts_per_page' ),
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'paged' => get_query_var( 'paged', 1 ),
  );
  $add = array( 'sort' => false );//sortmenuによるソートをするかについての設定
  $atts = shortcode_atts(
    array_merge( $args, $add ),
    $atts,
    'query'
  );
  if( $atts['sort'] ) {
    $atts['orderby'] = get_query_var( 'orderby' );
    $atts['order'] = get_query_var( 'order' );
  }
  if( $atts['post_status'] !== 'publish' && ! is_user_logged_in() ) {
    $atts['post_status'] = 'publish';
  }
  $input = array_diff( $atts, $add );
  $yd_query = new WP_Query( $input );
}
add_shortcode( 'query', 'yd_query_shortcode' );

//ループ
function yd_loop_shortcode( $atts ) {
  global $yd_query;
  if( empty( $yd_query ) ) {
    return;
  }
  $str = '';
  $atts = shortcode_atts(
    array( 'summary' ),//取得した記事の表示方法
    $atts,
    'loop'
  );
  if( $yd_query->have_posts() ) {
    ob_start();
    while( $yd_query->have_posts() ) {
      $yd_query->the_post();
      switch( $atts[0] ) {
        case 'summary':
          get_template_part( 'summary' );
          break;
        case 'title':
          get_template_part( 'title' );
          break;
        case 'list':
          get_template_part( 'list' );
          break;  
        case 'links': ?>
          <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li> <?php
          break;
        case 'news': ?>
          <dt><?php the_time( 'Y/m/d' ); ?></dt><dd><a href="<?php the_permalink(); ?>"><?php the_title(); ?>を掲載しました。</a></dd> <?php
          break;
        default:
          yd_display_post();
      }
    }
    $str = ob_get_clean();
    wp_reset_postdata(); 
  } else {
    $obj = get_post_type_object( $yd_query->get( 'post_type' ) );
    if( $obj ) {
      $name = $obj->labels->name;
    } else {
      $name = '記事';
    }
    switch( $atts[0] ) {
      case 'summary':
      case 'title':
      case 'list':
        $str = $name . 'はありません。';
        break;
      case 'links':
        $str = '<li>' . $name . 'はありません</li>';
        break;
      default:
        $str = '';
    }
  }
  return $str;
}
add_shortcode( 'loop', 'yd_loop_shortcode' );

//ページネーション
function yd_pagination_shortcode() {
  if( is_singular() ) {
    global $yd_query;
    if( empty( $yd_query ) ) {
      return;
    } else {
      $query = $yd_query;
      $base = get_permalink();
    }
  } else {
    global $wp_query;
    $query = $wp_query;
    $url = strtok( get_pagenum_link(), '?' );
    $base = rtrim( $url, '/' );
  }
  $str = '';
  if ( $query->max_num_pages > 1 ) {
    $str = '<div class="row justify-content-center pagination stripe">' . "\n";
    $str .= paginate_links( array(
      'base' => $base . '%_%',
      'format' => '/page/%#%',
      'current' => max( 1, get_query_var( 'paged' ) ),
      'total' => $query->max_num_pages,
    ));
    $str .= '</div>' . "\n";
  }
  return $str;
}
add_shortcode( 'pagination', 'yd_pagination_shortcode' );

//検索フォーム
function yd_searchform_shortcode( $atts ) {
  global $yd_searchform;
  $atts = shortcode_atts(
    array(
      'post_type' => '',
      'post_status' => ''
    ),
    $atts,
    'searchform'
  );
  $yd_searchform = $atts;
  ob_start();
  get_search_form();
  return ob_get_clean();
}
add_shortcode( 'searchform', 'yd_searchform_shortcode' );

//ソートメニュー
function yd_sortmenu_shortcode() {
  ob_start();
  get_template_part( 'sortmenu' );
  return ob_get_clean();
}
add_shortcode( 'sortmenu', 'yd_sortmenu_shortcode' );

//サイト内リンク
function yd_url_shortcode( $atts ) {
  $atts = shortcode_atts(
    array( '' ),
    $atts,
    'url'
  );
  return esc_url( home_url( $atts[0] ) );
}
add_shortcode( 'url', 'yd_url_shortcode' );

//記事内メニュー
function yd_menu_shortcode() {
  $args = array(
    'theme_location' => 'content',
    'container' => '',
    'items_wrap' => '%3$s',
    'echo' => false
  );
  return wp_nav_menu( $args );
}
add_shortcode( 'menu', 'yd_menu_shortcode' );

?>