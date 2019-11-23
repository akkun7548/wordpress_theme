<?php
$str = '';
$obj = get_post_type_object( get_post_type() );
if( $obj ) {
    $str = $obj->labels->name . '：';
} ?>
<a href="<?php the_permalink(); ?>" class="row title">
    <div class="col-12">
        <div class="row align-items-end title">
            <h2 class="mr-auto title"><?php echo esc_html( $str ); the_title(); ?></h2>
            <p class="title">掲載日: <?php the_time( 'Y年n月j日' ); ?></p>
            <P class="title">更新日: <?php the_modified_date( 'Y年n月j日' ); ?></P>
        </div>
    </div>
</a>
