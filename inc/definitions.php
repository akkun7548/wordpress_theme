<?php
/**
 * 全体で使用する関数
 * 
 * テーマ内の全てのファイルで使用可能にする想定で定義された関数です。
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
  /**空配列でもマッチした扱いになるため、返り値は利用していません。 */
  preg_match( '/<img.+?src=[\'"](.*?)[\'"].*?>/i', get_post()->post_content, $matches );
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
 * 投稿タイプを取得する関数
 * 
 * 投稿タイプpostの投稿を表示する時、get_query_var( 'post_type' )の値は空になるため、
 * そのような場合に統一的にpostを返すための関数です。
 * 
 * get_query_var( 'post_type' )では $wp_query->query_var->post_type を取得するため、
 * WP_Query オブジェクトを生成する時に指定した post_type が分かります。
 * これに対して get_post_type()では WP_Postオブジェクトである $post->post_type を取得します。
 * $post はループが一周するごとにWP_Queryで取得された各投稿の情報で書き換えられていくため、
 * 例えばループ前、後ではそれぞれ最初の投稿、最後の投稿の post_type が取得されます。
 * したがって、前者は複数の投稿がクエリされるアーカイブや検索結果などで全体を示す情報の
 * 取得に用い、後者は記事ページ内もしくはループ内で用いるのが適切となります。
 * 
 * get_query_var()は第二引数にデフォルト値を設定できるのですが、
 * get_query_var( 'post_type', 'post' )では空判定がempty()ではなくisset()で行われているため、
 * 値が空文字列の場合も出力されるようになっているため不採用です。
 * 
 * @return string  投稿タイプスラッグ
 */
function yadoken_post_type() {
  $post_type = get_query_var( 'post_type' );
  if( empty( $post_type ) ) {
    $post_type = 'post';
  }
  return $post_type;
}

/**
 * 投稿タイプのネームラベルを取得する関数
 * 
 * 複数の投稿タイプが配列で渡された場合、間を$sepで繋いで列記した文字列として返します。
 * 
 * @param string|string[] $post_type  投稿タイプ名
 * @param string       $sep        つなぎ文字列
 * @return string  投稿タイプネームラベルを$sepで繋いで列記した文字列
 */
function yadoken_post_type_name( $post_type = '', $sep = ' ' ) {
  $name = '';
  $names = array();
  foreach( (array) $post_type as $type ) {
    if( $obj = get_post_type_object( $type ) ) {
      $names[] = $obj->labels->name;
    }
  }
  if( empty( $names ) ) {
    $name = '記事';
  } else {
    $name = join( $sep, $names );
  }
  return $name;
}

/**
 * アーカイブページのタイトルを出力する関数
 * 
 * 投稿タイプ毎のアーカイブテンプレートを作成する時の利便性のために作成しました。
 * 
 * @param string $name  投稿タイプ名
 */
function yadoken_archive_title( $name ) {
  if( is_category() ) {
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
      get_template_part( 'template-parts/title' );
      break;
    case 'yadoken_minutes':
      get_template_part( 'template-parts/list' );
      break;
    default:
      get_template_part( 'template-parts/title' );
  }
}

/**
 * 年/月/日をリンク付きで取得する関数
 * 
 * 年月日それぞれにリンクを付けて取得するのは記述が長くなるため関数化しました。
 * summary.php内でaタグリンクに重ねて使用するため、objectタグを利用可能にしています。
 * 
 * @param bool $object  trueでHTMLのobjectタグあり、falseでなし
 */
function yadoken_date_link( $object = true ) {
  $start = '';
  $end = '';
  if( $object ) {
    $start = '<object>';
    $end = '</object>';
  }
  $date = '';
  $year = get_the_time( 'Y' );
  $month = get_the_time( 'n' );
  $day = get_the_time( 'j' );
  $links[$year . '年'] = get_year_link( $year );
  $links[$month . '月'] = get_month_link( $year, $month );
  $links[$day . '日'] = get_day_link( $year, $month, $day );
  foreach( $links as $key => $value ) {
      $date .= $start . '<a href="' . esc_url( $value ) . '">' . esc_html( $key ) . '</a>' . $end;
  }
  echo $date;
}

/**
 * 投稿タイプアーカイブページ(リンクがデフォルトでhome_url( '/投稿タイプスラッグ' )のページ)
 * か判定する関数
 * 
 * @param string|string[] $post_types  カスタム投稿タイプ名
 * @return bool  判定結果
 */
function yadoken_is_post_type_archive( $post_types = '' ) {
  return is_post_type_archive( $post_types ) && ! ( is_date() || is_author() || is_category() || is_tag() || is_tax() );
}

?>