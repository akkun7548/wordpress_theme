<div class="row justify-content-end searchform stripe">
    <form role="sort" method="get" action="<?php echo strtok( get_the_permalink(), '?' ); ?>">
        <select name="orderby">
            <option value="post_date">投稿日</option>
            <option value="post_modified">更新日</option>
            <option value="post_title">タイトル</option>
        </select>
        <select name="order">
            <option value="DESC">降順</option>
            <option value="ASC">昇順</option>
        </select>
        <input type="submit" value="並べ替え">
    </form>
</div>
