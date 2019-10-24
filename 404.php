<?php get_header(); ?>
<main>
<div class="main_visual">
    <img src="<?php echo get_template_directory_uri(); ?>/images/main_visual.jpg" alt="メインビジュアル画像">
    <h1>404エラー</h1>
</div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8 common_1">
            <p>ご指定されたページは見つかりませんでした。</p>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
<?php get_footer(); ?>