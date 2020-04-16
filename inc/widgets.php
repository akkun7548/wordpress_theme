<?php
/**
 * ウィジェット関係
 * 
 * ウィジェット関係のフィルターなどをまとめたファイルです。
 * slickスライダー用のギャラリーもしくは画像のみしか表示しないウィジェットエリアを定義しています。
 */

/**
 * ウィジェットエリアの登録
 * 
 * 全ページ共通サイドバー一箇所、その下にスクロールした際に追従してくるサイドバー一箇所を
 * 設定しています。sidebar.phpで使用しています。
 * ヘッダーに表示するスライダーのエリアを設定しています。header.phpで使用しています。
 */
add_action( 'widgets_init', 'yadoken_widgets_init' );
function yadoken_widgets_init() {
  register_sidebar(
    array(
      'name' => 'サイドバー',
      'id' => 'main-sidebar',
      'description' => 'サイドバーのウィジェットエリアです。',
      'before_widget' => '<div class="widget_sidebar">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2>',
      'after_title'   => '</h2>',
    )
  );
  register_sidebar(
    array(
      'name' => 'サイドバー(追従)',
      'id' => 'sticky-sidebar',
      'description' => 'サイドバー下部のスクロールした際に追従してくるウィジェットエリアです。',
      'before_widget' => '<div class="widget_sidebar">',
      'after_widget' => '</div>',
      'before_title' => '<h2>',
      'after_title' => '</h2>',
    )
  );
  register_sidebar(
    array(
      'name' => 'ページ上部',
      'id' => 'top',
      'description' => 'ページ上部のウィジェットエリアです。「ギャラリー」もしくは「画像」のみが保存され、他は「使用停止中のウィジェット」となり保存できないように設定してあります。ギャラリー設定項目中の「カラム」は無効化されています。',
      'before_widget' => '<div class="widget_top">',
      'after_widget' => '</div>',
      'before_title' => '<h2 class="title_top">',
      'after_title' => '</h2>',
    )
  );
}

/**
 * ヘッダーウィジェット内のギャラリーを区別し、$instanceの中に識別子を追加する
 * 
 * フィルターの名前は"widget_{$this->id_base}_instance"です。
 * 
 * @param array           $instance  ウィジェットの設定値
 * @param array           $args      register_sidebar()に与えた配列
 * @param WP_Widget_Media $this      ウィジェットのインスタンス
 */
add_filter( 'widget_media_gallery_instance', 'yadoken_media_gallery_instance', 10, 2 );
function yadoken_media_gallery_instance( $instance, $args ) {
  if( $args['id'] === 'top' ) {
    $instance['yadoken_slider_identifier'] = true;
  }
  return $instance;
}

/**
 * 「ギャラリー」ウィジェットの出力を変更
 * 
 * slickのスライダーに適合する形式に変更しています。
 * また、不要のためstyleタグを出力する関連の部分を削除しています。
 * html5か否かを判定する部分は、これらに依存しない構造に変更したため、削除しました。
 * 元のコードにあったフィルターも改変に伴い削除しました。
 * 
 * @param string $output    出力するHTML
 * @param array  $attr      gallery_shortcode()の引数
 * @param int    $instance  ギャラリーの固有ID(不使用)
 */
add_filter( 'post_gallery', 'yadoken_post_gallery', 10, 2 );
function yadoken_post_gallery( $output, $attr ) {
  if( ! isset( $attr['yadoken_slider_identifier'] ) ) {
    return '';
  }
  $post = get_post();
  $atts  = shortcode_atts(
      array(
          'order'      => 'ASC',
          'orderby'    => 'menu_order ID',
          'id'         => $post ? $post->ID : 0,
          'columns'    => 3,
          'size'       => 'thumbnail',
          'include'    => '',
          'exclude'    => '',
          'link'       => '',
      ),
      $attr,
      'gallery'
  );
  $id = intval( $atts['id'] );
  if ( ! empty( $atts['include'] ) ) {
      $_attachments = get_posts(
          array(
              'include'        => $atts['include'],
              'post_status'    => 'inherit',
              'post_type'      => 'attachment',
              'post_mime_type' => 'image',
              'order'          => $atts['order'],
              'orderby'        => $atts['orderby'],
          )
      );
      $attachments = array();
      foreach ( $_attachments as $key => $val ) {
          $attachments[$val->ID] = $_attachments[$key];
      }
  } elseif ( ! empty( $atts['exclude'] ) ) {
      $attachments = get_children(
          array(
              'post_parent'    => $id,
              'exclude'        => $atts['exclude'],
              'post_status'    => 'inherit',
              'post_type'      => 'attachment',
              'post_mime_type' => 'image',
              'order'          => $atts['order'],
              'orderby'        => $atts['orderby'],
          )
      );
  } else {
      $attachments = get_children(
          array(
              'post_parent'    => $id,
              'post_status'    => 'inherit',
              'post_type'      => 'attachment',
              'post_mime_type' => 'image',
              'order'          => $atts['order'],
              'orderby'        => $atts['orderby'],
          )
      );
  }
  if ( empty( $attachments ) ) {
      return '';
  }
  if ( is_feed() ) {
      $output = "\n";
      foreach ( $attachments as $att_id => $attachment ) {
          $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
      }
      return $output;
  }
  /**
   * サムネイルを表示する方のdivです。
   * data-lazyとslickのオプションを組み合わせて、遅延読み込みにしています。
   */
  $output .= "
      <div class='slider-thumb'>";
  foreach ( $attachments as $id => $attachment ) {
      $aria_describedby = ( trim( $attachment->post_excerpt ) ) ? " aria-describedby='slider-$id'" : '';
      if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
          $image_output = '<a href=' . esc_url( wp_get_attachment_url( $id ) ) . '><img data-lazy="' . esc_url( wp_get_attachment_image_src( $id, $atts['size'] )[0] ) . '"' . $aria_describedby .  '></a>';
      } elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
          $image_output = '<img data-lazy="' . esc_url( wp_get_attachment_image_src( $id, $atts['size'] )[0] ) . '"' . $aria_describedby .  '>';
      } else {
          $image_output = '<a href=' . esc_url( get_attachment_link( $id ) ) . '><img data-lazy="' . esc_url( wp_get_attachment_image_src( $id, $atts['size'] )[0] ) . '"' . $aria_describedby .  '></a>';
      }
      $output .= "
          <figure class='slider-item'>
              $image_output";
      if ( trim( $attachment->post_excerpt ) ) {
          $output .= "
              <figcaption id='slider-$id'>
                  " . wp_kses_post( wptexturize( $attachment->post_excerpt ) ) . "
              </figcaption>";
      }
      $output .= "
          </figure>";
  }
  $output .= "
      </div>\n";
  /**
   * 下のスライダーの方のdivです。
   * 設定に関わらず最小の大きさの画像を取得しています。
   * また、キャプションはなしにしています。
   */
  $output .= "
      <div class='slider-nav'>";
  foreach ( $attachments as $id => $attachment ) {
      $output .= "
          <figure class='slider-item'>
              <img src='" . esc_url( wp_get_attachment_image_src( $id, 'medium' )[0] ) . "'>
          </figure>";
  }
  $output .= "
      </div>\n";
  return $output;
}

/**
 * スライダーウィジェット登録数、種数制限
 * 
 * スライダーに登録できるウィジェットをギャラリーもしくは画像のみとしています。
 * 
 * @param array $value      更新する値
 * @param array $old_value  更新前の値(不使用)
 * @param string $option    optionテーブルの名前(sidebars_widgets・不使用)
 * @return array  更新後の値
 */
add_filter( 'pre_update_option_sidebars_widgets', 'yadoken_sidebars_widgets' );
function yadoken_sidebars_widgets( $value ) {
  if( isset( $value['top'] ) ) {
    //ウィジェットのbase_nameを利用して判別しています。
    $value['top'] = preg_grep( '/media_gallery|media_image/', $value['top'] );
  }
  return $value;
}

/**
 * 「画像」ウィジェットのインラインスタイリング防止
 * 
 * @param int $width       画像の幅
 * @param array $atts      img_caption_shortcode()の第一引数
 * @param string $content  img_caption_shortcode()の出力
 * @return string  画像の幅
 */
add_filter( 'img_caption_shortcode_width', 'yadoken_img_caption_shortcode_width' );
function yadoken_img_caption_shortcode_width( $width ) {
  return '';
}

/**
 * 「カスタムHTML」ウィジェット内でショートコードを実行
 */
add_filter( 'widget_text', 'do_shortcode' );

/**
 * 「ナビゲーションメニュー」ウィジェットの調整
 * 
 * ウィジェットの構造に合わせて変更をしています。
 */
add_filter( 'widget_nav_menu_args', 'yadoken_widget_nav_menu_args' );
function yadoken_widget_nav_menu_args( $nav_menu_args ) {
  $nav_menu_args['container'] = '';
  $nav_menu_args['items_wrap'] = '<ul>%3$s</ul>';
  return $nav_menu_args;
}

?>