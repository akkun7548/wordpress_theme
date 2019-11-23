<?php
$menu_list = '';
if( ( $locations = get_nav_menu_locations() ) && isset( $locations['header-nav'] ) ) {
    $menu = wp_get_nav_menu_object( $locations['header-nav'] );
    $menu_items = wp_get_nav_menu_items( $menu->term_id );
    $slug = '';
    foreach( (array) $menu_items as $menu_item ) {
        $title = $menu_item->title;
        $url = $menu_item->url;
        $slug = get_post( $menu_item->object_id )->post_name;
        $menu_list .= '<li class="nav-item';
        if( is_page( $slug ) ){
            $menu_list .= ' active';
        } else {
            if( $slug === 'report' ) {
                if( is_page( 'internalreport' ) ) {
                    $menu_list .= ' active';
                } else {
                    $slug = 'post';
                }
            }
            if( is_page() ) {
                $post_type = (array) get_query_var( 'post_type' );
            } else {
                $post_type = (array) yd_post_type();
            }
            if( in_array( $slug, $post_type ) ) {
                $menu_list .= ' active';
            }
        }
        $menu_list .= '"><a href="' . esc_url( $url ) . '" class="nav-link">' . esc_html( $title ) . '</a></li>' . "\n";
    }
}
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
        <div class="container-fluid justify-content-md-start">
            <a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>">
                <img class="sp" src="<?php echo get_template_directory_uri(); ?>/images/logo_name_mini_resized.png" alt="野生動物研究会">
                <img class="pc" src="<?php echo get_template_directory_uri(); ?>/images/logo_name_resized.png" alt="野生動物研究会">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <?php echo $menu_list; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
