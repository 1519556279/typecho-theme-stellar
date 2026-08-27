<?php if (!defined('__TYPECHO_ADMIN__')) exit; ?>
<div class="sa-sidebar-head">
    <a class="sa-brand" href="<?php $options->adminUrl(); ?>">
        <span class="sa-brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 4.9L20 8l-3.6 3.9L17.5 18 12 15.2 6.5 18l1.1-6.1L4 8l5.6-1.1z"/></svg>
        </span>
        <span class="sa-brand-text"><?php $options->title(); ?></span>
    </a>
    <button type="button" class="sa-sidebar-toggle" title="折叠 / 展开侧边栏">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M3 12h12"/><path d="M3 18h18"/></svg>
    </button>
</div>
<nav class="sa-nav">
    <?php if ((\Typecho\Widget::widget('Widget_Options')->plugin('StellarAdmin')->sa_enable_ai ?? '1') !== '0'): ?>
    <li class="sa-ai-entry"><a href="<?php $options->adminUrl('ai-console.php'); ?>">
        <svg class="sa-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3Z"/></svg>
        <span><?php _e('AI 大屏'); ?></span></a></li>
    <?php endif; ?>
    <?php $menu->output('', 'sa-current'); ?>
</nav>
<div class="sa-sidebar-foot">
    <div class="sa-user" id="sa-sidebar-user">
        <span class="sa-avatar"><?php echo mb_substr($user->screenName ?? 'U', 0, 1, 'UTF-8'); ?></span>
        <span class="sa-user-name"><?php $user->screenName(); ?></span>
    </div>
    <div class="sa-sidebar-links">
        <a href="<?php $options->siteUrl(); ?>" class="sa-foot-link" title="<?php _e('查看网站'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </a>
        <a href="<?php $options->logoutUrl(); ?>" class="sa-foot-link" title="<?php _e('登出'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
        </a>
    </div>
</div>
</aside>
<div class="sa-shell">
    <header class="sa-topbar">
        <button type="button" class="sa-topbar-toggle" title="<?php _e('折叠 / 展开侧边栏'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/></svg>
        </button>
        <div class="sa-crumbs">
            <span class="sa-crumb"><?php echo $menu->title ?? ''; ?></span>
        </div>
        <div class="sa-topbar-right">
            <button type="button" class="sa-theme-btn" id="sa-theme-btn" title="<?php _e('主题设置'); ?>"></button>
            <button type="button" class="sa-help-btn" id="sa-help-btn" title="<?php _e('帮助'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
            </button>
            <button type="button" class="sa-help-btn" id="sa-cmd-btn" title="<?php _e('命令面板'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 16 4-4-4-4"/><path d="m6 8-4 4 4 4"/><path d="m14.5 4-5 16"/></svg>
            </button>
            <button type="button" class="sa-user-chip" id="sa-user-chip">
                <span class="sa-avatar"><?php echo mb_substr($user->screenName ?? 'U', 0, 1, 'UTF-8'); ?></span>
                <span class="sa-user-name"><?php $user->screenName(); ?></span>
            </button>
            <div class="sa-user-menu" id="sa-user-menu">
                <a href="<?php $options->adminUrl('profile.php'); ?>"><?php _e('个人资料'); ?></a>
                <a href="<?php $options->siteUrl(); ?>"><?php _e('查看网站'); ?></a>
                <a href="<?php $options->logoutUrl(); ?>" class="sa-danger"><?php _e('登出'); ?></a>
            </div>
        </div>
    </header>
