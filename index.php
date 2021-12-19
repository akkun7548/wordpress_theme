<?php
/**
 * このファイルはどのテンプレートファイルにも分岐しなかった場合に用いられます。
 * sigular.phpを作成しているため、個別にページ以外のアーカイブ、検索結果ページなどがこちらの
 * コードを利用して出力しています。
 * また、テンプレートを作成しているものはそちらのファイルのコードを利用して出力されます。
 */
get_header();
$name = yadoken_post_type_name( get_query_var( 'post_type', '' ) ); ?>
<div class="title">
    <h1><?php yadoken_archive_and_search_title( $name ); ?></h1>
</div>
<?php get_template_part( 'template-parts/breadcrumb' ); ?>
<div class="row d-lg-flex flex-row-reverse main-wrapper">
    <div class="col-lg-8 main-content">
        <div class="row justify-content-end searchform stripe"><?php
            get_search_form(); ?>
        </div>
        <div class="row justify-content-between searchform stripe"><?php
            get_template_part( 'template-parts/count' );
            get_template_part( 'template-parts/sortmenu' ); ?>
        </div><?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                yadoken_display_post();
            endwhile;
            echo do_shortcode( '[pagination]' );
        else : ?>
            <P>該当する<?php echo esc_html( $name ); ?>はありません。</p>                
        <?php
        endif; ?>
    </div>
    <aside class="col-lg-4 align-self-lg-stretch main-sidebar">
        <?php get_sidebar(); ?>
    </aside>
</div>
<?php get_footer(); ?>