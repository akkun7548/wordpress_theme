<?php
/**
 * パンくずリストを出力
 * 
 * 全ページで設定しても問題ないように実装していますが、attachment.phpは親の投稿にリダイレクト
 * されるようにしてあるため、こちらでは全く考慮していません。
 */

/**ホームページでは何も出力しない。 */
if ( is_front_page() ) {
    return;
}
/**出力するHTML */
$str = '';
/**ページの種類毎に特有のオブジェクトを取得 */
$current_obj = get_queried_object();
/**親ページ、子ページそれぞれのsprintf()用のフォーマット */
$parent_format  = '<li class="breadcrumb-item"><a href="%s">%s</a></li>' . "\n\t\t";
$current_format = '<li class="breadcrumb-item active" aria-current="page">%s</li>' . "\n\t";

/**囲いタグ */
$str .= '<nav aria-label="パンくずリスト">' . "\n\t" . '<ul class="breadcrumb">' . "\n\t\t";

/**
 * タイトル取得
 * 
 * 「ホームページ」に設定されているページが存在する場合はそのタイトル、存在しない場合はサイトの名前を
 * 取得するように設定してしています。
 */
if( $page_on_front = get_option( 'page_on_front' ) ) {
    $home_title = apply_filters( 'the_title', get_post( $page_on_front )->post_title );
} else {
    $home_title = get_bloginfo( 'name' );
}
/**ホームのリンク */
$str .= sprintf( $parent_format, esc_url( home_url() ), esc_html( $home_title ) );

/**
 * 全ての個別ページ
 */
if( ( is_singular() || is_home() ) && $current_obj instanceof WP_Post ) {
    $post_type = $current_obj->post_type;
    /**
     * アーカイブがある投稿タイプはそのリンクを追加 
     * 
     * postはhas_archiveがfalseですが、投稿ページが設定されている場合はそちらがアーカイブ
     * として機能するため、そのリンクを取得しています。
     */
    if( $link = get_post_type_archive_link( $post_type ) ) {
        $post_type_obj = get_post_type_object( $post_type );
        $str .= sprintf( $parent_format, esc_url( $link ), esc_html( $post_type_obj->labels->name ) );
    }
    /**
     * 階層がある投稿タイプは親投稿を追加
     * 
     * 階層を反映して親投稿のリンクを取得しています。
     */
    if( $current_obj->post_parent ) {
        $parent_array = array_reverse( get_post_ancestors( $current_obj->ID ) );
        foreach( $parent_array as $parent_id ) {
            $str .= sprintf( $parent_format, esc_url( get_permalink( $parent_id ) ), esc_html( get_the_title( $parent_id ) ) );
        }
    }
    /**
     * 当該ページのタイトルを表示
     * 
     * リンクは不必要なため、設定していません。
     */
    $current_title = apply_filters( 'the_title', $current_obj->post_title );
    $str .= sprintf( $current_format, esc_html( $current_title ) );

/**
 * カスタム投稿タイプアーカイブ
 * 
 * 投稿タイプオブジェクトがクエリされています。
 */
} elseif( yadoken_is_post_type_archive() && $current_obj instanceof WP_Post_Type ) {
    $str .= sprintf( $current_format, esc_html( $current_obj->labels->name ) );
/**
 * 全てのターム
 */
} elseif( ( is_category() || is_tag() || is_tax() ) && $current_obj instanceof WP_Term ) {
    $term_id = $current_obj->term_id;
    $tax_name = $current_obj->taxonomy;
    $post_type = get_query_var( 'post_type', '' );
    if( empty( $post_type ) ) {
        $post_type = 'post';
    }
    /**
     * アーカイブがある投稿タイプのタクソノミーに属するタームがクエリされていた場合
     * 
     * アーカイブがある投稿タイプだった場合は、そのアーカイブページを先にリストアップしています。
     */
    if( $link = get_post_type_archive_link( $post_type ) ) {
        $post_type_obj = get_post_type_object( $post_type );
        $str .= sprintf( $parent_format, esc_url( $link ), esc_html( $post_type_obj->labels->name ) );
    }
    /**
     * タクソノミーに階層があった場合
     * 
     * 親タームを取得して先に表示しています。
     */
    if( $current_obj->parent ) {
        $parent_array = array_reverse( get_ancestors( $term_id, $tax_name ) );
        foreach( $parent_array as $parent_id ) {
            $parent_term = get_term( $parent_id, $tax_name );
            $str .= sprintf( $parent_format, esc_url( get_term_link( $parent_id, $tax_name ) ),  esc_html( $parent_term->name ) );
        }
    }
    /**
     * 当該タームのリンクを表示
     * 
     * リンクは不必要なため設定していません。
     */
    $str .= sprintf( $current_format, esc_html( $current_obj->name ) );

/**
 * 期間アーカイブ
 * 
 * $current_objはnullとなっています。
 * 期間アーカイブのパーマリンク構造に合わせて、月と日は必ず二桁になるようにしています。
 */
} elseif( is_date() ) {
    $year  = get_query_var( 'year', 0 );
    $month = get_query_var( 'monthnum', 0 );
    $day   = get_query_var( 'day', 0 );
    /**投稿タイプ用のGETパラメーターを追加 */
    $get = '';
    if( $post_type = get_query_var( 'post_type', '' ) ) {
        $get = '?post_type=' . $post_type;
    }

    /**日別アーカイブ */
    if( $day ) {
        $str .= sprintf( $parent_format, esc_url( get_year_link( $year ) . $get ), esc_html( $year ) );
        $str .= sprintf( $parent_format, esc_url( get_month_link( $year, $month ) . $get ), esc_html( $month ) );
        $str .= sprintf( $current_format, esc_html( $day ) );

    /**月別アーカイブ */
    } elseif( $month ) {
        $str .= sprintf( $parent_format, esc_url( get_year_link( $year ) . $get ), esc_html( $year ) );
        $str .= sprintf( $current_format, esc_html( $month ) );

    /**年別アーカイブ */
    } else {
        $str .= sprintf( $current_format, esc_html( $year ) );
    }
/**
 * 投稿者アーカイブ
 * 
 * カスタム投稿タイプがクエリされている時、$current_objはWP_Userオブジェクトにならないため
 * クエリ変数から取得しています。
 */
} elseif( is_author() && $user = get_userdata( get_query_var( 'author', false ) ) ) {
    $str .= sprintf( $current_format, esc_html( $user->data->display_name ) );
/**
 * 検索結果ページ
 */
} elseif( is_search() ) {
    $str .= sprintf( $current_format, '検索結果：' . esc_html( get_search_query() ) );
/**
 * 404ページの場合
 */
} elseif( is_404() ) {
    $str .= sprintf( $current_format, 'ご指定されたページは見つかりませんでした' );
/**
 * 例外のページ
 */
} else {
    $str .= sprintf( $current_format, esc_html( get_the_title() ) );
}

/**
 * 最初に設定したタグに対応する閉じタグ
 */
$str .= '</ul>' . "\n" . '</nav>' . "\n";

/**出力 */
echo $str;

?>