<?php if( is_user_logged_in() ) : ?>
    <h2>内部ページ</h2>
    <ul class="sidebar_list">
        <?php
        $internalnav_args = array(
            'theme_location' => 'sidebar-internal',
            'container' => '',
            'items_wrap' => '%3$s'
        );
        wp_nav_menu( $internalnav_args );
        ?>
    </ul>
<?php endif; ?>
<?php if( is_user_logged_in() && ( is_page( 'minutes' ) || get_query_var( 'post_type' ) === 'minutes' ) ) : ?>
    <h2>議事録</h2>
    <ul class="sidebar_list">
        <?php
        $arc_args = array(
            'type' => 'monthly',
            'limit' => '12',
            'format' => 'html',
            'show_post_count' => '1',
            'order' => 'DESC',
            'echo' => 0,
            'post_type' => 'minutes'
        );
        $arcs = wp_get_archives( $arc_args );
        if( $arcs ) {
            echo $arcs;
        } else { ?>
            <li>議事録はありません</li> <?php
        } ?>
    </ul>
<?php endif; ?>
<?php if( is_page( 'news' ) || is_singular( 'news' ) ) : ?>
    <h2>お知らせ一覧</h2>
    <ul class="sidebar_list">
        <?php
        echo do_shortcode( '[query
            posts_per_page="5" 
            orderby="date" 
            order="DESC" 
            post_type="news" 
            post_status="publish" 
        ]' );
        echo do_shortcode( '[loop links]' ); ?>
    </ul>
<? endif; ?>
<?php if( is_front_page() ) : ?>
    <h2>旧ホームページ</h4>
    <ul class="sidebar_list">
        <?php
        $linksnav_args = array(
            'theme_location' => 'sidebar-links',
            'container' => '',
            'items_wrap' => '%3$s'
        );
        wp_nav_menu( $linksnav_args );
        ?>
    </ul>
<?php endif; ?>
    <h2>共有</h2>
    <?php
    global $yd_ogp;
    extract( $yd_ogp );
    $enc_url = urlencode( esc_url( $url ) );
    $enc_title = urlencode( esc_attr( $title ) );
    $esc_twitter = esc_attr( $twitter );
    ?>
    <ul class="sidebar_share">
        <li>
            <a href="https://twitter.com/share?url=<?php echo $enc_url; ?>&via=<?php echo $esc_twitter; ?>&related=<?php echo $esc_twitter; ?>&text=<?php echo $enc_title; ?>" rel="nofollow">
                <i class="fab fa-twitter-square fa-fw fa-3x twitter"></i>
            </a>
        </li>
        <li>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $enc_url; ?>" rel="nofollow">
                <i class="fab fa-facebook-square fa-fw fa-3x facebook"></i>
            </a>
        </li>
        <li>
            <a href="https://social-plugins.line.me/lineit/share?url=<?php echo $enc_url; ?>" rel="nofollow">
                <i class="fab fa-line fa-fw fa-3x line"></i>
            </a>
        </li>
    </ul>
    <h2>公式ツイッター</h2>
    <div class="sticky">
        <a class="twitter-timeline sticky" width="100%" height="800px" href="https://twitter.com/yadoken_tsukuba?ref_src=twsrc%5Etfw">Tweets by yadoken_tsukuba</a>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> 
    </div>
