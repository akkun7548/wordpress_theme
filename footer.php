<?php
/**
 * このファイルは全ページテンプレートでget_footer()により読み込まれます。
 * wp_footer()ではjavascriptのファイルを読み込むタグなどが出力されます。
 */
?>
</main>
<footer class="footer">
    <div>
        <ul>
            <?php
            $args = array(
                'theme_location' => 'footer-nav',
                'container' => '',
                'items_wrap' => '%3$s'
            );
            wp_nav_menu( $args );
            ?>
        </ul>
    </div>
    <small>&copy; <?php echo date('Y') ?> 野生動物研究会</small>
</footer>
<?php wp_footer(); ?>
</body>
</html>