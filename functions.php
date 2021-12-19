<?php
/**
 * このファイルはテーマ内で最も早く読み込まれ、テーマに合わせてwordpressコアファイルの動作を
 * 変更する役割を持っています。
 * また、このテーマが有効である限り全てのページで読み込まれるため、全体を通して使う関数や設定
 * などもこちらで行います。
 * 関数名の重複を防ぐため、基本的に全ての関数に接頭辞"yadoken"を付加しています。
 * 
 * wordpressにはhookという仕組みがあり、テーマファイル内で定義した関数をコールバックとして
 * add_filter()やadd_action()というwordpress関数で登録することで、wordpressコアファイルの
 * 動作を変更することができます。
 * 参照：https://wpdocs.osdn.jp/%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3_API
 * 
 * phpの正規表現を理解しておくと、preg_replaceなどで文字列を操作する時非常に有用です。
 * 参照：https://www.php.net/manual/ja/reference.pcre.pattern.modifiers.php
 * 便宜的に公式のドキュメントを示していますが、もっと分かりやすいサイトもあるため
 * 各自好みのサイトを探しましょう。
 * 
 * wordpressの関数やhookの引数などの参照にあたっては、以下の公式サイトが便利です。
 * 参照：https://developer.wordpress.org/reference/
 */

$dir = get_template_directory();

/**
 * 全体で使用する関数
 * 
 * 他のfunctions系ファイルでも使用している関数の定義があるため、最初にインクルードしてください。
 */
require $dir . '/inc/definitions.php';

/**
 * wordpress設定
 * 
 * wordpressの動作をカスタマイズする関数をまとめたファイル
 */
require $dir . '/inc/settings.php';

/**
 * ユーザー設定
 * 
 * ユーザー関係の設定をまとめたファイル
 */
require $dir . '/inc/users.php';

/**
 * head内のタグ制御
 * 
 * headタグ内、footerの下のスクリプトタグを出力する関数のファイル
 */
require $dir . '/inc/metadata.php';

/**
 * 見た目関係
 * 
 * htmlに関係する変更を行っているふぁいる
 */
require $dir . '/inc/appearances.php';

/**
 * ウィジェット関係
 * 
 * ウィジェット関係のクラスやフィルターをまとめたファイル
 */
require $dir . '/inc/widgets.php';

/**
 * カスタマイザー関係
 * 
 * カスタマイザーを定義しているファイル
 */
require $dir . '/inc/customize.php';

/**
 * ショートコード
 * 
 * ショートコード関係の関数をまとめたファイル
 */
require $dir . '/inc/shortcodes.php';

?>