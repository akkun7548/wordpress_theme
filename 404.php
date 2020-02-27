<?php
/**
 * このファイルはクエリされたページが見つからなかった場合に 404 not found のページとして
 * 表示されます。
 */
get_header();
?>
<div class="title_common_1">
    <h1>404エラー</h1>
</div>
<div class="row d-lg-flex flex-row-reverse wrapper">
    <div class="col-lg-8 content common_1">
        <p>ご指定されたページは見つかりませんでした。</p>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>