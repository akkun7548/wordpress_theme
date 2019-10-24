<a href="<?php the_permalink(); ?>" class="row d-sm-flex summary stripe">
    <div class="col-sm-3 d-sm-flex align-items-center">
        <figure class="summary">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'thumbnail' ); ?>
        <?php else : ?>
            <img src="<?php yd_first_image(); //functions.php ?>" alt="<?php the_title(); ?>">
        <?php endif ; ?>
        </figure>
    </div>
    <div class="col-sm-9">
        <h3 class="summary"><?php the_title(); ?></h3>
        <div class="row summary-excerpt">
            <?php the_excerpt(); ?>
        </div>
        <div class="row justify-content-end summary-info">
            <p class="summary">投稿者：<object><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></object></p>
            <p class="summary">投稿日：<object><a href="<?php echo get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ); ?>"><?php the_time( 'n月' ); ?></a></object><?php the_time( 'j日' ); ?></p>
            <?php if( has_tag() ) : ?>
                <p class="summary">タグ：<?php the_tags('<object>','、','</object>'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</a>
