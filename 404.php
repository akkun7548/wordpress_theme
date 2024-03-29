<?php
/**
 * このファイルはクエリされたページが見つからなかった場合に 404 not found のページとして
 * 表示されます。
 */
get_header();
?>
<div class="title">
    <h1>404エラー</h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse main-wrapper">
    <div class="col-lg-8 main-content">
        <p>ご指定されたページは見つかりませんでした。</p>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch main-sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>