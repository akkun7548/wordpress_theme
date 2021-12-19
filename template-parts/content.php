<?php
/**<!--more-->のある場所まで記事の内容を全て表示します。 */
$date_format = get_option( 'date_format' );
?>
<article class="content">
    <div class="title">
        <h2><?php the_title(); ?></h2>
    </div>
    <div class="row justify-content-end date">
        <p>公開日 <?php the_time( $date_format ); ?></p>
        <p>更新日 <?php the_modified_date( $date_format ); ?></p>
    </div>
    <?php the_content( 'もっと読む' ); ?>
    <div style="clear: both;"></div>
</article>
