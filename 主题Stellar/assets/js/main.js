/* Nova Theme - 交互动效与增强 */
(function () {
    'use strict';

    var doc = document;
    var root = doc.documentElement;
    var $ = function (s, c) { return (c || doc).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || doc).querySelectorAll(s)); };

    var bodyLocked = false;
    /* 禁用浏览器自动滚动位置恢复：与 .reveal 入场动画位移冲突会导致刷新后页面自动往下跳一段 */
    if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }
    function lockBody(lock) {
        if (lock === bodyLocked) return;
        bodyLocked = lock;
        if (lock) {
            doc.body.style.overflow = 'hidden';
        } else {
            doc.body.style.overflow = '';
        }
    }

    /* 文章目录状态（提前声明：onScroll 初始化时就会访问） */
    var tocList = null;
    var tocLinks = [];
    var tocActiveIndex = -1;

    /* ---------- 暗色模式（圆形扩散动画） ---------- */
    var themeToggle = $('#theme-toggle');
    function setTheme(t, save) {
        root.setAttribute('data-theme', t);
        if (save !== false) {
            try { localStorage.setItem('nova-theme', t); } catch (e) {}
        }
    }
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            // View Transition：点击处圆形扩散揭示新主题（快照渲染，文字不消失）
            if (document.startViewTransition && window.innerWidth > 480) {
                var rect = themeToggle.getBoundingClientRect();
                var x = rect.left + rect.width / 2, y = rect.top + rect.height / 2;
                var r = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));
                var vt = document.startViewTransition(function () { setTheme(next, true); });
                vt.ready.then(function () {
                    document.documentElement.animate(
                        { clipPath: ['circle(0px at ' + x + 'px ' + y + 'px)', 'circle(' + r + 'px at ' + x + 'px ' + y + 'px)'] },
                        { duration: 650, easing: 'cubic-bezier(.4, 0, .2, 1)', pseudoElement: '::view-transition-new(root)' }
                    );
                });
            } else {
                setTheme(next, true);
            }
        });
    }

    /* ---------- 移动端抽屉菜单 ---------- */
    var menuBtn = $('#menu-toggle');
    var drawer = $('#drawer');
    var drawerBackdrop = $('#drawer-backdrop');
    var drawerClose = $('#drawer-close');
    function openDrawer() {
        if (!drawer) return;
        drawer.classList.add('open');
        drawer.setAttribute('aria-hidden', 'false');
        if (drawerBackdrop) drawerBackdrop.hidden = false;
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
        lockBody(true);
    }
    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('open');
        drawer.setAttribute('aria-hidden', 'true');
        if (drawerBackdrop) drawerBackdrop.hidden = true;
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
        lockBody(false);
    }
    if (menuBtn) menuBtn.addEventListener('click', openDrawer);
    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);
    $$('.drawer-nav a, .drawer-cats a').forEach(function (a) { a.addEventListener('click', closeDrawer); });

    /* ---------- 搜索面板 ---------- */
    var searchOpen = $('#search-open');
    var searchMask = $('#search-mask');
    var searchInput = $('#search-input');
    var searchClose = $('#search-close');
    function openSearch() {
        if (!searchMask) return;
        searchMask.hidden = false;
        lockBody(true);
        setTimeout(function () { if (searchInput) searchInput.focus(); }, 60);
    }
    function closeSearch() {
        if (!searchMask) return;
        searchMask.hidden = true;
        lockBody(false);
    }
    if (searchOpen) searchOpen.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);
    if (searchMask) searchMask.addEventListener('click', function (e) { if (e.target === searchMask) closeSearch(); });

    /* ---------- 全局键盘 ---------- */
    doc.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeSearch(); closeDrawer(); closeLightbox(); }
        // Ctrl/Cmd + K 打开搜索
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            if (searchMask && searchMask.hidden) { openSearch(); } else { closeSearch(); }
        }
    });

    /* ---------- 头部滚动状态 / 阅读进度 / 返回顶部 ---------- */
    var header = $('#site-header');
    var progress = $('#reading-progress');
    var backTop = $('#back-top');
    var ring = $('#back-top .ring-fg');
    var RING = 2 * Math.PI * 19;
    if (ring) ring.style.strokeDasharray = RING;
    var ticking = false;
    function onScroll() {
        var y = window.scrollY || doc.documentElement.scrollTop;
        var h = doc.documentElement.scrollHeight - window.innerHeight;
        var pct = h > 0 ? (y / h) * 100 : 0;
        if (header) header.classList.toggle('scrolled', y > 8);
        if (progress) {
            progress.style.width = pct + '%';
            progress.classList.toggle('show', y > 120 && pct < 99.5);
        }
        if (backTop) backTop.classList.toggle('show', y > 480);
        if (ring) ring.style.strokeDashoffset = RING * (1 - pct / 100);
        // 读完标记
        if (pct > 96) {
            var pill = $('#reading-pill');
            if (pill && pill.textContent.indexOf('✓') < 0) pill.textContent = '✓ 读完了';
        }
        updateTocActive();
        ticking = false;
    }
    window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();
    if (backTop) {
        backTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    var footerTop = $('#footer-top');
    if (footerTop) {
        footerTop.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---------- 滚动入场动画 ---------- */
    var reveals = $$('.reveal');
    if ('IntersectionObserver' in window && reveals.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    en.target.classList.add('in');
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -8% 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('in'); });
    }

    /* ---------- 图片加载失败 → 本地默认头像兜底 ---------- */
    doc.addEventListener('error', function (e) {
        var t = e.target;
        if (t && t.tagName === 'IMG') {
            var fb = t.getAttribute('data-fallback');
            if (fb && t.src !== fb) {
                t.src = fb;
                t.removeAttribute('data-fallback'); // 避免再次失败死循环
            }
        }
    }, true);

    /* ---------- 图片懒加载增强 ---------- */
    $$('.post-content img, .post-cover img').forEach(function (img) {
        if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
        if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
    });

    /* ---------- 表格移动端横向滚动 ---------- */
    $$('.post-content table').forEach(function (table) {
        if (table.parentElement && table.parentElement.classList.contains('table-wrap')) return;
        var wrap = doc.createElement('div');
        wrap.className = 'table-wrap';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    });

    /* ---------- 文章目录 TOC ---------- */
    function buildToc() {
        var article = $('.single-post .post-content');
        if (!article) return;
        var heads = $$('h2', article);
        if (heads.length < 2) return;

        var tocBox = doc.createElement('nav');
        tocBox.className = 'single-toc';
        tocBox.setAttribute('aria-label', '文章目录');
        var title = doc.createElement('div');
        title.className = 'toc-title';
        title.textContent = '目录';
        tocList = doc.createElement('ol');
        tocList.className = 'toc-list';

        heads.forEach(function (h, i) {
            if (!h.id) h.id = 'sec-' + (i + 1);
            var li = doc.createElement('li');
            var a = doc.createElement('a');
            a.href = '#' + h.id;
            a.textContent = h.textContent;
            a.addEventListener('click', function (e) {
                e.preventDefault();
                var top = h.getBoundingClientRect().top + window.scrollY - 90;
                window.scrollTo({ top: top, behavior: 'smooth' });
                history.replaceState(null, '', '#' + h.id);
            });
            li.appendChild(a);
            tocList.appendChild(li);
            tocLinks.push(a);
        });

        tocBox.appendChild(title);
        tocBox.appendChild(tocList);
        tocBox.className = 'toc-float';
        doc.body.appendChild(tocBox);
    }
    function updateTocActive() {
        if (!tocLinks || !tocLinks.length) return;
        var y = window.scrollY + 120;
        var current = -1;
        $$('.post-content h2').forEach(function (h, i) {
            if (h.getBoundingClientRect().top + window.scrollY <= y) current = i;
        });
        if (current === tocActiveIndex) return;
        tocActiveIndex = current;
        tocLinks.forEach(function (a, i) {
            a.classList.toggle('active', i === current);
        });
    }

    /* ---------- 轻量代码高亮 ---------- */
    var TOKENS = {
        kw: /\b(abstract|and|as|async|await|break|case|catch|class|const|continue|debugger|declare|default|delete|do|echo|else|elseif|endfor|endforeach|endif|enum|extends|false|final|finally|for|foreach|from|fn|function|global|goto|if|implements|import|in|instanceof|interface|let|match|namespace|new|null|of|or|private|protected|public|require|require_once|return|self|static|switch|throw|trait|true|try|typeof|use|var|void|while|xor|yield|this|parent)\b/,
        str: /("[^"\n]*"|'[^'\n]*'|`[^`\n]*`)/,
        num: /\b(0x[0-9a-fA-F]+|\d+\.?\d*)\b/,
        com: /(\/\*[\s\S]*?\*\/|\/\/[^\n]*|#[^\n]*|<!--[\s\S]*?-->)/,
        fn: /\b([a-zA-Z_$][\w$]*)(?=\s*\()/,
        prop: /\b([a-zA-Z_$][\w$]*)(?=\s*:)/,
        tag: /<\/?[a-zA-Z][^>]*>/
    };
    var TOKEN_ORDER = ['com', 'str', 'kw', 'num', 'fn', 'prop', 'tag'];
    function highlightBlock(block) {
        var text = block.textContent || '';
        if (!text.trim() || text.length > 20000) return;
        block.textContent = '';
        var safe = function (s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); };
        var out = [];
        var rest = text;
        var pos = 0;
        var guard = 0;
        while (pos < rest.length && guard++ < 20000) {
            var best = null;
            for (var i = 0; i < TOKEN_ORDER.length; i++) {
                var re = TOKENS[TOKEN_ORDER[i]];
                re.lastIndex = 0;
                var m = re.exec(rest.slice(pos));
                if (m && (best === null || m.index < best.index)) {
                    best = { type: TOKEN_ORDER[i], index: m.index, len: m[0].length };
                }
            }
            if (!best) break;
            if (best.index > 0) out.push(safe(rest.slice(pos, pos + best.index)));
            out.push('<span class="tok-' + best.type + '">' + safe(rest.slice(pos + best.index, pos + best.index + best.len)) + '</span>');
            pos += best.index + best.len;
            if (best.index === 0 && best.len === 0) break;
        }
        if (pos < rest.length) out.push(safe(rest.slice(pos)));
        block.innerHTML = out.join('');
    }
    $$('.post-content pre code').forEach(function (block) {
        var pre = block.closest('pre');
        if (pre) {
            var cls = block.className.match(/(?:language|lang)-([\w+-]+)/);
            if (cls) {
                var label = doc.createElement('span');
                label.className = 'language-label';
                label.textContent = cls[1];
                pre.appendChild(label);
            }
            // 代码复制按钮
            var copyBtn = doc.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'copy-code';
            copyBtn.setAttribute('aria-label', '复制代码');
            copyBtn.title = '复制代码';
            copyBtn.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>';
            copyBtn.addEventListener('click', function () {
                var text = block.textContent || '';
                var done = function () { showToast('代码已复制'); };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
                } else {
                    fallbackCopy(text); done();
                }
            });
            pre.appendChild(copyBtn);
        }
        highlightBlock(block);
    });

    /* ---------- 图片灯箱 ---------- */
    var lightbox = null;
    function closeLightbox() {
        if (lightbox) { lightbox.remove(); lightbox = null; lockBody(false); }
    }
    $$('.post-content img').forEach(function (img) {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function () {
            if (lightbox) closeLightbox();
            lightbox = doc.createElement('div');
            lightbox.className = 'img-lightbox';
            lightbox.innerHTML = '<img src="' + img.src + '" alt="">';
            lightbox.addEventListener('click', function (e) { if (e.target === lightbox) closeLightbox(); });
            doc.body.appendChild(lightbox);
            lockBody(true);
        });
    });

    /* ---------- 评论回复 ---------- */
    var parentInput = $('#comment-parent');
    var replyBtns = $$('.comment-reply-btn');
    replyBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var coid = btn.getAttribute('data-coid');
            if (parentInput) parentInput.value = coid;
            var respond = $('#respond');
            var textarea = $('#comment-text');
            var submit = $('.comment-form .btn-primary');
            if (respond) respond.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (textarea) {
                textarea.placeholder = '回复 #' + coid + ' 的评论…';
                setTimeout(function () { textarea.focus(); }, 400);
            }
            if (submit) submit.textContent = '提交回复';
            var cancel = $('.respond-cancel a');
            if (cancel && !cancel._bound) {
                cancel._bound = true;
                cancel.addEventListener('click', function () {
                    if (parentInput) parentInput.value = '0';
                    if (textarea) textarea.placeholder = '写下你的想法… Markdown 语法可用';
                    if (submit) submit.textContent = '提交评论';
                });
            }
        });
    });

    /* ---------- 复制链接 ---------- */
    var copyBtns = $$('.copy-link');
    copyBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var link = btn.getAttribute('data-link') || window.location.href;
            var done = function () { showToast('链接已复制'); };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link).then(done, function () { fallbackCopy(link); done(); });
            } else {
                fallbackCopy(link); done();
            }
        });
    });
    function fallbackCopy(text) {
        var ta = doc.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        doc.body.appendChild(ta); ta.select();
        try { doc.execCommand('copy'); } catch (e) {}
        doc.body.removeChild(ta);
    }

    /* ---------- Toast ---------- */
    var toastTimer = null;
    function showToast(msg) {
        var toast = $('#toast');
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 2200);
    }

    /* ---------- 互动：标题逐字浮现 ---------- */
    function splitChars(el) {
        if (!el || el.getAttribute('data-split')) return;
        var text = el.textContent || '';
        if (!text.trim() || text.length > 40) return;
        el.setAttribute('data-split', '1');
        el.classList.add('chars-split');
        el.textContent = '';
        for (var i = 0; i < text.length; i++) {
            var s = doc.createElement('span');
            s.className = 'char';
            s.style.animationDelay = (i * 0.04).toFixed(2) + 's';
            s.textContent = text[i] === ' ' ? '\u00A0' : text[i];
            el.appendChild(s);
        }
    }
    splitChars($('.hero-title'));
    splitChars($('.single-title'));

    /* ---------- 头像预加载：先显示本地默认头像，真实头像可用时平滑替换（永不破图） ---------- */
    $$('img[data-src]').forEach(function (img) {
        var real = img.getAttribute('data-src');
        var test = new Image();
        test.onload = function () { img.src = real; };
        // 加载失败保持本地默认头像，无操作
        test.src = real;
    });

    /* ---------- 互动：离开页面标签彩蛋 ---------- */
    var pageTitle = doc.title;
    doc.addEventListener('visibilitychange', function () {
        if (doc.hidden) {
            doc.title = '👋 别走呀，回来继续看～';
        } else {
            doc.title = pageTitle;
        }
    });

    /* ---------- 互动：评论提交反馈 ---------- */
    var cf = $('#comment-form');
    if (cf) {
        cf.addEventListener('submit', function () {
            var b = $('.comment-form .btn-primary', cf);
            if (b) { b.textContent = '提交中…'; b.disabled = true; }
        });
    }

    /* ---------- 互动：双击空白爆星星 ---------- */
    doc.addEventListener('dblclick', function (e) {
        var t = e.target;
        if (t.closest('.post-content, a, button, input, textarea, .single-toc, .toc-float')) return;
        for (var i = 0; i < 4; i++) {
            var s = doc.createElement('span');
            s.className = 'star-burst';
            s.textContent = '✦';
            s.style.left = (e.clientX + (Math.random() * 70 - 35)) + 'px';
            s.style.top = (e.clientY + (Math.random() * 70 - 35)) + 'px';
            s.style.animationDelay = (Math.random() * 0.15).toFixed(2) + 's';
            doc.body.appendChild(s);
            (function (el) { setTimeout(function () { el.remove(); }, 1000); })(s);
        }
    });

    /* ---------- 互动：选中文字浮出复制 ---------- */
    function removeBubble() { var b = $('#sel-bubble'); if (b) b.remove(); }
    doc.addEventListener('mouseup', function (e) {
        var b = $('#sel-bubble');
        /* 点击复制按钮本身时不重建（否则按钮被删、点击落空、复制失败） */
        if (b && b.contains(e.target)) return;
        var sel = window.getSelection();
        var text = sel ? sel.toString().trim() : '';
        removeBubble();
        if (text.length > 1 && sel.rangeCount && !sel.isCollapsed) {
            var rect = sel.getRangeAt(0).getBoundingClientRect();
            if (rect.width === 0 && rect.height === 0) return;
            var nb = doc.createElement('button');
            nb.type = 'button';
            nb.id = 'sel-bubble';
            nb.className = 'sel-bubble';
            nb.textContent = '复制';
            nb.style.left = (rect.left + rect.width / 2) + 'px';
            nb.style.top = (rect.top - 38) + 'px';
            /* 按下时保持文本选区（防止点击清除选区导致复制失败） */
            nb.addEventListener('mousedown', function (ev) { ev.preventDefault(); });
            nb.addEventListener('click', function () {
                var done = function () {
                    showToast('已复制选中内容');
                    removeBubble();
                    try { window.getSelection().removeAllRanges(); } catch (err) {}
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text); done(); });
                } else { fallbackCopy(text); done(); }
            });
            doc.body.appendChild(nb);
        }
    });
    doc.addEventListener('mousedown', function (e) {
        var b = $('#sel-bubble');
        if (b && !b.contains(e.target)) removeBubble();
    });
    doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') removeBubble(); });

    /* ---------- 3D 视差卡片（tilt）：鼠标悬停时轻微倾斜 + 高光跟随 ---------- */
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches
        && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        $$('.post-card').forEach(function (card) {
            card.classList.add('tiltable');
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width - .5;
                var py = (e.clientY - r.top) / r.height - .5;
                card.style.setProperty('--rx', (-py * 5).toFixed(2) + 'deg');
                card.style.setProperty('--ry', (px * 7).toFixed(2) + 'deg');
                card.style.setProperty('--gx', ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%');
                card.style.setProperty('--gy', ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%');
            });
            card.addEventListener('mouseleave', function () {
                card.style.setProperty('--rx', '0deg');
                card.style.setProperty('--ry', '0deg');
            });
        });
    }

    buildToc();
})();
