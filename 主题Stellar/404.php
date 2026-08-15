<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<section class="error-page reveal">
    <div class="error-stars" aria-hidden="true">
        <span class="err-star err-star-1">✦</span>
        <span class="err-star err-star-2">✦</span>
        <span class="err-star err-star-3">✦</span>
    </div>
    <div class="error-code">404</div>
    <h1 class="error-title"><?php _e('页面走丢了'); ?></h1>
    <p class="error-desc"><?php _e('你要找的内容可能已被移动或删除，也许那颗星星知道它去哪了。'); ?></p>
    <div class="error-actions">
        <a class="btn-primary" href="<?php $this->options->siteUrl(); ?>"><?php _e('返回首页'); ?></a>
        <a class="btn-ghost" href="javascript:history.back()"><?php _e('返回上一页'); ?></a>
    </div>
</section>
<?php $this->need('footer.php'); ?>
