<?php
/**
 * ウィジェット関係
 * 
 * ウィジェット関係のクラスやフィルターをまとめたファイルです。
 * 以下のクラスを拡張したクラスを定義しています。
 * ・WP_Widget_Recent_Posts
 * ・WP_Widget_Archives
 * 全てのウィジェットに以下の機能を追加しています。
 * ・選択したページのみで表示する。
 * ・ログインユーザーのみに表示する。
 * slickスライダー用の１つのギャラリーのみしか表示しないウィジェットエリアを定義しています。
 */

/**
 * 最近の投稿を表示するウィジェットに投稿タイプ選択機能を追加
 * 
 * 参照：https://developer.wordpress.org/reference/classes/wp_widget_recent_posts/
 */
class Yadoken_WP_Widget_Recent_posts extends WP_Widget {

  /**
   * コンストラクタ
   * 
   * 設定画面に表示される説明などを設定できます。
   */
  public function __construct() {
    $widget_ops = array(
      'classname' => 'yadoken_widget_recent_entries',
      'description' => __( '投稿タイプを選択出来るようにした「最近の投稿」です。テーマオリジナルです。' ),
      'customize_selective_refresh' => true,
    );
    parent::__construct( 'yadoken-recent-posts', __( '最近の投稿(カスタム投稿タイプ含む)' ), $widget_ops );
    $this->alt_option_name = 'yadoken_widget_recent_entries';
  }

  /**
   * 実際のサイト内の設定した箇所にHTMLを出力するメゾット
   * 
   * WP_Queryオブジェクトのインスタンスを作成する際のオプションにpost_typeを追加し、
   * カスタム投稿タイプも取得できるようにしています。
   * 
   * @param array $args      表示するものの連想配列
   * @param array $instance  現在のインスタンスの連想配列
   */
  public function widget( $args, $instance ) {
    if ( ! isset( $args['widget_id'] ) ) {
      $args['widget_id'] = $this->id;
    }
    $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
    if ( ! $number ) {
      $number = 5;
    }
    $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
    /**
     * post_typeの値をサニタイズするため、フォームでの選択肢と同様に投稿タイプ名の配列を取得して
     * その値に含まれるか調べています。
     */
    $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
    $post_type = isset( $instance['post_type'] ) && in_array( $instance['post_type'], $post_types, true ) ? $instance['post_type'] : 'post';
    $r = new WP_Query(
      //フィルターはWP_Widget_Recent_postsにかける想定で実装されると考えられるため名前を変更しました。
      apply_filters(
        'yadoken_widget_posts_args',
        array(
          'posts_per_page' => $number,
          'no_found_rows' => true,
          'post_status' => 'publish',
          'ignore_sticky_posts' => true,
          //変更点はこの変数の追加のみです。
          'post_type' => $post_type,
        ),
        $instance
      )
    );
    if ( ! $r->have_posts() ) {
      return;
    }
    ?>
    <?php echo $args['before_widget']; ?>
    <?php
    if ( $title ) {
        echo $args['before_title'] . $title . $args['after_title'];
    }
    ?>
    <ul>
      <?php foreach ( $r->posts as $recent_post ) : ?>
        <?php
        $post_title   = get_the_title( $recent_post->ID );
        $title        = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
        $aria_current = '';

        if ( get_queried_object_id() === $recent_post->ID ) {
          $aria_current = ' aria-current="page"';
        }
        ?>
        <li>
          <a href="<?php the_permalink( $recent_post->ID ); ?>"<?php echo $aria_current; ?>><?php echo $title; ?></a>
          <?php if ( $show_date ) : ?>
            <span class="post-date"><?php echo get_the_date( '', $recent_post->ID ); ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php
    echo $args['after_widget'];
  }

  /**
   * ウィジェットの更新をするメゾット
   * 
   * 設定画面で保存を押した時に、設定を保存します。
   * 
   * @param array $new_instance  設定画面からの入力値の連想配列
   * @param array $old_instance  同じインスタンスの更新前の設定
   * @return array 保存する更新後の設定の連想配列
   */
  public function update( $new_instance, $old_instance ) {
    $instance              = $old_instance;
    $instance['title']     = sanitize_text_field( $new_instance['title'] );
    $instance['number']    = (int) $new_instance['number'];
    $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
    /**
     * post_typeの値をサニタイズするため、フォームでの選択肢と同様に投稿タイプ名の配列を取得して
     * その値に含まれるか調べています。
     */
    $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
    $instance['post_type'] = isset( $new_instance['post_type'] ) && in_array( $new_instance['post_type'], $post_types, true ) ? $new_instance['post_type'] : 'post';
    return $instance;
  }

  /**
   * ウィジェットの設定フォームを出力するメゾット
   * 
   * 設定画面にフォームを出力します。
   * 存在しているカスタム投稿タイプ全てに活動報告を合わせたものをリストアップして、
   * プルダウンメニューで選択できるようにしています。
   * 
   * @param string $instance  現在の設定の連想配列
   */
  public function form( $instance ) {
    $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
    $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
    //カスタム投稿タイプにpostを合わせた投稿タイプスラッグの配列を取得しています。
    $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
    $post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : 'post';
    ?>
    <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

    <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
    <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

    <p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>

    <p><label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( '投稿タイプ:' ) ?></label>
    <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
    <?php foreach( (array) $post_types as $_post_type ) {
      $name = yadoken_post_type_name( $_post_type, false );
      echo '<option value="' . esc_attr( $_post_type ) . '"' . selected( $post_type, $_post_type, false ) . '>' . esc_html( $name ) . '</option>' . "\n";
    } ?>
    </select></p>
    <?php
  }
}

/**
 * アーカイブのウィジェットに投稿タイプ選択機能を追加
 * 
 * 参照：https://developer.wordpress.org/reference/classes/wp_widget_archives/
 */
class Yadoken_WP_Widget_Archives extends WP_Widget {

  /**
   * コンストラクタ
   * 
   * 設定画面に表示される説明などを設定できます。
   */
  public function __construct() {
      $widget_ops = array(
          'classname'                   => 'yadoken_widgete_archive',
          'description'                 => __( '「アーカイブ」に期間と投稿タイプを選択する機能を追加したものです。' ),
          'customize_selective_refresh' => true,
      );
      parent::__construct( 'yadoken_archives', __( 'アーカイブ(カスタム投稿タイプ含む)' ), $widget_ops );
  }

  /**
   * 実際のサイト内に設定した箇所にHTMLを出力するメゾット
   * 
   * WP_Queryオブジェクトのインスタンスを作成する際のオプションにpost_typeを追加し、
   * カスタム投稿タイプも取得できるようにしています。
   * また、日別から年別まで投稿を取得する期間も指定できるようにしています。
   * 取得アーカイブ数の上限設定も追加しています。
   * 
   * @param array $args      表示するものの連想配列
   * @param array $instance  現在のインスタンスの連想配列
   */
  public function widget( $args, $instance ) {
      $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Archives' );
      $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
      $c = ! empty( $instance['count'] ) ? '1' : '0';
      $d = ! empty( $instance['dropdown'] ) ? '1' : '0';
      $types = array( 'yearly', 'monthly', 'daily', 'weekly' );
      $type = isset( $instance['type'] ) && in_array( $instance['type'], $types, true ) ? $instance['type'] : 'monthly';
      $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
      if ( ! $number ) {
        $number = 5;
      }
      /**
       * post_typeの値をサニタイズするため、フォームでの選択肢と同様に投稿タイプ名の配列を取得して
       * その値に含まれるか調べています。
       */
      $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
      $post_type = isset( $instance['post_type'] ) && in_array( $instance['post_type'], $post_types, true ) ? $instance['post_type'] : 'post' ;
      echo $args['before_widget'];
      if ( $title ) {
          echo $args['before_title'] . $title . $args['after_title'];
      }
      if ( $d ) {
          $dropdown_id = "{$this->id_base}-dropdown-{$this->number}";
          ?>
      <label class="screen-reader-text" for="<?php echo esc_attr( $dropdown_id ); ?>"><?php echo $title; ?></label>
      <select id="<?php echo esc_attr( $dropdown_id ); ?>" name="archive-dropdown">
          <?php
          //フィルターはWP_Widget_Recent_postsにかける想定で実装されると考えられるため名前を変更しました。
          $dropdown_args = apply_filters(
              'yadoken_widget_archives_dropdown_args',
              array(
                  'type'            => $type,
                  'format'          => 'option',
                  'show_post_count' => $c,
                  'post_type' => $post_type,
                  'limit' => $number,
              ),
              $instance
          );
          switch ( $dropdown_args['type'] ) {
              case 'yearly':
                  $label = __( 'Select Year' );
                  break;
              case 'monthly':
                  $label = __( 'Select Month' );
                  break;
              case 'daily':
                  $label = __( 'Select Day' );
                  break;
              case 'weekly':
                  $label = __( 'Select Week' );
                  break;
              default:
                  $label = __( 'Select Post' );
                  break;
          }
          $type_attr = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
          ?>
          <option value=""><?php echo esc_attr( $label ); ?></option>
          <?php wp_get_archives( $dropdown_args ); ?>
      </select>
<script<?php echo $type_attr; ?>>
/* <![CDATA[ */
(function() {
  var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
  function onSelectChange() {
      if ( dropdown.options[ dropdown.selectedIndex ].value !== '' ) {
          document.location.href = this.options[ this.selectedIndex ].value;
      }
  }
  dropdown.onchange = onSelectChange;
})();
/* ]]> */
</script>
      <?php } else { ?>
      <ul class="sidebar_list">
          <?php
          wp_get_archives(
            //フィルターはWP_Widget_Recent_postsにかける想定で実装されると考えられるため名前を変更しました。
              apply_filters(
                  'yadoken_widget_archives_args',
                  array(
                      'type'            => $type,
                      'show_post_count' => $c,
                      'post_type' => $post_type,
                      'limit' => $number,
                  ),
                  $instance
              )
          );
          ?>
      </ul>
          <?php
      }
      echo $args['after_widget'];
  }
  
  /**
   * ウィジェットの更新をするメゾット
   * 
   * 設定画面で保存を押した時に、設定を保存します。
   * 
   * @param array $new_instance  設定画面からの入力値の連想配列
   * @param array $old_instance  同じインスタンスの更新前の設定
   * @return array 保存する更新後の設定の連想配列
   */
  public function update( $new_instance, $old_instance ) {
      $instance             = $old_instance;
      $new_instance         = wp_parse_args(
          (array) $new_instance,
          array(
              'title'    => '',
              'count'    => 0,
              'dropdown' => '',
              'post_type' => 'post',
              'number' => 5,
              'type' => 'monthly',
          )
      );
      $instance['title']     = sanitize_text_field( $new_instance['title'] );
      $instance['count']     = $new_instance['count'] ? 1 : 0;
      $instance['dropdown']  = $new_instance['dropdown'] ? 1 : 0;
      $types = array( 'yearly', 'monthly', 'daily', 'weekly' );
      $instance['type']      = in_array( $new_instance['type'], $types, true ) ? $new_instance['type'] : 'monthly';
      $instance['number']    = (int) $new_instance['number'];
      /**
       * post_typeの値をサニタイズするため、フォームでの選択肢と同様に投稿タイプ名の配列を取得して
       * その値に含まれるか調べています。
       */
      $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
      $instance['post_type'] = in_array( $new_instance['post_type'], $post_types, true ) ? $new_instance['post_type'] : 'post';
      return $instance;
  }

  /**
   * ウィジェットの設定フォームを出力するメゾット
   * 
   * 設定画面にフォームを出力します。
   * 存在しているカスタム投稿タイプ全てに活動報告を合わせたものをリストアップして、
   * プルダウンメニューで選択できるようにしています。
   * また、日別から年別まで投稿を取得する期間も指定できるようにしています。
   * 
   * @param string $instance  現在の設定の連想配列
   */
  public function form( $instance ) {
      $instance = wp_parse_args(
          (array) $instance,
          array(
              'title'    => '',
              'count'    => 0,
              'dropdown' => '',
              'post_type' => 'post',
              'number' => 5,
              'type' => 'monthly',
          )
      );
      //カスタム投稿タイプにpostを合わせた投稿タイプスラッグの配列を取得しています。
      $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
      $types = array(
        'yearly' => __( '年別' ),
        'monthly' => __( '月別' ),
        'weekly' => __( '週別' ),
        'daily' => __( '日別' ),
      );
      ?>
      <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
      <p>
          <input class="checkbox" type="checkbox"<?php checked( $instance['dropdown'] ); ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" /> <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown' ); ?></label>
          <br/>
          <input class="checkbox" type="checkbox"<?php checked( $instance['count'] ); ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" /> <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
      </p>
      <p><label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( '期間:' ) ?></label>
      <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
      <?php foreach( (array) $types as $key => $value ) {
        echo '<option value="' . esc_attr( $key ) . '"' . selected( $instance['type'], $key, false ) . '>' . esc_html( $value ) . '</option>' . "\n";
      } ?>
      </select></p>
      <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( '表示アーカイブ数の上限' ); ?></label>
      <select id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>">
      <?php
      for ( $i = 1; $i <= 20; ++$i ) {
          echo "<option value='$i'" . selected( $instance['number'], $i, false ) . ">$i</option>";
      }
      ?>
      </select></p>
      <p><label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( '投稿タイプ:' ) ?></label>
      <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
      <?php foreach( (array) $post_types as $_post_type ) {
        $name = yadoken_post_type_name( $_post_type, false );
        echo '<option value="' . esc_attr( $_post_type ) . '"' . selected( $instance['post_type'], $_post_type, false ) . '>' . esc_html( $name ) . '</option>' . "\n";
      } ?>
      </select></p>
      <?php
  }
}

/**
 * 全ウィジェットに表示場所の選択機能を追加(入力フォーム)
 * 
 * 全てのウィジェットの下部にログインユーザー向けのチェックボックス、全ページで表示するの
 * チェックボックス、表示する各ページのチェックボックスを追加しています。
 * 全ページで表示するのチェックボックスを有効にしている状態では、各ページのチェックボックスは
 * 無効化されるため出力されないようにしました。
 * 
 * @param WP_Widget $this      ウィジェットのインスタンス(参照渡し)
 * @param null      $return    ヌル
 * @param array     $instance  ウィジェットの設定
 */
add_action( 'in_widget_form', 'yadoken_widget_form', 10, 3 );
function yadoken_widget_form( $widget, $return, $instance ) {
  $internal = isset( $instance['internal'] ) ? (bool) $instance['internal'] : false;
  //セットされていない場合の初期値としてtrue、つまり全ページで表示するように設定しています。
  $all_display = isset( $instance['all_display'] ) ? (bool) $instance['all_display'] : true;
  ?>
  <p><input class="checkbox" type="checkbox"<?php checked( $internal ); ?> id="<?php echo $widget->get_field_id( 'internal' ); ?>" name="<?php echo $widget->get_field_name( 'internal' ); ?>" />
  <label for="<?php echo $widget->get_field_id( 'internal' ); ?>"><?php _e( 'ログインユーザー向け' ) ?></label></p>
  <p><input class="checkbox" type="checkbox"<?php checked( $all_display ); ?> id="<?php echo $widget->get_field_id( 'all_display' ); ?>" name="<?php echo $widget->get_field_name( 'all_display' ); ?>" />
  <label for="<?php echo $widget->get_field_id( 'all_display' ); ?>"><?php _e( '全ページで表示' ) ?></label></p>
  <?php
  if( ! $all_display ) {
    $pages = get_pages();
    if( $pages ) {
      //管理画面にはstyle.cssが読み込まれないため、属性値としてスタイリングしました。 ?>
      <p style="font-size: 1.1rem; margin: 5px 0;"><?php _e( '固定ページ' ) ?></p>
      <p style="border: 2px solid #eeeeee; border-radius: 3px; padding: 5px; margin: -2px -2px 9px -2px;"><?php
      foreach( (array) $pages as $page ) {
        $key = $page->post_name;
        $page_display[$key] = ( isset( $instance['page_display'] ) && in_array( $key, $instance['page_display'], true ) ) ? true : false;
        ?>
          <input class="checkbox" type="checkbox"<?php checked( $page_display[$key] ); ?> id="<?php echo $widget->get_field_id( 'page_display' ); ?>" name="<?php echo $widget->get_field_name( 'page_display' ) . '[]'; ?>" value="<?php echo esc_attr( $key ); ?>" />
          <label for="<?php echo $widget->get_field_id( 'page_display' ); ?>"><?php echo esc_html( $page->post_title ); ?></label>
        <?php
      } ?>
      </p><?php
    }
    $post_types = array_merge( array( 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );
    ?>
    <p style="font-size: 1.1rem; margin: 5px 0;"><?php _e( '投稿タイプ' ) ?></p>
    <p style="border: 2px solid #eeeeee; border-radius: 3px; padding: 5px; margin: -2px -2px 9px -2px;"><?php
    foreach( $post_types as $key ) {
      $post_type_display[$key] = ( isset( $instance['post_type_display'] ) && in_array( $key, $instance['post_type_display'], true ) ) ? true : false;
      ?>
        <input class="checkbox" type="checkbox"<?php checked( $post_type_display[$key] ); ?> id="<?php echo $widget->get_field_id( 'post_type_display' ); ?>" name="<?php echo $widget->get_field_name( 'post_type_display' ) . '[]'; ?>" value="<?php echo esc_attr( $key ); ?>" />
        <label for="<?php echo $widget->get_field_id( 'post_type_display' ); ?>"><?php yadoken_post_type_name( $key ); ?></label>
      <?php  
    } ?>
    </p><?php
  }
}

/**
 * 全ウィジェットに表示場所の選択機能を追加(保存)
 * 
 * フォームからPOST送信されてくるデータはチェックボックスがオンになっていたもののみであり、
 * $key => 'on' という値になっているため、存在するキーとその値がtrueになった配列を作成しています。
 * 
 * @param array     $instance      現在のインスタンスの設定値
 * @param array     $new_instance  設定画面からの入力値の連想配列
 * @param array     $old_instance  同じインスタンスの更新前の設定
 * @param WP_Widget $this          現在のインスタンス
 * @return array 保存する更新後の設定の連想配列
 */
add_filter( 'widget_update_callback', 'yadoken_update_callback', 10, 2 );
function yadoken_update_callback( $instance, $new_instance ) {
  $instance['internal'] = isset( $new_instance['internal'] ) ? (bool) $new_instance['internal'] : false;
  $instance['all_display'] = isset( $new_instance['all_display'] ) ? (bool) $new_instance['all_display'] : false;
  $instance['page_display'] = array();
  if( isset( $new_instance['page_display'] ) ) {
    foreach( (array) $new_instance['page_display'] as $value ) {
      $instance['page_display'][] = $value;
    }
  }
  $instance['post_type_display'] = array();
  if( isset( $new_instance['post_type_display'] ) ) {
    foreach( (array) $new_instance['post_type_display'] as $value ) {
      $instance['post_type_display'][] = $value;
    }
  }
  return $instance;
}

/**
 * 全ウィジェットに表示場所の選択機能を追加(出力)
 * 
 * 配列のキーに当該ページのスラッグが含まれているかで、表示するかを判定しています。
 * 
 * $instanceをfalseにすることで当該ウィジェットが表示されなくなります。
 * 
 * @param array     $instance  現在のインスタンスの設定
 * @param WP_Widget $this      現在のインスタンス
 * @param array     $args      ウィジェットのデフォルト引数
 * @return array falseで表示しない
 */
add_filter( 'widget_display_callback', 'yadoken_display_callback', 10, 3 );
function yadoken_display_callback( $instance, $widget, $args ) {
  if( isset( $instance['internal'] ) && $instance['internal'] && ! is_user_logged_in() ) {
    $instance = false;
  } elseif( isset( $instance['all_display'] ) && ! $instance['all_display'] ) {
    if( is_page() ) {
      if( isset( $instance['page_display'] ) ) {
        $obj = get_queried_object();
        if( ! $obj instanceof WP_Post || ! in_array( $obj->post_name, $instance['page_display'], true ) ) {
          $instance = false;
        }  
      } else {
        $instance = false;
      }
    } else {
      if( isset( $instance['post_type_display'] ) ) {
        if( ! array_intersect( (array) yadoken_post_type(), $instance['post_type_display'] ) ) {
          $instance = false;
        }
      } else {
       $instance = false;
      }
    }
  }
  return $instance;
}

/**
 * ヘッダーウィジェット内のギャラリーを区別し、$instanceの中に識別子を追加する
 * 
 * @param array           $instance  ウィジェットの設定値
 * @param array           $args      register_sidebar()に与えた配列
 * @param WP_Widget_Media $this      ウィジェットのインスタンス
 */
add_filter( 'widget_media_gallery_instance', 'yadoken_media_gallery_instance', 10, 2 );
function yadoken_media_gallery_instance( $instance, $args ) {
  if( $args['id'] === 'slider' ) {
    $instance['yadoken_slider_identifier'] = true;
  }
  return $instance;
}

/**
 * ウィジェットのギャラリーの出力を変更
 * 
 * slickのスライダーに適合する形式に変更しています。
 * また、不要のためstyleタグを出力する関連の部分を削除しています。
 * html5かxhtmlかを判定する部分は、これらに依存しない構造に変更したため、削除しました。
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
          <div class='slider-item'>
              $image_output";
      if ( trim( $attachment->post_excerpt ) ) {
          $output .= "
              <div class='slider-caption'>
                  <p id='slider-$id'>" . wp_kses_post( wptexturize( $attachment->post_excerpt ) ) . "</p>
              </div>";
      }
      $output .= "
          </div>";
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
          <div class='slider-item'>
              <img src='" . esc_url( wp_get_attachment_image_src( $id, 'medium' )[0] ) . "'>
          </div>";
  }
  $output .= "
      </div>\n";
  return $output;
}

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
      'name' => __( 'Main Sidebar' ),
      'id' => 'main-sidebar',
      'description' => __( 'サイドバーに表示されるウィジェットエリアです。' ),
      'before_widget' => '<div class="widget_sidebar">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2>',
      'after_title'   => '</h2>',
    )
  );
  register_sidebar(
    array(
      'name' => __( 'Sticky Sidebar' ),
      'id' => 'sticky-sidebar',
      'description' => __( 'サイドバー下部のスクロールした際に付いてくる部分です。' ),
      'before_widget' => '<div class="widget_sidebar">',
      'after_widget' => '</div>',
      'before_title' => '<h2>',
      'after_title' => '</h2>',
    )
  );
  register_sidebar(
    array(
      'name' => __( 'Slider' ),
      'id' => 'slider',
      'description' => __( 'ヘッダー部分にスライダーを表示します。一番上のギャラリーのみが保存され、他は「使用停止中のウィジェット」となります。設定項目中の「カラム」は無効化されています。' ),
      'before_widget' => '<div class="widget_slider">',
      'after_widget' => '</div>',
      'before_title' => '<h2 class="slider-title"><span>',
      'after_title' => '</span></h2>',
    )
  );
}

/**
 * カスタムウィジェットのクラスを登録
 */
add_action( 'widgets_init', 'yadoken_register_widgets' );
function yadoken_register_widgets() {
  register_widget( 'Yadoken_WP_Widget_Recent_posts' );
  register_widget( 'Yadoken_WP_Widget_Archives' );
}

/**
 * カスタムHTMLウィジェット内でショートコードを実行
 */
add_filter( 'widget_text', 'do_shortcode' );

/**
 * ウィジェット内メニューの調整
 * 
 * ウィジェットの構造に合わせて変更をしています。
 */
add_filter( 'widget_nav_menu_args', 'yadoken_widget_nav_menu_args' );
function yadoken_widget_nav_menu_args( $nav_menu_args ) {
  $nav_menu_args['container'] = '';
  $nav_menu_args['items_wrap'] = '<ul>%3$s</ul>';
  return $nav_menu_args;
}

/**
 * スライダーウィジェット登録数、種数制限
 * 
 * スライダーに登録できるウィジェットをギャラリー１つのみに制限しています。
 * 
 * @param array $value      更新する値
 * @param array $old_value  更新前の値(不使用)
 * @param string $option    optionテーブルの名前(sidebars_widgets・不使用)
 * @return array  更新後の値
 */
add_filter( 'pre_update_option_sidebars_widgets', 'yadoken_sidebars_widgets' );
function yadoken_sidebars_widgets( $value ) {
  if( isset( $value['slider'] ) ) {
    if( $widgets = preg_grep( '/media_gallery/', $value['slider'] ) ) {
      $value['slider'] = array( reset( $widgets ) );
    } else {
      $value['slider'] = array();
    }
  }
  return $value;
}

?>