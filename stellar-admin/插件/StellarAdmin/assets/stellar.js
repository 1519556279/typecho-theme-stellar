/* Stellar Admin —— 新框架交互：主题/折叠/用户菜单/批量操作/登录页装饰 */
(function () {
    'use strict';
    var doc = document, root = doc.documentElement;
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

    /* ---------- 编辑器工具（Markdown 引导插入 / 撤回重做） ---------- */


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


        }
    }

    /* ---------- 命令面板（宝塔式 API 调用） ---------- */


    function decorateLogin() {
        try { if (sessionStorage.getItem('sa-login-anim')) return; sessionStorage.setItem('sa-login-anim', '1'); } catch (e) {}
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


    /* ---------- 启动 ---------- */
    /* ---------- 启动 ---------- */
    function init() {
        if (doc.querySelector('.typecho-login')) {
            decorateLogin();
            return;
        }
        setupSidebar();
        bindCollapse();
        bindUserMenu();
        bindThemeButtons();
        bindBatch();
        bindHelp();
        bindEditorTools();
    }
    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
