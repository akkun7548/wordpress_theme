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
        <div class="container-fluid justify-content-md-start">
            <a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>">
                <img class="sp" src="<?php echo get_template_directory_uri(); ?>/images/logo_name_mini_resized.png" alt="野生動物研究会">
                <img class="pc" src="<?php echo get_template_directory_uri(); ?>/images/logo_name_resized.png" alt="野生動物研究会">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <?php
                    $menu_name = 'header-nav';
                    if( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
                        $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
                        $menu_items = wp_get_nav_menu_items( $menu->term_id );
                        $menu_list = '';
                        $page_slug = '';
                        foreach( (array) $menu_items as $key => $menu_item ) {
                            $title = $menu_item->title;
                            $url = $menu_item->url;
                            $page_slug = str_replace( home_url( '/' ), '', $url );
                            $menu_list .= '<li class="nav-item';
                            if( empty( $page_slug ) && is_front_page() ) {
                                $menu_list .= ' active';
                            } elseif( ! empty( $page_slug ) && is_page( $page_slug ) ){
                                $menu_list .= ' active';
                            } elseif( $page_slug === 'report' && is_page( 'internalreport' ) ) {
                                $menu_list .= ' active';
                            } elseif( $page_slug === 'report' && ( get_query_var( 'post_type' ) === 'post' || get_post_type() === 'post' ) ) {
                                $menu_list .= ' active';
                            } elseif( $page_slug === 'news' && ( get_query_var( 'post_type' ) === 'news' || get_post_type() === 'news' ) ) {
                                $menu_list .= ' active';
                            }
                            $menu_list .= '"><a href="' . $url . '" class="nav-link">' . $title . '</a></li>' . "\n";
                        }
                    } else {
                        $menu_list = '<li class="nav-item">' . __( 'ヘッダーメニューがありません。' ) . '</li>';
                    }
                    echo $menu_list;
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
