<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="<?php $this->options->language(); ?>" data-theme="light" data-accent="<?php echo nova_option('accent', 'default'); ?>">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="generator" content="Typecho">
    <meta name="renderer" content="webkit">
    <title><?php $this->archiveTitle(array('category' => _t('分类 %s'), 'search' => _t('搜索 %s'), 'tag' => _t('标签 %s'), 'author' => _t('%s 的文章')), '', ' - '); ?><?php $this->options->title(); ?></title>
    <meta name="description" content="<?php $this->options->description(); ?>">
    <meta property="og:site_name" content="<?php $this->options->title(); ?>">
    <meta property="og:title" content="<?php $this->archiveTitle(array('category' => _t('分类 %s'), 'search' => _t('搜索 %s'), 'tag' => _t('标签 %s'), 'author' => _t('%s 的文章')), '', ' - '); ?><?php $this->options->title(); ?>">
    <meta property="og:type" content="<?php if ($this->is('post')) echo 'article'; elseif ($this->is('page')) echo 'website'; else echo 'website'; ?>">
    <meta property="og:url" content="<?php echo $this->request->getRequestUrl(); ?>">
    <meta property="og:description" content="<?php $this->options->description(); ?>">
    <?php
    /* favicon：图片优先，否则按 Logo 图标动态生成内联 SVG（渐变底 + 白色图形） */
    $logoImg = trim((string)nova_option('logoImage', ''));
    if ($logoImg !== '') {
        echo '<link rel="icon" href="' . htmlspecialchars($logoImg) . '">' . "\n";
    } else {
        $icon = (string)nova_option('logoIcon', 'star');
        $fav = nova_favicon_svg($icon);
        echo '<link rel="icon" type="image/svg+xml" href="' . $fav . '">' . "\n";
    }
    ?>
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php $this->options->feedUrl(); ?>">
    <link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php $this->options->feedUrl('/atom'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css?v=1.3.3'); ?>">
    <?php $this->header(); ?>
    <script>
        // 首屏前读取暗色偏好，避免闪烁
        (function () {
            var t = null;
            try { t = localStorage.getItem('nova-theme'); } catch (e) {}
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body class="<?php if ($this->is('post') || $this->is('page')) echo 'is-single'; ?>">
<div class="aurora" aria-hidden="true"><span></span><span></span><span></span></div>
<a class="skip-link" href="#main">跳到正文</a>

<header class="site-header" id="site-header">
    <div class="header-inner">
        <a class="brand" href="<?php $this->options->siteUrl(); ?>">
            <span class="brand-mark" aria-hidden="true">
                <?php echo nova_logo_html(20); ?>
            </span>
            <span class="brand-name"><?php $this->options->title(); ?></span>
        </a>

        <nav class="site-nav" id="site-nav" aria-label="主导航">
            <a href="<?php $this->options->siteUrl(); ?>"<?php if ($this->is('index')) echo ' class="active"'; ?>><?php _e('首页'); ?></a>
            <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
            <?php while ($pages->next()): ?>
                <a href="<?php $pages->permalink(); ?>"<?php if ($this->is('page', $pages->slug)) echo ' class="active"'; ?>><?php $pages->title(); ?></a>
            <?php endwhile; ?>
            <a href="<?php $this->options->feedUrl(); ?>" target="_blank" rel="noopener"><?php _e('RSS'); ?></a>
        </nav>

        <div class="header-actions">
            <button class="icon-btn" id="theme-toggle" type="button" aria-label="切换主题模式" title="切换主题模式">
                <svg class="icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                <svg class="icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
            </button>
            <button class="icon-btn search-open-btn" id="search-open" type="button" aria-label="搜索" title="搜索 (Ctrl+K)">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            </button>
            <button class="icon-btn menu-btn" id="menu-toggle" type="button" aria-label="打开菜单" aria-expanded="false">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>
</header>

<!-- 搜索遮罩层 -->
<div class="search-mask" id="search-mask" hidden>
    <div class="search-panel" role="dialog" aria-modal="true" aria-label="搜索">
        <form method="post" action="<?php $this->options->siteUrl(); ?>" class="search-form" id="search-form">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="text" name="s" id="search-input" placeholder="输入关键词搜索文章…" autocomplete="off" required>
            <button type="submit" class="search-submit">搜索</button>
            <button type="button" class="search-close" id="search-close" aria-label="关闭搜索">✕</button>
        </form>
    </div>
</div>

<!-- 移动端抽屉菜单 -->
<div class="drawer-backdrop" id="drawer-backdrop" hidden></div>
<aside class="drawer" id="drawer" aria-hidden="true">
    <div class="drawer-head">
        <span class="drawer-title">菜单</span>
        <button class="icon-btn" id="drawer-close" type="button" aria-label="关闭菜单">✕</button>
    </div>
    <nav class="drawer-nav">
        <a href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
        <?php $this->widget('Widget_Contents_Page_List')->to($drawerPages); ?>
        <?php while ($drawerPages->next()): ?>
            <a href="<?php $drawerPages->permalink(); ?>"><?php $drawerPages->title(); ?></a>
        <?php endwhile; ?>
        <a href="<?php $this->options->feedUrl(); ?>" target="_blank" rel="noopener"><?php _e('RSS 订阅'); ?></a>
    </nav>
    <div class="drawer-cats">
        <div class="drawer-cats-title"><?php _e('分类'); ?></div>
        <?php $this->widget('Widget_Metas_Category_List')->to($drawerCats); ?>
        <?php if ($drawerCats->have()): ?>
            <?php while ($drawerCats->next()): ?>
                <a href="<?php $drawerCats->permalink(); ?>"><?php $drawerCats->name(); ?></a>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</aside>

<!-- 阅读进度条 -->
<div class="reading-progress" id="reading-progress" aria-hidden="true"></div>

<main class="site-main" id="main">
