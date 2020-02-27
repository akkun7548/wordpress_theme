<?php
/**
 * 投稿をリスト形式で出力する時にlistを指定した場合、このファイルを用いて出力されます。
 * 現段階では仮置きのような状態です。
 */
?>
<a href="<?php the_permalink(); ?>" class="row list">
    <h2><?php the_title(); ?></h2>
</a>
