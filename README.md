### Wordpressテーマ "Yadoken"
野生動物研究会WEBサイトで使用しているWordpressテーマです。
当該WEBサイトは2019年10月よりこちらのコードで動作しています。
また、2020年4月にテーマとは分離すべき一部の機能をmu-pluginsに移行したため、こちらのコードのみでは現在の動作となりません。分離した機能の詳細は"mu-plugins_yadoken"のリポジトリを参照してください。

## 使用方法
Wordpress 5.2以上の環境で、wp-content/themes/ディレクトリにこのリポジトリをcloneしてください。

## 機能
- Wordpressテーマとして基本的な各種ページの生成
- ヘッダー、フッターメニューの編集
- カスタマイザーによる一部の画像の変更
- metaタグを通した基本的なSEO対策
- cssによる基本的なスマホ対応(不完全)
- mu-pluginsにて定義されたカスタム投稿タイプの一覧及び記事ページの生成
- 記事内でのショートコードの利用
 - 機能は[inc/shortcodes.php](/inc/shortcodes.php)のコメントを参照してください。

## 依存
このテーマが依存しているフレームワークなどの一覧表です。
cssやjavascriptの外部リソースを読み込むことで利用しています。
|名前|バージョン|
|:---:|:---:|
|Bootstrap|4.4.1|
|slick-carousel|1.8.1|
|Font Awesome|5.6.1|