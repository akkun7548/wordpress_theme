<?php
/**
 * 全体で使用する関数
 * 
 * テーマ内の全てのファイルで使用可能にする想定で定義された関数です。
 * 
 * - yadoken_first_image
 *  - 最初の画像を取得する関数
 * 
 * - yadoken_post_type_name
 *  - 投稿タイプのネームラベルを取得する関数
 * 
 * - yadoken_archive_and_search_title
 *  - アーカイブ、検索結果ページのタイトルを出力する関数
 * 
 * - yadoken_display_post
 *  - ループ内で各投稿を出力する関数
 * 
 * - yadoken_date_link
 *  - 年/月/日をリンク付きで取得する関数
 * 
 * - yadoken_is_post_type_archive
 *  - 投稿タイプアーカイブページ(リンクがデフォルトでhome_url( '/投稿タイプスラッグ' )のページ)
 *    か判定する関数
 * 
 * - yadoken_author_link
 *  - 著者タクソノミーのリンクを出力
 */


/**
 * 最初の画像を取得する関数
 * 
 * 投稿の本文から最初に出てくる画像のurlを抜き出します。
 * set_post_thumbnail_size()で指定したサイズの画像が自動生成されているため、存在していれば
 * そちらを取得しています。
 * リサイズされた画像のファイル名の最後、拡張子の前の部分は * "-(横)x(縦)"もしくは"-scaled"
 * となっています。
 * 
 * @return string  投稿内の最初の画像のURLを返す
 */
function yadoken_first_image() {

  $first_img = '';
  $post_content = apply_filters( 'the_content', get_post()->post_content );

  /**$mathes[1]が空配列でもマッチした扱いになるため、返り値は利用していません。 */
  preg_match( '/<img.+?src=[\'"](.*?)[\'"].*?>/i', $post_content, $matches );

  // 投稿内に画像がなかった場合
  if( empty( $matches[1] ) ) {
    $first_img = get_template_directory_uri() . '/images/noimages.png';
  } else {
    $home_url = home_url( '/' );

    //同じサーバー内の画像だった場合
    if( strpos( $matches[1], $home_url ) !== false ) {
      global $_wp_additional_image_sizes;
      $path = substr( $matches[1], strlen( $home_url ) );
      $width = $_wp_additional_image_sizes['post-thumbnail']['width'];
      $height = $_wp_additional_image_sizes['post-thumbnail']['height'];

      //画像サイズ、拡張子の部分にマッチさせています。
      $file = preg_replace( '/(-\d{2,4}x\d{2,4}|-scaled|)\.(jpg|jpeg|jpe|gif|png|bmp|tiff|tif|ico)$/', "-{$width}x{$height}.$2", $path );

      //画像が存在しているかの確認
      if( file_exists( $file ) ) {
        $first_img = $home_url . $file;
      } else {
        $first_img = $matches[1];
      }

    //googleフォトなど外部ホストの画像だった場合
    } else {
      $first_img = $matches[1];
    }
  }

  return $first_img;
}


/**
 * 投稿タイプのネームラベルを取得する関数
 * 
 * 複数の投稿タイプが配列で渡された場合、先頭の投稿タイプのネームラベルを返します。
 * 
 * この関数に渡す投稿タイプ名の取得方法について
 * get_query_var( 'post_type' )では $wp_query->query_var->post_type を取得するため、
 * WP_Query オブジェクトを生成する時に指定した post_type が分かります。
 * これに対して get_post_type()では WP_Postオブジェクトである $post->post_type を取得します。
 * $post はループが一周するごとにWP_Queryで取得された各投稿の情報で書き換えられていくため、
 * 例えばループ前、後ではそれぞれ最初の投稿、最後の投稿の post_type が取得されます。
 * したがって、前者は複数の投稿がクエリされるアーカイブや検索結果などで全体を示す情報の
 * 取得に用い、後者は記事ページ内もしくはループ内で用いるのが適切となります。
 * 
 * @param string $post_type  投稿タイプ名
 * @return string  投稿タイプネームラベル
 */
function yadoken_post_type_name( $post_type = '' ) {

  $name = '';

  if( $post_type === '' ) {
    $post_type = 'post';
  }

  if( $obj = get_post_type_object( $post_type ) ) {
    $name = $obj->labels->name;
  } else {
    $name = '記事';
  }

  return $name;
}


/**
 * アーカイブ、検索結果ページのタイトルを出力する関数
 * 
 * wordpressで複数の記事がクエリされるページのタイトルをそれぞれ出力出来るようにしています。
 * 
 * @param string $name  投稿タイプ名
 */
function yadoken_archive_and_search_title( $name ) {

  if( is_home() || yadoken_is_post_type_archive() ) {
    if( ( $obj = get_queried_object() ) instanceof WP_Post ) {
      $title = apply_filters( 'the_title', $obj->post_title );
    } else {
      $title = $name;
    }
  } elseif( is_search() ) {
    global $wp_query;
    $title = '検索結果：' . get_search_query() . ' (' . $wp_query->found_posts . '件)';
  } elseif( is_category() ) {
    $title = single_cat_title( 'カテゴリー：', false );
  } elseif( is_tag() ) {
    $title = single_tag_title( 'タグ：', false );
  } elseif( is_tax() ) {
    $title = single_term_title( 'ターム：', false );
  } elseif( is_day() && $time = get_the_time( 'Y年n月j日' ) ) {
    $title = $time . 'の' . $name;
  } elseif( is_month() && $time = get_the_time( 'Y年n月' ) ) {
    $title = $time . 'の' . $name;
  } elseif( is_year() && $time = get_the_time( 'Y年' ) ) {
    $title = $time . 'の' . $name;

  /**
   * get_queried_object()ではWP_User以外のオブジェクトが取得されてしまうこともあったため、
   * 取得方法を変更しました。
   */
  } elseif( is_author() && $obj = get_userdata( get_query_var( 'author', false ) ) ) {
    $title = $obj->data->display_name . 'さんの' . $name;
  } elseif( is_post_type_archive( 'yadoken_minutes' ) ) {
    $title = $name;
  } else {
    $title = $name . 'アーカイブ';
  }

  echo esc_html( $title );
}


/**
 * ループ内で各投稿を出力する関数
 * 
 * 投稿をリスト表示する時の形式を投稿タイプ毎に統一する為の関数です。
 * 記事毎の投稿タイプで決定するため、各記事の投稿タイプを取得出来るget_post_type()を使用しています。
 * 読み込むテンプレートファイルは別途作成しています。
 */
function yadoken_display_post() {

  switch( get_post_type() ) {
    case 'post':
      get_template_part( 'template-parts/summary' );
      break;
    case 'yadoken_news':
      get_template_part( 'template-parts/content' );
      break;
    case 'yadoken_minutes':
      get_template_part( 'template-parts/list' );
      break;
    default:
      get_template_part( 'template-parts/list' );
  }
}


/**
 * 年/月/日をリンク付きで取得する関数
 * 
 * 年月日それぞれにリンクを付けて取得するのは記述が長くなるため関数化しました。
 * ループ内でのみ使用することができます。
 * 
 * @param string $format  日付フォーマット
 * @param string $before  リンクの前に挿入する文字列
 * @param string $after   リンクの後に挿入する文字列
 */
function yadoken_date_link( $format = '', $before = '', $after = '' ) {

  /**フォーマットが空だった場合は 設定 > 一般 > 日付のフォーマット に設定されているものを使用*/
  if( $format === '' ) {
    $format = get_option( 'date_format' );
  }

  /**php標準の日付フォーマットから年/月/日それぞれで使用するパラメーターをリスト化 */
  $times = array(
    'year' => array( 'o', 'Y', 'y' ),
    'month' => array( 'F', 'm', 'M', 'n' ),
    'day' => array( 'd', 'j' )
  );

  /**それぞれのリンクを取得する関数の引数を取得 */
  $year  = get_the_time( 'Y' );
  $month = get_the_time( 'n' );
  $day   = get_the_time( 'j' );

  /**
   * フォーマットを一文字ずつ配列に分解
   * 
   * utf-8のマルチバイト文字対応にするため、str_split()の代わりにpreg_split()の正規表現の
   * 中でマルチバイト対応(u)をしています。
   */
  $format_array = preg_split( '//u', $format, -1, PREG_SPLIT_NO_EMPTY );

  /**フォーマットを一文字ずつ展開 */
  foreach( $format_array as $key => $fragment ) {

    /**パラメーターとなる1バイト文字以外は処理をスキップ */
    if( strlen( $fragment ) !== 1 ) {
      continue;
    }

    /**期間毎にまとめたパラメーターの配列と期間名のキーを展開 */
    foreach( $times as $time => $params ) {

      /**各パラメーターを展開 */
      foreach( $params as $param ) {
        if( $param === $fragment ) {

          /**可変関数として利用する文字列作成 */
          $func = 'get_' . $time . '_link';
          $format_array[$key] = '<a href="' . esc_url( $func( $year, $month, $day ) ) . '">' . esc_html( get_the_time( $param ) ) . '</a>';

          /**リンク化した後は当該のパラメーターに関する処理をスキップ */
          continue 3;
        }
      }
    }

    /**上に列挙したもの以外のパラメーターをパース */
    $format_array[$key] = esc_html( get_the_time( $fragment ) );
  }
  
  /**配列を文字列として結合して出力 */
  echo $before . join( '', $format_array ) . $after;
}


/**
 * 投稿タイプアーカイブページ(リンクがデフォルトでhome_url( '/投稿タイプスラッグ' )のページ)
 * か判定する関数
 * 
 * @param string|string[] $post_types  カスタム投稿タイプ名
 * @return bool  判定結果
 */
function yadoken_is_post_type_archive( $post_types = '' ) {
  return is_post_type_archive( $post_types ) && ! ( is_search() || is_date() || is_author() || is_category() || is_tag() || is_tax() );
}


/**
 * 著者タクソノミーのリンクを出力
 * 
 * @param string $before  リンクの前の出力
 * @param string $sep     リンク同士を繋ぐ出力
 * @param string $after   リンク後の出力
 * @param int $post_id    投稿ID
 */
function yadoken_author_link( $before, $sep, $after, $post_id = 0 ) {
  $term_list = get_the_term_list( $post_id, 'yadoken_author', $before, $sep, $after );

  if( ! is_wp_error( $term_list ) ) {
    echo $term_list;
  }
}
?>