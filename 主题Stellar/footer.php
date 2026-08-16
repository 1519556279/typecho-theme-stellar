<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <p class="footer-copy">
                © <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
                <span class="footer-sep">·</span>
                Powered by <a href="https://typecho.org" target="_blank" rel="noopener">Typecho</a> · Theme <strong>Stellar</strong>
            </p>
            <a class="footer-top-link" href="#" id="footer-top">
                回到顶部
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5m-6 6l6-6 6 6"/></svg>
            </a>
        </div>
        <p class="footer-sub"><?php $this->options->description(); ?></p>
    </div>
</footer>

<button class="back-top" id="back-top" type="button" aria-label="返回顶部" title="返回顶部">
    <svg class="ring" viewBox="0 0 44 44" width="44" height="44" aria-hidden="true">
        <circle class="ring-bg" cx="22" cy="22" r="19" fill="none" stroke-width="3"/>
        <circle class="ring-fg" cx="22" cy="22" r="19" fill="none" stroke-width="3"/>
    </svg>
    <svg class="arrow" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5m-6 6l6-6 6 6"/></svg>
</button>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<?php $this->footer(); ?>
<script src="<?php $this->options->themeUrl('assets/js/main.js?v=1.1.2'); ?>"></script>
</body>
</html>
