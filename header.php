<?php
/**
 * このファイルは全ページテンプレートでget_header()によって読み込まれます。
 * body_class()でbodyタグに各ページ固有のclassを付与して、スタイリングに活用しています。
 * bootstrap4のnavbarに対応するため、wp_nav_menu()では独自のwalkerクラスを使用しています。
 * 独自クラスについてはfunctions.phpを参照してください。
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<?php wp_head(); ?> 
</head>
<body <?php body_class(); ?>>
<header class="header">
    <nav class="navbar navbar-expand-md navbar-dark">
        <a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>">
            <img class="sp" src="<?php echo get_template_directory_uri(); ?>/images/logo_name_mini_resized.png" alt="野生動物研究会">
            <img class="pc" src="<?php echo get_template_directory_uri(); ?>/images/logo_name_resized.png" alt="野生動物研究会">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav">
                <?php
                $args = array(
                    'theme_location' => 'header-nav',
                    'container' => '',
                    'items_wrap' => '%3$s',
                    'walker' => new yadoken_walker_nav_menu
                );
                wp_nav_menu( $args );
                ?>
            </ul>
        </div>
    </nav>
</header>
<main role="main">
<?php
if( is_active_sidebar( 'slider' ) ) {
    dynamic_sidebar( 'slider' );
}
?>
