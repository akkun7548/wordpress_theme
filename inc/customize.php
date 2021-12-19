<?php
/**
 * テーマカスタマイズAPI
 * 
 * 管理画面のサイドバーから 外観 > カスタマイズ でアクセスできるテーマカスタマイザーに、
 * セクションや設定項目を追加しています。
 * 
 * - customize_register
 *  - 設定項目を追加　以下設定項目一覧
 * 
 * ・ヘッダー
 *  ・ロゴ画像
 *  ・ロゴ画像(スマホ用)
 * 
 * ・メイン
 *  ・No Images
 * 
 * その他、画像の設定値は出力する場所で直接取得しています。
 */

 
/**
 * カスタマイザーにセクションと設定項目を追加、設定値を管理オブジェクトに登録
 * 
 * 使用しているメソッドはadd_section(), add_setting(), add_control()で、一つのセクションに
 * 対して複数のコントロール(設定項目)をまとめることができます。
 * また、add_setting()は設定値の名前と初期値を登録します。
 * 
 * @param WP_Customize_Manager $wp_customize  カスタマイザー管理オブジェクトのインスタンス
 */
add_action( 'customize_register', function( $wp_customize ) {

    /**
     * ヘッダー
     * 
     * ヘッダー要素関係の設定をするセクションです。
     * 以下の設定をまとめています。
     * ・ロゴ画像
     * ・ロゴ画像(スマホ用)
     * 
     * ロゴ画像、及びロゴ画像(スマホ用)では初期値としてテーマディレクトリ内のimages/以下にある
     * 画像を参照しているため、そちらを変更する際は注意してください。
     */
    $wp_customize->add_section(
        'header_section',
        array(
            'priority' => 30,
            'title' => 'ヘッダー',
            'description' => 'ヘッダー要素内の設定です。(テーマ固有)'
        )
    );

    // ロゴ画像
    $wp_customize->add_setting(
        'header_logo_img',
        array(
            'default' => get_template_directory_uri() . '/images/logo_name_resized.png'
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'header_logo_img',
            array(
                'label' => 'ロゴ画像',
                'section' => 'header_section',
                'settings' => 'header_logo_img',
                'description' => '左上に表示するロゴ画像を選択してください。画面の幅が992px以上の時に表示されます。画像の高さは50pxを目安にしてください。'
            )
        )
    );

    // ロゴ画像(スマホ用)
    $wp_customize->add_setting(
        'header_small_logo_img',
        array(
            'default' => get_template_directory_uri() . '/images/logo_name_mini_resized.png'
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'header_small_logo_img',
            array(
                'label' => 'ロゴ画像(スマホ用)',
                'section' => 'header_section',
                'settings' => 'header_small_logo_img',
                'description' => '上部に表示されるスマホ用のロゴ画像を選択してください。画面の幅が992px未満の時に表示されます。画像の高さは50pxを目安にしてください。また、画面の幅が狭いスマホでヘッダーが改行されるのを防ぐため、幅は250px未満としてください。'
            )
        )
    );


    /**
     * メイン
     * 
     * メイン要素内の設定をまとめたセクションです。
     * 以下の設定をまとめています。
     
     */
    $wp_customize->add_section(
        'main_section',
        array(
            'priority' => 32,
            'title' => 'メイン',
            'description' => 'メイン要素内の設定です。(テーマ固有)'
        )
    );

    
    // No Images
    $wp_customize->add_setting(
        'noimages',
        array(
            'default' => get_template_directory_uri() . '/images/noimages.png'
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'noimages',
            array(
                'label' => 'No Images',
                'section' => 'main_section',
                'settings' => 'noimages',
                'description' => '画像がない記事のサムネイルとして使用する画像を選択してください。記事サマリーや共有時のサムネイルに適用されます。'
            )
        )
    );

});

?>