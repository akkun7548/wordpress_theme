<?php
////////////////////////////////////////全体で使用する関数////////////////////////////////////////
//最初の画像を取得する関数
function yd_first_image( $echo = true ) {
  global $post;
  $first_img = '';
  preg_match( '/<img.+src="(\S+)"\s.+>/', $post->post_content, $match );
  if( empty( $match[1] ) ) { //no images
    $first_img = get_template_directory_uri() . '/images/noimages.png';
  } else {
    $path_parts = pathinfo( $match[1] );
    $filename = preg_replace( '/-\d{1,4}x\d{1,4}/', "", $path_parts['filename'] );
    $dirname = preg_replace( '/(http|https):\/\/(.*?)\//', "", $path_parts['dirname'] );
    $img_pixel = 600;
    $first_img_square = $path_parts['dirname'] . '/' . $filename . '-' . $img_pixel . 'x' . $img_pixel . '.' . $path_parts['extension'];
    $file = $dirname . '/' . $filename . '-' . $img_pixel . 'x' . $img_pixel . '.' . $path_parts['extension'];
    if( file_exists( $file ) ) {
      $first_img = $first_img_square;
    } else {
      $first_img = $match[1];
    }
  }
  if( $echo ) {
    echo esc_url( $first_img );
  } else {
    return esc_url( $first_img );
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
  $labels = &$wp_post_types['post']->labels;
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
function yd_post_type() {
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
      'capability_type' => "page",
      'map_meta_cap' => true,
      'rewrite' => array(
        'with_front' => false
      ),
      'show_in_rest' => true,
    )
  );
}
add_action('init', 'yd_post_type');

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
  if ( is_admin() || ! $query->is_main_query() ) {
    return;
  }
  if( $query->is_main_query() && get_query_var( 'post_type' ) === 'news' ) {
    $query->set( 'posts_per_page', 20 );
  }
  if( get_query_var( 'post_type' ) === 'minutes' && ! is_user_logged_in() ) {
    $query->set( 'post_type', 'post' );
  }
}
add_action( 'pre_get_posts', 'yd_change_main_loop' );

//内部ページで非ログインユーザーのみ404にする対応
function yd_404() {
  if( ( is_page_template( 'internal.php' ) || get_post_type() === 'minutes' ) && ! is_user_logged_in() ) {
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
  global $ogp_url, $ogp_title, $twitter_account;
  $ogp_descr = '';
  $ogp_img = '';
  $ogp_type = '';
  $insert = '';
  //og:title, og:descr, og:url
  if( is_front_page() ) {
    $ogp_title = get_bloginfo( 'name' );
    $ogp_descr = get_bloginfo( 'description' );
    $ogp_url = home_url(); 
  } elseif( is_singular() ) {
    $ogp_url = get_permalink();
    if( is_page() ) {
      $ogp_title = get_the_title() . ' | ' . get_bloginfo( 'name' );
      if( is_page( 'news' ) ) {
        $ogp_descr = 'やどけんからのお知らせのページです。';
      } elseif( is_page( 'report' ) ) {
        $ogp_descr = '部員の活動報告をブログ形式で掲載しています。';
      } elseif ( is_page( 'contact' ) ) {
        $ogp_descr = '活動拠点や連絡先についてのページです。';
      } elseif( is_page( 'links' ) ) {
        $ogp_descr = '会員の個人ページや古いページへのリンク集です。';
      } else {
        $ogp_descr = get_bloginfo( 'description' );
      }
    } elseif( is_single() ) {
      $ogp_descr = get_the_excerpt();
      if( get_post_type() === 'news' ) {
        $ogp_title = get_the_title() . ' | ' . get_bloginfo( 'name' );
      } elseif( get_post_type() === 'post' ) {
        $ogp_title = get_the_title();
      } else {
        $ogp_title = get_the_title();
      }
    }
  } elseif( is_archive() ) {
    $name = get_post_type_object( get_post_type() )->labels->name;
    if( empty( $name ) ) {
      $name = 'アーカイブ';
    }
    if( is_category() ) {
      $ogp_url = get_category_link( get_query_var( 'cat' ) );
      $ogp_title = single_cat_title( "", false ) . ' | ' . get_bloginfo( 'name' );
      $ogp_descr = single_cat_title( "", false ) . 'のカテゴリーの' . $name . '一覧です';
    } elseif( is_tag() ) {
      $ogp_url = get_tag_link( get_query_var( 'tag' ) );
      $ogp_title = single_tag_title( "", false ) . ' | ' . get_bloginfo( 'name' );
      $ogp_descr = single_tag_title( "", false ) . 'のタグが付いた' . $name . '一覧です';
    } elseif( is_tax() ) {
      $ogp_url = get_term_link( get_queried_object_id(), get_query_var( 'taxonomy' ) );
      $ogp_title = single_term_title( "", false ) . ' | ' . get_bloginfo( 'name' );
      $ogp_descr = 'ターム:' . single_term_title( "", false ) . 'の' . $name . '一覧です';
    } elseif( is_author() ) {
      $ogp_url = get_author_posts_url( get_query_var( 'author' ) );
      $ogp_title = get_queried_object()->data->display_name . 'さんの' . $name;
      $ogp_descr = get_queried_object()->data->display_name . 'さんの' . $name . '一覧です';
    } elseif( is_day() ) {
      $ogp_url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
      $ogp_title = get_the_time( 'Y年n月j日' ) . 'の' . $name;
      $ogp_descr = get_the_time( 'Y年n月j日' ) . 'の' . $name . '一覧です';
    } elseif( is_month() ) {
      $ogp_url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
      $ogp_title = get_the_time( 'Y年n月' ) . 'の' . $name;
      $ogp_descr = get_the_time( 'Y年n月' ) . 'の' . $name . '一覧です';
    } elseif( is_year() ) {
      $ogp_url = get_year_link( get_query_var( 'year' ) );
      $ogp_title = get_the_time( 'Y年' ) . 'の' . $name;
      $ogp_descr = get_the_time( 'Y年' ) . 'の' . $name . '一覧です';
    } else {
      $ogp_url = home_url();
      $ogp_title = 'アーカイブ';
      $ogp_descr = 'アーカイブページです';
    }
  } elseif( is_search() ) {
    $ogp_url = home_url( $_SERVER["REQUEST_URI"] );
    $ogp_title = '検索結果：' . get_search_query() . ' | ' . get_bloginfo( 'name' );
    $ogp_descr = 'キーワード：「' . get_search_query() . '」の検索結果ページです';
  } elseif( is_404() ) {
    $ogp_url = home_url();
    $ogp_title = '404 not found' . ' | ' . get_bloginfo( 'name' );
    $ogp_descr = '404 not found';
  } else {
    $ogp_url = home_url();
    $ogp_title = get_bloginfo( 'name' );
    $ogp_descr = get_bloginfo( 'description' );
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
     $ps_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
     $ogp_img = $ps_thumb[0];
    } else {
      $ogp_img = yd_first_image( false );
    }
  } else {
   $ogp_img = get_template_directory_uri() . '/images/logo.png';
  }
  //ツイッターアカウントID
  $twitter_account = 'yadoken_tsukuba';
  //metaタグ、title
  $insert .= '<title>' . esc_attr( $ogp_title ) . '</title>' . "\n";
  $insert .= '<meta name="description" content=" ' . esc_attr( $ogp_descr ) . ' ">' . "\n";
  $insert .= '<meta name="thumbnail" content=" ' . esc_url( $ogp_img ) . ' ">' . "\n";
  //OGP
  $insert .= '<meta property="og:title" content=" ' . esc_attr( $ogp_title ) . ' ">' . "\n";
  $insert .= '<meta property="og:description" content=" ' . esc_attr( $ogp_descr ) . ' ">' . "\n";
  $insert .= '<meta property="og:type" content=" ' . $ogp_type . ' ">' . "\n";
  $insert .= '<meta property="og:url" content=" ' . esc_url( $ogp_url ) . ' ">' . "\n";
  $insert .= '<meta property="og:image" content=" ' . esc_url( $ogp_img ) . ' ">' . "\n";
  $insert .= '<meta property="og:site_name" content=" ' . esc_attr( get_bloginfo( 'name' ) ) . ' ">' . "\n";
  $insert .= '<meta property="og:locale" content="ja_JP">' . "\n";
  //twitter
  $insert .= '<meta name="twitter:card" content="summary">' . "\n";
  $insert .= '<meta name="twitter:site" content="@' . esc_attr( $twitter_account ) . '">' . "\n";
  echo $insert;
}
add_action( 'wp_head', 'yd_head' );

//stylesheet, javascriptファイルの読み込み
function yd_scripts() {
  wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
  wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.1/css/all.css' );
  wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css', array(), '1.1.2', 'all' );
  wp_enqueue_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '1.0.0', true );
  wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery', 'popper' ), '1.0.0', true );
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'yd_scripts' );

////////////////////////////////////////ショートコード////////////////////////////////////////
//ループ
function yd_loop_shortcode( $atts ) {
  global $yd_query;
  $str = '';
  $atts = shortcode_atts(
    array(
      'posts_per_page' => get_option( 'posts_per_page' ),
      'orderby' => 'post_date',
      'order' => 'DESC',
      'post_type' => 'post',
      'post_status' => 'publish',
      'paged' => get_query_var( 'paged', 1 ),
      'content' => 'summary'
    ),
    $atts,
    'loop'
  );
  $yd_query = new WP_Query( $atts );
  if( $yd_query->have_posts() ) {
    ob_start();
    while( $yd_query->have_posts() ) {
      $yd_query->the_post();
      if( $atts['content'] === 'summary' ) {
        get_template_part( 'summary' );
      } elseif( $atts['content'] === 'title' ) {
        get_template_part( 'title' );
      } elseif( $atts['content'] === 'links' ) { ?>
        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li> <?php
      } elseif( $atts['content'] === 'news' ) { ?>
        <dt><?php the_time( 'Y/m/d' ); ?></dt><dd><a href="<?php the_permalink(); ?>"><?php the_title(); ?>を掲載しました。</a></dd> <?php
      } elseif( $atts['content'] === 'list' ) {
        get_template_part( 'list' );
      }
    }
    $str = ob_get_clean();
    wp_reset_postdata(); 
  } else {
    if( $atts['post_type'] === 'post' ) {
      $str = '<p>記事がありません。</p>';
    } elseif( $atts['post_type'] === 'news' ) {
      $str = '<p>お知らせはありません。</p>';
    } elseif( $atts['post_type'] === 'minutes' ){
      $str = '<p>議事録はありません。</p>';
    }
  }
  return $str;
}
add_shortcode( 'loop', 'yd_loop_shortcode' );

//ページネーション
function yd_pagination_shortcode( $atts ) {
  global $wp_query, $yd_query, $paged;//デフォルトのクエリか直上のカスタムクエリを取得します。
  $atts = shortcode_atts( 
    array(
      'yd_format' => '?paged=%#%',
      'yd_esc_url' => true
    ),
    $atts,
    'pagination'
  );
  extract( $atts );
  if ( isset( $yd_query ) ) {
    $query = $yd_query;
  } else {
    $query = $wp_query;
  }
  $yd_pagination = '';
  if ( $query->max_num_pages > 1 ) {
    $yd_pagination = '<div class="row justify-content-center pagination stripe">' . "\n";
    $yd_pagination .= paginate_links( array(
      'base' => get_pagenum_link( 1, $yd_esc_url ) . '%_%',
      'format' => $yd_format,
      'current' => max( 1, $paged ),
      'total' => $query->max_num_pages
    ));
    $yd_pagination .= '</div>' . "\n";
  }
  return $yd_pagination;
}
add_shortcode( 'pagination', 'yd_pagination_shortcode' );

//検索フォーム
function yd_searchform_shortcode( $atts ) {
  global $yd_post_type;
  $atts = shortcode_atts(
    array( 'post_type' => 'post' ),
    $atts,
    'searchform'
  );
  $yd_post_type = $atts['post_type'];
  ob_start();
  get_search_form();
  return ob_get_clean();
}
add_shortcode( 'searchform', 'yd_searchform_shortcode' );

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