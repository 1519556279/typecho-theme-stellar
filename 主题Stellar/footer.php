<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <p class="footer-copy">
                © <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
                <span class="footer-sep">·</span>
                Powered by <a href="https://typecho.org" target="_blank" rel="noopener">Typecho</a> · Theme <strong>Stellar</strong>
                <span class="footer-sep">·</span>
                By <a href="https://github.com/1519556279" target="_blank" rel="noopener" title="作者咔咔的 GitHub 主页">
                    <svg viewBox="0 0 16 16" width="13" height="13" fill="currentColor" aria-hidden="true" style="vertical-align:-2px"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/></svg>
                    咔咔
                </a>
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
<script src="<?php $this->options->themeUrl('assets/js/main.js'); ?>?v=20260829"></script>
<script>
/* 联系方式悬停提示：显示具体账号/地址（邮箱显示完整邮箱，社交链接显示主页地址） */
(function () {
    var setTip = function (a, text) {
        if (!text) return;
        a.setAttribute('data-mail', text);
        a.setAttribute('aria-label', text);
        a.removeAttribute('title'); /* 去掉原生 title，避免两个气泡重叠 */
        /* 按钮靠近视口顶部时气泡显示在下方（否则会被视口裁掉） */
        a.addEventListener('mouseenter', function () {
            var r = a.getBoundingClientRect();
            a.classList.toggle('mail-tip-down', r.top < 90);
        });
    };
    /* 所有 mailto 链接（评论作者邮箱、侧边栏邮箱等） */
    var links = document.querySelectorAll('a[href^="mailto:"]');
    for (var i = 0; i < links.length; i++) {
        var mail = decodeURIComponent(links[i].getAttribute('href').slice(7) || '');
        setTip(links[i], mail);
    }
    /* 侧边栏社交链接（GitHub / 微博 / X 等非邮箱）：显示主页地址；微信号点击复制 */
    var socials = document.querySelectorAll('.widget-social a');
    for (var j = 0; j < socials.length; j++) {
        var href = socials[j].getAttribute('href') || '';
        if (href.indexOf('mailto:') === 0) continue; /* 上面已处理 */
        var copyVal = socials[j].getAttribute('data-copy');
        if (copyVal) {
            socials[j].addEventListener('click', function (e) {
                e.preventDefault();
                var v = this.getAttribute('data-copy');
                var t = document.getElementById('toast');
                if (t) { t.textContent = '微信号已复制'; t.classList.add('show'); setTimeout(function () { t.classList.remove('show'); }, 2200); }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(v);
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = v; ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta); ta.select();
                    try { document.execCommand('copy'); } catch (err) {}
                    document.body.removeChild(ta);
                }
            });
            setTip(socials[j], copyVal);
            continue;
        }
        setTip(socials[j], href.replace(/^https?:\/\//, ''));
    }
})();
</script>
</body>
</html>
