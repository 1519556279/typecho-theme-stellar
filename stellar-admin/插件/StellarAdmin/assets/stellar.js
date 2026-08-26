/* Stellar Admin —— 新框架交互：主题/折叠/用户菜单/批量操作/登录页装饰 */
(function () {
    'use strict';
    var doc = document, root = doc.documentElement;
    var saFlags = window.__saFlags || { ui: true, ai: true }; /* 插件配置开关：美化 / AI 助手 */
    var KEY_T = 'stellar-admin-theme', KEY_A = 'stellar-admin-accent', KEY_C = 'stellar-admin-collapsed';
    var ACCENTS = [
        ['default', '#5b5bd6'], ['blue', '#2563eb'], ['emerald', '#0d9488'],
        ['rose', '#e11d48'], ['amber', '#d97706'], ['violet', '#7c3aed']
    ];
    var ICONS = {
        sun: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>',
        moon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>'
    };

    /* ---------- 初始化 ---------- */
    var savedT = null, savedA = null, savedC = null;
    try {
        savedT = localStorage.getItem(KEY_T);
        savedA = localStorage.getItem(KEY_A);
        savedC = localStorage.getItem(KEY_C);
    } catch (e) {}
    var theme = savedT || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    root.setAttribute('data-theme', theme);
    root.setAttribute('data-accent', savedA || 'default');

    function getTheme() { return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'; }
    function isDesktop() { return window.innerWidth >= 900; }

    function setTheme(t, save) {
        root.setAttribute('data-theme', t);
        if (save !== false) { try { localStorage.setItem(KEY_T, t); } catch (e) {} }
        updateBtns();
    }
    function setAccent(a) {
        root.setAttribute('data-accent', a);
        try { localStorage.setItem(KEY_A, a); } catch (e) {}
        updateDots();
    }

    /* 点击处圆形扩散切换主题 */
    function toggleTheme(btn) {
        var next = getTheme() === 'dark' ? 'light' : 'dark';
        if (doc.startViewTransition && isDesktop()) {
            var rect = btn.getBoundingClientRect();
            var x = rect.left + rect.width / 2, y = rect.top + rect.height / 2;
            var r = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));
            var vt = doc.startViewTransition(function () { setTheme(next, true); });
            vt.ready.then(function () {
                root.animate(
                    { clipPath: ['circle(0px at ' + x + 'px ' + y + 'px)', 'circle(' + r + 'px at ' + x + 'px ' + y + 'px)'] },
                    { duration: 650, easing: 'cubic-bezier(.4, 0, .2, 1)', pseudoElement: '::view-transition-new(root)' }
                );
            });
        } else {
            setTheme(next, true);
        }
    }

    /* ---------- 主题面板（挂 body，fixed 定位，避免 overflow 裁剪） ---------- */
    var panel = null;
    function buildPanel() {
        if (panel) return panel;
        panel = doc.createElement('div');
        panel.className = 'sa-theme-panel';
        panel.style.display = 'none';
        panel.innerHTML =
            '<div class="sa-row"><span class="sa-row-title">暗色模式</span><button type="button" class="sa-switch" title="切换暗色模式"></button></div>' +
            '<div class="sa-row"><span class="sa-row-title">主题配色</span><span class="sa-accent-row">' +
            ACCENTS.map(function (a) { return '<button type="button" class="sa-accent" data-a="' + a[0] + '" style="background:' + a[1] + '" title="' + a[0] + '"></button>'; }).join('') +
            '</span></div>';
        panel.addEventListener('click', function (e) {
            var sw = e.target.closest('.sa-switch');
            if (sw) { toggleTheme(sw); return; }
            var dot = e.target.closest('.sa-accent');
            if (dot) { setAccent(dot.getAttribute('data-a')); }
        });
        doc.body.appendChild(panel);
        return panel;
    }
    function showPanel(btn) {
        buildPanel();
        var r = btn.getBoundingClientRect();
        panel.style.position = 'fixed';
        panel.style.top = (r.bottom + 8) + 'px';
        panel.style.right = (window.innerWidth - r.right) + 'px';
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }
    function updateBtns() {
        var dark = getTheme() === 'dark';
        doc.querySelectorAll('.sa-theme-btn').forEach(function (b) { b.innerHTML = dark ? ICONS.sun : ICONS.moon; });
        doc.querySelectorAll('.sa-switch').forEach(function (s) { s.setAttribute('data-on', dark ? '1' : '0'); });
    }
    function updateDots() {
        var cur = root.getAttribute('data-accent');
        doc.querySelectorAll('.sa-accent').forEach(function (d) { d.setAttribute('data-on', d.getAttribute('data-a') === cur ? '1' : '0'); });
    }
    function bindThemeButtons() {
        doc.querySelectorAll('.sa-theme-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                showPanel(btn);
            });
        });
        doc.addEventListener('click', function (e) {
            if (panel && panel.style.display !== 'none' && !e.target.closest('.sa-theme-panel') && !e.target.closest('.sa-theme-btn')) {
                panel.style.display = 'none';
            }
        });
        updateBtns();
        updateDots();
    }

    /* ---------- 侧边栏：图标 / accordion / 折叠 ---------- */
    var NAV_ICONS = {
        'write-post': 'edit', 'write-page': 'file-plus',
        'manage-posts': 'file-text', 'manage-pages': 'files',
        'manage-comments': 'message', 'manage-categories': 'folder',
        'manage-tags': 'tag', 'manage-medias': 'image', 'manage-users': 'users',
        'options-general': 'sliders', 'options-discussion': 'chat', 'options-reading': 'book',
        'options-permalink': 'link', 'options-theme': 'palette', 'plugins': 'box',
        'themes': 'palette', 'backup': 'archive', 'upgrade': 'refresh', 'welcome': 'sparkle',
        'category': 'folder', 'user': 'user-plus', 'media': 'image'
    };
    var ICON_SVG = {
        edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'file-plus': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M12 18v-6"/><path d="M9 15h6"/>',
        'file-text': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/>',
        files: '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v5h5"/><path d="M9 13h6"/><path d="M9 17h4"/>',
        message: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        chat: '<path d="M8 10h8"/><path d="M8 14h5"/><path d="M21 12a8 8 0 0 1-8 8H6l-3 2v-5.3A8 8 0 1 1 21 12Z"/>',
        folder: '<path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/>',
        tag: '<path d="M12.6 2.6 20 10l-8 8-7.4-7.4a2 2 0 0 1-.6-1.4V4a2 2 0 0 1 2-2h5.2a2 2 0 0 1 1.4.6Z"/><circle cx="7" cy="7" r="1.5"/>',
        image: '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/>',
        users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user-plus': '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/>',
        sliders: '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
        book: '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>',
        link: '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
        palette: '<circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.66 1.5-1.5 0-.42-.17-.78-.44-1.05-.27-.27-.56-.6-.56-1.2 0-.93.66-1.5 1.5-1.5H16a6 6 0 0 0 6-6c0-4.5-4.22-8-10-8Z"/>',
        archive: '<rect x="2" y="3" width="20" height="5" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>',
        refresh: '<path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/>',
        sparkle: '<path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3Z"/>',
        box: '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        grid: '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        layers: '<path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/>',
        settings: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.7 1.7 0 0 0 .34-1.87 1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.7 1.7 0 0 0 1.87.34h0a1.7 1.7 0 0 0 1-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55h0a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.7 1.7 0 0 0-.34 1.87v0a1.7 1.7 0 0 0 1.55 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.55 1Z"/>'
    };
    function makeIcon(name) {
        var s = doc.createElement('span');
        s.className = 'sa-nav-icon';
        s.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'
            + (ICON_SVG[name] || ICON_SVG.sparkle) + '</svg>';
        return s;
    }
    function setupSidebar() {
        var nav = doc.querySelector('.sa-nav');
        if (!nav) return;
        var parents = doc.querySelectorAll('.sa-nav > li');
        parents.forEach(function (li) {
            var a = li.querySelector(':scope > a');
            var sub = li.querySelector(':scope > menu');
            if (!a || !sub) return;
            a.classList.add('sa-parent');
            a.setAttribute('data-title', a.textContent.trim());
            if (li.querySelector('menu li.sa-current')) li.classList.add('sa-open');
            a.addEventListener('click', function (e) {
                /* Typecho 默认行为：父级点击仅展开/收起子菜单，不跳转；子项点击才跳转 */
                e.preventDefault();
                if (doc.querySelector('.sa-app') && doc.querySelector('.sa-app').classList.contains('sa-collapsed') && isDesktop()) {
                    /* 折叠态点击：先展开侧边栏 */
                    toggleCollapse(false);
                    return;
                }
                if (li.classList.contains('sa-open')) {
                    li.classList.remove('sa-open');
                } else {
                    parents.forEach(function (o) { o.classList.remove('sa-open'); });
                    li.classList.add('sa-open');
                }
            });
            if (!a.querySelector('.sa-nav-icon')) {
                var t = a.textContent.trim();
                var icon = t.indexOf('控制台') === 0 ? 'grid'
                    : t.indexOf('撰写') === 0 ? 'edit'
                        : t.indexOf('管理') === 0 ? 'layers'
                            : t.indexOf('设置') === 0 ? 'settings' : null;
                if (icon) a.insertBefore(makeIcon(icon), a.firstChild);
            }
        });
        doc.querySelectorAll('.sa-nav > li menu > li a').forEach(function (a) {
            if (a.querySelector('.sa-nav-icon')) return;
            var m = (a.getAttribute('href') || '').match(/([a-z-]+)\.php/);
            a.insertBefore(makeIcon(m && NAV_ICONS[m[1]] ? NAV_ICONS[m[1]] : 'sparkle'), a.firstChild);
        });
        if (!nav.querySelector('li.sa-current, li menu li.sa-current') && isDesktop()) {
            var first = nav.querySelector('li');
            if (first) first.classList.add('sa-open');
        }
        /* AI 页面修复：Typecho 会把"概要"误判为当前菜单项，并将其 href 覆盖为当前页（ai-console.php）
           导致点击"概要"不跳转——还原为真实的概要页 index.php */
        if (/ai-console\.php/.test(location.pathname)) {
            doc.querySelectorAll('.sa-nav > li > menu li.sa-current > a').forEach(function (a) {
                if (/ai-console\.php/.test(a.getAttribute('href') || '')) {
                    a.setAttribute('href', a.getAttribute('href').replace(/ai-console\.php.*$/, 'index.php'));
                    var li = a.closest('li');
                    if (li) li.classList.remove('sa-current');
                }
            });
        }
    }

    /* ---------- 折叠 / 抽屉 ---------- */
    function toggleCollapse(force) {
        var app = doc.querySelector('.sa-app');
        if (!app) return;
        var open;
        if (isDesktop()) {
            open = force !== undefined ? force : !app.classList.contains('sa-collapsed');
            app.classList.toggle('sa-collapsed', open);
            try { localStorage.setItem(KEY_C, open ? '1' : '0'); } catch (e) {}
        } else {
            open = force !== undefined ? force : !app.classList.contains('sa-nav-open');
            app.classList.toggle('sa-nav-open', open);
        }
    }
    function bindCollapse() {
        doc.querySelectorAll('.sa-sidebar-toggle, .sa-topbar-toggle').forEach(function (b) {
            b.addEventListener('click', function () { toggleCollapse(); });
        });
        var app = doc.querySelector('.sa-app');
        var forceC = (location.search.match(/[?&]sa_collapsed=1/) !== null);
        if (app && isDesktop() && (savedC === '1' || forceC)) app.classList.add('sa-collapsed');
        /* 抽屉打开时点遮罩关闭 */
        doc.addEventListener('click', function (e) {
            var app = doc.querySelector('.sa-app');
            if (app && app.classList.contains('sa-nav-open') && !e.target.closest('.sa-sidebar') && !e.target.closest('.sa-topbar-toggle')) {
                app.classList.remove('sa-nav-open');
            }
        });
    }

    /* ---------- 用户菜单 ---------- */
    function bindUserMenu() {
        var chip = doc.getElementById('sa-user-chip');
        var menu = doc.getElementById('sa-user-menu');
        if (!chip || !menu) return;
        chip.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('open');
        });
        doc.addEventListener('click', function (e) {
            if (!chip.contains(e.target) && !menu.contains(e.target)) menu.classList.remove('open');
        });
    }

    /* ---------- 批量操作 / 单行删除 / 全选 ---------- */
    function getCheckedCids() {
        var cids = [];
        doc.querySelectorAll('form[name="manage_posts"] input[name="cid[]"]:checked').forEach(function (i) { cids.push(i.value); });
        return cids;
    }
    function submitCids(url, cids, msg) {
        if (!cids.length) {
            toast('请先选择要操作的文章', 'error');
            return;
        }
        if (msg && !confirm(msg)) return;
        var f = doc.createElement('form');
        f.method = 'post';
        f.action = url;
        f.style.display = 'none';
        cids.forEach(function (cid) {
            var i = doc.createElement('input');
            i.type = 'hidden';
            i.name = 'cid[]';
            i.value = cid;
            f.appendChild(i);
        });
        doc.body.appendChild(f);
        f.submit();
    }
    function bindBatch() {
        doc.querySelectorAll('.sa-batch-op').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                submitCids(link.href, getCheckedCids(), link.getAttribute('lang') || null);
            });
        });
        doc.querySelectorAll('.sa-act-del').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var delUrl = doc.querySelector('.sa-batch-op.danger');
                submitCids(delUrl ? delUrl.href : '',
                    [btn.getAttribute('data-cid')], '你确认要删除这篇文章吗?');
            });
        });
        doc.querySelectorAll('.sa-check-all input[type="checkbox"]').forEach(function (all) {
            all.addEventListener('change', function () {
                doc.querySelectorAll('form[name="manage_posts"] input[name="cid[]"]').forEach(function (i) { i.checked = all.checked; });
            });
        });
    }

    /* ---------- Toast ---------- */
    function toast(msg, type) {
        var t = doc.createElement('div');
        t.className = 'message popup ' + (type || 'notice');
        t.innerHTML = '<ul><li>' + msg + '</li></ul>';
        doc.body.appendChild(t);
        setTimeout(function () {
            t.style.opacity = '0';
            t.style.transition = 'opacity .3s';
            setTimeout(function () { t.remove(); }, 320);
        }, 2600);
    }

    /* ---------- 模态框 ---------- */
    var modal = null;
    function getModal() {
        if (modal) return modal;
        modal = doc.createElement('div');
        modal.className = 'sa-modal';
        modal.hidden = true;
        modal.innerHTML =
            '<div class="sa-modal-card">' +
            '<div class="sa-modal-head"><h3 id="sa-modal-title"></h3>' +
            '<button type="button" class="sa-modal-close" title="关闭">×</button></div>' +
            '<div class="sa-modal-body" id="sa-modal-body"></div></div>';
        modal.addEventListener('click', function (e) {
            if (e.target === modal || e.target.closest('.sa-modal-close')) closeModal();
        });
        doc.body.appendChild(modal);
        return modal;
    }
    function openModal(title, html) {
        var m = getModal();
        m.querySelector('#sa-modal-title').textContent = title;
        m.querySelector('#sa-modal-body').innerHTML = html;
        m.hidden = false;
        doc.body.style.overflow = 'hidden';
    }
    function closeModal() {
        if (!modal) return;
        modal.hidden = true;
        doc.body.style.overflow = '';
    }

    /* ---------- 全局帮助（按页面） ---------- */
    var HELP = {
        'write-post.php': '<div class="sa-help-sec"><h4>✍️ 发布文章流程</h4><ol>' +
            '<li>填写<b>标题</b>（建议 10–30 字，含关键词）</li>' +
            '<li>正文用 <b>Markdown</b> 编写（点编辑器上方「Markdown 语法」查看符号规则）</li>' +
            '<li>右侧面板设置<b>分类 / 标签</b>，标签用逗号分隔多个</li>' +
            '<li>插入 <code>&lt;!--more--&gt;</code> 标记可控制首页摘要截断位置</li>' +
            '<li>点「预览文章」检查效果 →「保存草稿」或「发布文章」</li></ol></div>' +
            '<div class="sa-help-sec"><h4>🤖 AI 润色</h4><p>选中正文片段或直接点「AI 润色」，AI 会优化表达并保持 Markdown 结构。需先在 插件 → StellarAdmin → 设置 中配置 API Key。</p></div>' +
            '<div class="sa-help-sec"><h4>💡 提示</h4><p>正文以一级标题开头更利于阅读；段落之间空一行；首次发布建议先用「保存草稿」。</p></div>',
        'manage-posts.php': '<div class="sa-help-sec"><h4>📋 文章管理</h4><ol>' +
            '<li><b>筛选</b>：顶部标签切换 可用/待审核/草稿，右侧可搜索关键字、按分类筛选</li>' +
            '<li><b>批量操作</b>：勾选文章（或全选）后，点底部「删除 / 标记为…」</li>' +
            '<li><b>单行操作</b>：每行右侧铅笔=编辑、眼睛=查看、垃圾桶=删除（需确认）</li></ol></div>' +
            '<div class="sa-help-sec"><h4>状态说明</h4><p><span class="sa-badge waiting">待审核</span> 等待管理员审核；<span class="sa-badge hidden">隐藏</span> 前台不可见；<span class="sa-badge private">私密</span> 仅登录用户可见。</p></div>',
        'options-general.php': '<div class="sa-help-sec"><h4>⚙️ 基本设置</h4><ul>' +
            '<li><b>站点名称</b>：显示在浏览器标题与主题品牌区</li>' +
            '<li><b>站点地址</b>：用于生成文章永久链接，改后需同步更新伪静态规则</li>' +
            '<li><b>站点描述 / 关键词</b>：用于 SEO，被搜索引擎收录时展示</li></ul></div>',
        'options-reading.php': '<div class="sa-help-sec"><h4>📖 阅读设置</h4><p>控制首页与列表页显示文章数量、Feed 输出内容等。</p></div>',
        'options-discussion.php': '<div class="sa-help-sec"><h4>💬 评论设置</h4><p>可开启评论审核、限制评论间隔；「垃圾评论保护」建议保持开启。</p></div>',
        'options-permalink.php': '<div class="sa-help-sec"><h4>🔗 永久链接</h4><p>自定义文章 URL 格式，可用变量：<code>{cid}</code> 文章ID、<code>{slug}</code> 缩略名、<code>{year}/{month}/{day}</code> 日期、<code>{category}</code> 分类。修改后需同步服务器伪静态规则（Nginx/Apache）。</p></div>',
        'options-theme.php': '<div class="sa-help-sec"><h4>🎨 设置外观</h4><p>当前主题 Stellar 的配置：主题配色、博主头像、站点简介、友情链接（每行 名称|链接|简介，# 开头为注释）。</p></div>',
        'themes.php': '<div class="sa-help-sec"><h4>🎭 外观管理</h4><p>点击「启用」切换主题；「编辑」可修改主题文件；当前启用主题标记为高亮。</p></div>',
        'plugins.php': '<div class="sa-help-sec"><h4>🧩 插件管理</h4><p>StellarAdmin 插件提供后台美化与 AI 功能，点「设置」配置 AI 服务商与 API Key。</p></div>',
        'manage-comments.php': '<div class="sa-help-sec"><h4>💬 评论管理</h4><p>待审核评论需要批准后展示；垃圾评论可直接删除。评论者头像：QQ 邮箱自动显示 QQ 头像。</p></div>',
        'profile.php': '<div class="sa-help-sec"><h4>👤 个人资料</h4><p>修改昵称、邮箱与密码；「我的头像」用于后台侧边栏与评论博主标识。</p></div>',
        'backup.php': '<div class="sa-help-sec"><h4>💾 备份</h4><p>可导出数据库与文件备份；建议定期备份并在升级前操作。</p></div>',
        'welcome.php': '<div class="sa-help-sec"><h4>👋 欢迎页</h4><p>查看站点概览、最近文章与评论。快速开始：撰写新文章 → 更换外观 → 系统设置。</p></div>',
        'default': '<div class="sa-help-sec"><h4>✨ Stellar Admin 帮助</h4><ul>' +
            '<li>左侧<b>侧边栏</b>：点击折叠按钮可收起为图标栏</li>' +
            '<li>右上角<b>主题按钮</b>：切换暗色模式与 6 套配色</li>' +
            '<li><b>命令面板</b>（&lt;/&gt; 按钮）：用自然语言发布文章、查看统计、修改站点信息，如「发布一篇标题为你好世界的文章」</li>' +
            '<li>写文章时点「Markdown 语法」查看符号规则</li></ul></div>'
    };
    function bindHelp() {
        var btn = doc.getElementById('sa-help-btn');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var page = location.pathname.split('/').pop();
            openModal('帮助 · ' + (HELP[page] ? page.replace('.php', '') : 'Stellar Admin'),
                HELP[page] || HELP['default']);
        });
    }

    /* ---------- 编辑器工具（Markdown 引导插入 / AI 润色 / 智能优化 / 撤回重做） ---------- */
    function aiUrl() {
        return new URL('../../usr/plugins/StellarAdmin/ai.php', location.href).href;
    }    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function bindEditorTools() {
        var mdToggle = doc.getElementById('sa-md-toggle');
        var guide = doc.getElementById('sa-md-guide');
        var undoBtn = doc.getElementById('sa-undo-btn');
        var redoBtn = doc.getElementById('sa-redo-btn');
        var undoStack = [], redoStack = [];

        /* 内容快照入栈 */
        function pushUndo(ta) {
            undoStack.push({ text: ta.value, start: ta.selectionStart, end: ta.selectionEnd });
            if (undoStack.length > 5) undoStack.shift();
            redoStack = [];
            if (undoBtn) undoBtn.hidden = false;
            if (redoBtn) redoBtn.hidden = true;
        }
        function applySnapshot(ta, item) {
            ta.value = item.text;
            ta.setSelectionRange(item.start, item.end);
            ta.dispatchEvent(new Event('input'));
        }

        /* 撤回 / 重做 */
        if (undoBtn) {
            undoBtn.addEventListener('click', function () {
                var ta = doc.getElementById('text');
                if (!ta || !undoStack.length) return;
                var cur = { text: ta.value, start: ta.selectionStart, end: ta.selectionEnd };
                redoStack.push(cur);
                applySnapshot(ta, undoStack.pop());
                undoBtn.hidden = !undoStack.length;
                if (redoBtn) redoBtn.hidden = false;
                toast('已撤回 ↩', 'success');
            });
        }
        if (redoBtn) {
            redoBtn.addEventListener('click', function () {
                var ta = doc.getElementById('text');
                if (!ta || !redoStack.length) return;
                var cur = { text: ta.value, start: ta.selectionStart, end: ta.selectionEnd };
                undoStack.push(cur);
                applySnapshot(ta, redoStack.pop());
                redoBtn.hidden = !redoStack.length;
                if (undoBtn) undoBtn.hidden = false;
                toast('已重做 ↪', 'success');
            });
        }

        /* Markdown 语法面板展开 */
        if (mdToggle && guide) {
            mdToggle.addEventListener('click', function () {
                var show = guide.hidden;
                guide.hidden = !show;
                mdToggle.setAttribute('data-on', show ? '1' : '0');
            });
        }

        /* 语法点击插入到光标处（{{选中}} 标记要选中的部分） */
        if (guide) {
            guide.addEventListener('click', function (e) {
                var code = e.target.closest('code[data-insert]');
                if (!code) return;
                var ta = doc.getElementById('text');
                if (!ta) return;
                var tpl = code.getAttribute('data-insert');
                var selMark = '{{', selEnd = '}}';
                var ms = tpl.indexOf(selMark), me = tpl.indexOf(selEnd);
                var insert = tpl, selStart = -1, selLen = 0;
                if (ms >= 0 && me > ms) {
                    insert = tpl.slice(0, ms) + tpl.slice(ms + 2, me) + tpl.slice(me + 2);
                    selStart = ms;
                    selLen = me - ms - 2;
                }
                var start = ta.selectionStart, end = ta.selectionEnd;
                pushUndo(ta);
                ta.value = ta.value.slice(0, start) + insert + ta.value.slice(end);
                var pos = selStart >= 0 ? start + selStart : start + insert.length;
                ta.setSelectionRange(pos, pos + (selStart >= 0 ? selLen : 0));
                ta.focus();
                ta.dispatchEvent(new Event('input'));
                toast('已插入「' + code.textContent.trim().slice(0, 12) + '…」', 'success');
            });
        }

        /* AI 润色 / 智能优化 共用逻辑 */
        function aiPolish(mode, btn, label) {
            var ta = doc.getElementById('text');
            if (!ta) return;
            var sel = ta.value.substring(ta.selectionStart, ta.selectionEnd);
            var text = sel.trim() || ta.value.trim();
            if (!text) { toast('编辑器里还没有内容', 'error'); return; }
            btn.classList.add('sa-busy');
            var status = doc.getElementById('sa-ai-status');
            if (status) status.textContent = label + '中，请稍候…';
            aiFetch({ action: 'polish', text: text, mode: mode }).then(function (d) {
                btn.classList.remove('sa-busy');
                if (!d.ok) { if (status) status.textContent = ''; toast(d.error || '处理失败', 'error'); return; }
                if (status) status.textContent = '';
                if (confirm(label + '完成，是否替换当前内容？')) {
                    var start = ta.selectionStart, end = ta.selectionEnd;
                    pushUndo(ta);
                    if (sel.trim()) {
                        ta.value = ta.value.substring(0, start) + d.text + ta.value.substring(end);
                        ta.setSelectionRange(start, start + d.text.length);
                    } else {
                        ta.value = d.text;
                    }
                    ta.dispatchEvent(new Event('input'));
                    toast(label + '完成 ✓（可撤回/重做）', 'success');
                }
            }).catch(function (e) {
                btn.classList.remove('sa-busy');
                if (status) status.textContent = '';
                toast(e && e.message ? e.message : '网络请求失败', 'error');
            });
        }
        var aiBtn = doc.getElementById('sa-ai-btn');
        if (aiBtn) {
            if (!saFlags.ai) { aiBtn.hidden = true; }
            else { aiBtn.addEventListener('click', function () { aiPolish('通用', aiBtn, 'AI 润色'); }); }
        }
        var autoBtn = doc.getElementById('sa-auto-btn');
        if (autoBtn) {
            if (!saFlags.ai) { autoBtn.hidden = true; }
            else { autoBtn.addEventListener('click', function () { aiPolish('auto', autoBtn, '智能优化'); }); }
        }
    }

    /* ---------- 命令面板（宝塔式 API 调用） ---------- */
    function bindCmd() {
        var btn = doc.getElementById('sa-cmd-btn');
        if (!btn || btn.dataset.saBound) return;
        btn.dataset.saBound = '1';
        btn.addEventListener('click', function () {
            var html =
                '<p class="sa-cmd-desc">用自然语言下达指令，AI 会解析并执行（如发布文章、查看统计、修改站点信息）。</p>' +
                '<div class="sa-cmd-examples">' +
                '<button type="button" class="sa-cmd-chip">发布一篇标题为「你好世界」的文章，内容写一段自我介绍</button>' +
                '<button type="button" class="sa-cmd-chip">查看最近的 5 篇文章</button>' +
                '<button type="button" class="sa-cmd-chip">查看站点统计</button>' +
                '<button type="button" class="sa-cmd-chip">把站点标题改为「我的博客」</button>' +
                '</div>' +
                '<textarea id="sa-cmd-input" rows="3" placeholder="例如：发布一篇关于 Typecho 的文章，内容用 Markdown 写三段…"></textarea>' +
                '<div class="sa-cmd-actions"><button type="button" class="btn btn-primary" id="sa-cmd-send">执行</button></div>' +
                '<div class="sa-cmd-result" id="sa-cmd-result"></div>';
            openModal('命令面板', html);
            var input = doc.getElementById('sa-cmd-input');
            var send = doc.getElementById('sa-cmd-send');
            var result = doc.getElementById('sa-cmd-result');
            function run() {
                var cmd = input.value.trim();
                if (!cmd) { result.innerHTML = '<p class="sa-cmd-err">请输入指令</p>'; return; }
                send.disabled = true;
                send.textContent = '执行中…';
                result.innerHTML = '<p class="sa-cmd-wait">AI 解析中，请稍候…</p>';
                fetch(aiUrl(), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'command', command: cmd })
                }).then(function (r) { return r.json(); }).then(function (d) {
                    send.disabled = false;
                    send.textContent = '执行';
                    if (!d.ok) { result.innerHTML = '<p class="sa-cmd-err">' + escapeHtml(d.error || '执行失败') + '</p>'; return; }
                    if (d.note) { result.innerHTML = '<p class="sa-cmd-note">' + escapeHtml(d.note) + '</p>'; return; }
                    var html = '';
                    (d.results || []).forEach(function (r) {
                        html += '<div class="sa-cmd-item ' + (r.ok ? 'ok' : 'err') + '">' +
                            '<span class="sa-cmd-icon">' + (r.ok ? '✓' : '✗') + '</span>' +
                            '<div><div class="sa-cmd-title">' + escapeHtml(r.action || '') + '</div>' +
                            '<div class="sa-cmd-detail">' + escapeHtml(r.detail || '') + '</div></div></div>';
                        if (r.list) {
                            html += '<ul class="sa-cmd-list">' + r.list.map(function (p) {
                                return '<li><b>#' + escapeHtml(p.cid) + '</b> ' + escapeHtml(p.title) + ' <span class="sa-cmd-st">' + escapeHtml(p.status) + '</span> ' + escapeHtml(p.created) + '</li>';
                            }).join('') + '</ul>';
                        }
                    });
                    result.innerHTML = html || '<p class="sa-cmd-note">没有可执行的操作</p>';
                }).catch(function (e) {
                    send.disabled = false;
                    send.textContent = '执行';
                    result.innerHTML = '<p class="sa-cmd-err">' + escapeHtml(e && e.message ? e.message : '网络请求失败') + '</p>';
                });
            }
            send.addEventListener('click', run);
            input.addEventListener('keydown', function (e) { if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) run(); });
            var body = doc.getElementById('sa-modal-body');
            (body ? body.querySelectorAll('.sa-cmd-chip') : []).forEach(function (c) {
                c.addEventListener('click', function () { input.value = c.textContent; run(); });
            });
        });
    }

    /* ---------- 登录页装饰 ---------- */
    function decorateLogin() {
        if (!doc.querySelector('.typecho-login')) return;
        var bar = doc.createElement('div');
        bar.className = 'sa-login-bar';
        var btn = doc.createElement('button');
        btn.type = 'button';
        btn.className = 'sa-theme-btn';
        btn.title = '主题设置';
        bar.appendChild(btn);
        doc.body.appendChild(bar);
        bindThemeButtons();
        var frag = doc.createDocumentFragment();
        for (var i = 0; i < 46; i++) {
            var s = doc.createElement('span');
            s.className = 'sa-star';
            var size = 1 + Math.random() * 2.4;
            s.style.cssText =
                'width:' + size + 'px;height:' + size + 'px;' +
                'left:' + (Math.random() * 100) + '%;top:' + (Math.random() * 100) + '%;' +
                '--t:' + (2 + Math.random() * 4).toFixed(2) + 's;' +
                '--o:' + (0.25 + Math.random() * 0.6).toFixed(2) + ';' +
                'animation-delay:' + (Math.random() * 5).toFixed(2) + 's';
            frag.appendChild(s);
        }
        doc.body.appendChild(frag);
    }

    /* ---------- 独立 AI 页面（ai-console.php） ---------- */
    function aiFetch(body, timeoutMs) {
        var ctrl = new AbortController();
        var timer = setTimeout(function () { ctrl.abort(); }, timeoutMs || 90000);
        return fetch(aiUrl(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
            signal: ctrl.signal
        }).then(function (r) { clearTimeout(timer); return r.json(); })
          .catch(function (e) {
              clearTimeout(timer);
              if (e && e.name === 'AbortError') throw new Error('请求超时，请重试');
              throw e;
          });
    }
    function bindAiConsole() {
        if (!/ai-console\.php/.test(location.pathname)) return;
        /* 当前页高亮侧边栏入口 */
        var entry = doc.querySelector('.sa-ai-entry');
        if (entry) entry.classList.add('sa-current');

        var status = doc.getElementById('sa-ai-status');
        /* 连接状态 */
        function ping() {
            if (status) status.textContent = '检查连接中…';
            aiFetch({ action: 'ping' }, 15000).then(function (d) {
                if (status) status.textContent = d.ok ? '已连接：' + (d.provider || '') + ' / ' + (d.model || '') : '未配置：' + (d.error || '');
            }).catch(function () {
                if (status) status.textContent = '连接失败';
            });
        }
        var testBtn = doc.getElementById('sa-ai-test');
        if (testBtn) testBtn.addEventListener('click', ping);
        ping();

        /* 对话 */
        var chat = doc.getElementById('sa-chat');
        var chatText = doc.getElementById('sa-chat-text');
        var chatSend = doc.getElementById('sa-chat-send');
        /* 对话历史：localStorage 持久化，超上限自动删最旧 */
        var HISTORY_KEY = 'sa-ai-history';
        var HISTORY_MAX = 40;
        function loadHistory() {
            try {
                var h = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
                return Array.isArray(h) ? h : [];
            } catch (e) { return []; }
        }
        function saveHistory() {
            try {
                localStorage.setItem(HISTORY_KEY, JSON.stringify(messages.slice(-HISTORY_MAX)));
            } catch (e) {}
        }
        var messages = loadHistory();
        /* 渲染历史对话 */
        messages.forEach(function (m) {
            if (m && m.role === 'user') {
                addMsg('user', String(m.content));
            } else if (m && m.role === 'assistant') {
                addMsg('bot', String(m.content));
            }
        });
        if (chat) chat.scrollTop = chat.scrollHeight;
        function addMsg(role, text) {
            var div = doc.createElement('div');
            div.className = 'sa-chat-msg ' + (role === 'user' ? 'me' : 'bot');
            div.innerHTML = role === 'user'
                ? '<div class="sa-chat-bubble me">' + escapeHtml(text) + '</div>'
                : '<span class="sa-chat-avatar">✦</span><div class="sa-chat-bubble">' + escapeHtml(text).replace(/\n/g, '<br>') + '</div>';
            chat.appendChild(div);
            chat.scrollTop = chat.scrollHeight;
            return div;
        }
        function sendChat() {
            var text = chatText.value.trim();
            if (!text) return;
            addMsg('user', text);
            messages.push({ role: 'user', content: text });
            saveHistory();
            chatText.value = '';
            doChat(messages[messages.length - 1].content, null);
        }
        function doChat(userText, retryWait) {
            if (retryWait) retryWait.remove();
            var wait = addMsg('bot', '🤔 正在思考…');
            /* 限流自动重试（免费模型偶发 429）：最多自动重试 2 次，间隔 10 秒 */
            var rateLimitRetries = 0;
            function doChatInner() {
                /* 统一走 chat：AI 自主判断——分析回复或执行操作（结果同样在此展示） */
                aiFetch({ action: 'chat', messages: messages }).then(function (d) {
                    if (!d.ok) {
                        if (d.error && /限流|繁忙/.test(d.error) && rateLimitRetries < 2) {
                            rateLimitRetries++;
                            wait.querySelector('.sa-chat-bubble').innerHTML = '⏳ AI 有点忙（限流），' + (rateLimitRetries === 1 ? '正在自动重试…' : '再试一次…');
                            setTimeout(doChatInner, 10000);
                            return;
                        }
                        wait.querySelector('.sa-chat-bubble').innerHTML = '⚠️ ' + escapeHtml(d.error || '出错了');
                        addRetry(wait, userText);
                        return;
                    }
                /* 执行操作结果（后端执行后返回 results） */
                var results = d && d.results;
                if (results && results.length) {
                    var html = results.map(function (r) {
                        var icon = r.ok ? '✅' : '⚠️';
                        var extra = r.list ? '<ul class="sa-cmd-list">' + r.list.map(function (p) {
                            return '<li><b>#' + escapeHtml(p.cid) + '</b> ' + escapeHtml(p.title) + ' <span class="sa-cmd-st">' + escapeHtml(p.status) + '</span></li>';
                        }).join('') + '</ul>' : '';
                        if (r.content) {
                            extra += '<div class="sa-cmd-content">' + escapeHtml(String(r.content).slice(0, 300)) + '</div>';
                        }
                        return '<div class="sa-cmd-item">' + icon + ' ' + escapeHtml(r.action || '') + '：' + escapeHtml(r.detail || '') + '</div>' + extra;
                    }).join('');
                    wait.querySelector('.sa-chat-bubble').innerHTML = html;
                    chat.scrollTop = chat.scrollHeight; /* 结果填充后自动滚到底部 */
                    messages.push({ role: 'assistant', content: results.map(function (r) { return r.detail || ''; }).join('\n') });
                    saveHistory();
                    var okCount = results.filter(function (r) { return r.ok; }).length;
                    var failCount = results.length - okCount;
                    if (failCount === 0) {
                        toast('✅ 已完成：' + (results[0] && results[0].detail || '操作成功'), 'success');
                    } else if (okCount > 0) {
                        toast('✅ ' + okCount + ' 项成功，⚠️ ' + failCount + ' 项失败', 'error');
                    } else {
                        toast('⚠️ 操作失败：' + (results[0] && results[0].detail || '未知原因'), 'error');
                    }
                    return;
                }
                /* 正常分析/聊天回复 */
                var replyHtml = '';
                if (d.web) {
                    replyHtml = '🔗 已联网查看：<a href="' + escapeHtml(d.web.url) + '" target="_blank" rel="noopener">' + escapeHtml(d.web.title) + '</a><br>';
                }
                wait.querySelector('.sa-chat-bubble').innerHTML = replyHtml + escapeHtml(d.reply || '').replace(/\n/g, '<br>');
                chat.scrollTop = chat.scrollHeight; /* 回复填充后自动滚到底部 */
                messages.push({ role: 'assistant', content: d.reply || '' });
                saveHistory();
                /* 生成文章时提供复制按钮 */
                if (d.reply && /^# /.test(d.reply.trim())) {
                    var copyBtn = doc.createElement('button');
                    copyBtn.type = 'button';
                    copyBtn.className = 'btn btn-s sa-copy-btn';
                    copyBtn.textContent = '复制全文';
                    copyBtn.addEventListener('click', function () {
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(d.reply).then(function () { toast('已复制 ✓', 'success'); });
                        } else {
                            var ta = doc.createElement('textarea');
                            ta.value = d.reply;
                            doc.body.appendChild(ta);
                            ta.select();
                            doc.execCommand('copy');
                            ta.remove();
                            toast('已复制 ✓', 'success');
                        }
                    });
                    wait.querySelector('.sa-chat-bubble').appendChild(copyBtn);
                }
            }).catch(function (e) {
                wait.querySelector('.sa-chat-bubble').innerHTML = '⚠️ ' + escapeHtml(e && e.message ? e.message : '网络请求失败');
                addRetry(wait, userText);
            });
            }
            doChatInner();
        }
        function addRetry(wait, userText) {
            var retry = doc.createElement('button');
            retry.type = 'button';
            retry.className = 'btn btn-s sa-copy-btn';
            retry.textContent = '重试';
            retry.addEventListener('click', function () { doChat(userText, wait); });
            wait.querySelector('.sa-chat-bubble').appendChild(retry);
        }
        if (chatSend && chatText) {
            chatSend.addEventListener('click', sendChat);
            chatText.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) sendChat();
            });
            doc.querySelectorAll('.sa-chat-tools .sa-cmd-chip').forEach(function (c) {
                c.addEventListener('click', function () {
                    chatText.value = c.getAttribute('data-prompt') + '\n\n';
                    chatText.focus();
                });
            });
        }

        /* 命令执行（右侧卡片） */
        var cmdInput = doc.getElementById('sa-cmd-input');
        var cmdSend = doc.getElementById('sa-cmd-send');
        var cmdResult = doc.getElementById('sa-cmd-result');
        if (cmdSend && cmdInput) {
            function runCmd() {
                var cmd = cmdInput.value.trim();
                if (!cmd) { cmdResult.innerHTML = '<p class="sa-cmd-err">请输入指令</p>'; return; }
                cmdSend.disabled = true;
                cmdSend.textContent = '执行中…';
                cmdResult.innerHTML = '<p class="sa-cmd-wait">AI 解析中…</p>';
                aiFetch({ action: 'command', command: cmd }).then(function (d) {
                    cmdSend.disabled = false;
                    cmdSend.textContent = '执行';
                    if (!d.ok) { cmdResult.innerHTML = '<p class="sa-cmd-err">' + escapeHtml(d.error || '失败') + '</p>'; return; }
                    if (d.note) { cmdResult.innerHTML = '<p class="sa-cmd-note">' + escapeHtml(d.note) + '</p>'; return; }
                    var html = '';
                    (d.results || []).forEach(function (r) {
                        html += '<div class="sa-cmd-item ' + (r.ok ? 'ok' : 'err') + '"><span class="sa-cmd-icon">' + (r.ok ? '✓' : '✗') + '</span>' +
                            '<div><div class="sa-cmd-title">' + escapeHtml(r.action || '') + '</div>' +
                            '<div class="sa-cmd-detail">' + escapeHtml(r.detail || '') + '</div></div></div>';
                        if (r.list) {
                            html += '<ul class="sa-cmd-list">' + r.list.map(function (p) {
                                return '<li><b>#' + p.cid + '</b> ' + escapeHtml(p.title) + ' <span class="sa-cmd-st">' + escapeHtml(p.status) + '</span> ' + escapeHtml(p.created) + '</li>';
                            }).join('') + '</ul>';
                        }
                    });
                    cmdResult.innerHTML = html;
                }).catch(function () {
                    cmdSend.disabled = false;
                    cmdSend.textContent = '执行';
                    cmdResult.innerHTML = '<p class="sa-cmd-err">网络请求失败</p>';
                });
            }
            cmdSend.addEventListener('click', runCmd);
        }
    }

    /* ---------- 插件设置页：检测可用模型 ---------- */
    function bindModelDetect() {
        var btn = doc.getElementById('sa-detect-models');
        if (!btn) return;
        var box = doc.getElementById('sa-model-list');
        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.textContent = '检测中…';
            box.innerHTML = '<span class="sa-cmd-wait">正在获取模型列表…</span>';
            aiFetch({ action: 'models' }, 30000).then(function (d) {
                btn.disabled = false;
                btn.textContent = '重新检测';
                if (!d.ok) {
                    box.innerHTML = '<span class="sa-cmd-err">⚠️ ' + escapeHtml(d.error || '检测失败') + '</span>';
                    return;
                }
                if (!d.models || !d.models.length) {
                    box.innerHTML = '<span class="sa-cmd-err">该服务商未返回可用模型</span>';
                    return;
                }
                box.innerHTML = '';
                d.models.forEach(function (m) {
                    var chip = doc.createElement('button');
                    chip.type = 'button';
                    chip.className = 'sa-model-chip' + (m === d.default ? ' sa-default' : '');
                    chip.textContent = m === d.default ? m + '（默认免费）' : m;
                    chip.title = m === d.default ? '当前服务商默认免费模型，点击填入' : '点击填入模型名';
                    chip.addEventListener('click', function () {
                        var input = doc.querySelector('input[name="ai_model"]');
                        if (input) input.value = m;
                        box.querySelectorAll('.sa-model-chip').forEach(function (c) { c.classList.remove('sa-active'); });
                        chip.classList.add('sa-active');
                        toast('已填入：' + m + '，记得点「保存设置」', 'success');
                    });
                    box.appendChild(chip);
                });
            }).catch(function (e) {
                btn.disabled = false;
                btn.textContent = '重新检测';
                box.innerHTML = '<span class="sa-cmd-err">⚠️ ' + escapeHtml(e && e.message ? e.message : '检测失败') + '</span>';
            });
        });
    }

    /* ---------- 启动 ---------- */
    function init() {
        if (doc.querySelector('.typecho-login')) {
            if (saFlags.ui) decorateLogin();
            return;
        }
        if (saFlags.ui) {
            setupSidebar();
            bindCollapse();
            bindUserMenu();
            bindThemeButtons();
            bindBatch();
            bindHelp();
        }
        initPage();
    }
    /* 页面级绑定（按插件开关分别加载美化 / AI 模块） */
    function initPage() {
        if (saFlags.ui) bindEditorTools();
        if (saFlags.ai) {
            bindCmd();
            bindAiConsole();
            bindModelDetect();
        }
    }
    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
