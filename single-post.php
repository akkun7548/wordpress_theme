<?php
if( have_posts() ) :
    while( have_posts() ) :
    the_post();
    get_header(); ?>
<main>
<div class="main_visual"></div>
<div class="wrapper container-fluid">
<!--コンテンツ-->
    <div class="row d-lg-flex flex-row-reverse">
        <div class="content col-lg-8">
            <article <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>
                <div class="row justify-content-end info_single-post">
                    <?php
                    if( has_tag() ) : ?>
                        <p class="info_single-post"><?php the_tags('タグ ',', ',''); ?></p>
                    <?php
                    endif; ?>
                    <p class="info_single-post">投稿日 <?php the_time( 'Y年' ) ?><a href="<?php echo get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ); ?>"><?php the_time( 'n月' ); ?></a><?php the_time( 'j日' ) ?></p>
                    <p class="info_single-post">更新日 <?php the_modified_date( 'Y年n月j日' ); ?></p>
                </div>
                <?php the_content(); ?>
                <div class="clear-both"></div>
            </article>
            <?php wp_link_pages(); ?>
            <h2 class="author">投稿者紹介</h2>
            <div class="row author">
                <div class="col-sm-4">
                    <div class="row justify-content-center">
                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 240 ); ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <p class="author"><?php the_author_posts_link(); ?></p>
                    <p><?php the_author_meta( 'description' ); ?></p>
                </div>
            </div>
        <?php comments_template(); ?>
        </div>
<!--サイドバー-->
        <aside class="sidebar col-lg-4 align-self-lg-stretch">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>
</main>
    <?php
    get_footer();
    endwhile;
else:
    wp_safe_redirect( home_url(), 302 );
endif; ?>