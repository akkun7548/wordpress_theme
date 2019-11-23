<?php
if ( has_post_thumbnail() ) {
    $thumb = get_the_post_thumbnail();
} else {
    $thumb = '<img src="' . esc_url( yd_first_image( false ) ) . '" alt="' . esc_attr( get_the_title() ) . '">';
}
$date = '';
$year = get_the_time( 'Y' );
$month = get_the_time( 'n' );
$day = get_the_time( 'j' );
$links[$year . '年'] = get_year_link( $year );
$links[$month . '月'] = get_month_link( $year, $month );
$links[$day . '日'] = get_day_link( $year, $month, $day );
foreach( $links as $key => $value ) {
    $date .= '<object><a href="' . esc_url( $value ) . '">' . esc_html( $key ) . '</a></object>';
}
?>
<a href="<?php the_permalink(); ?>" class="row d-sm-flex summary stripe">
    <div class="col-sm-3 d-sm-flex align-items-center">
        <figure class="summary">
            <?php echo $thumb; ?>
        </figure>
    </div>
    <div class="col-sm-9">
        <h3 class="summary"><?php the_title(); ?></h3>
        <div class="row summary-excerpt">
            <?php the_excerpt(); ?>
        </div>
        <div class="row justify-content-end summary-info">
            <p class="summary">投稿者：<object><?php the_author_posts_link(); ?></object></p>
            <p class="summary">投稿日：<?php echo $date; ?></p>
            <?php the_tags( '<p class="summary">タグ：<object>', '、', '</object></p>' ); ?>
        </div>
    </div>
</a>
