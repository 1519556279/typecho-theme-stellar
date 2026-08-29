<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 咔咔主题 —— 现代、简洁、功能全面的 Typecho 博客主题。
 * 毛玻璃导航、暗色模式、星辰渐变配色、滚动动效、代码高亮、图片懒加载、
 * 8 种社交渠道、文章阅读时间统计、评论头像本地缓存，开箱即用。
 *
 * @package 咔咔
 * @author 咔咔
 * @link https://lioip.cn
 * @version 1.2.0
 */
?>
<?php $this->need('header.php'); ?>

<!-- Hero 区域 -->
<section class="hero reveal">
    <div class="hero-inner">
        <?php if (nova_option('showAvatar', '0') === '1'): ?>
            <div class="hero-avatar">
                <span class="hero-avatar-letter"><?php echo mb_substr($this->options->title(), 0, 1, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>
        <h1 class="hero-title"><?php $this->options->title(); ?></h1>
        <p class="hero-desc"><?php echo nova_option('siteDesc', $this->options->description); ?></p>
        <div class="hero-stats">
            <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
            <span class="hero-stat"><strong><?php $stat->publishedPostsNum(); ?></strong><em><?php _e('文章'); ?></em></span>
            <span class="hero-dot">·</span>
            <span class="hero-stat"><strong><?php $stat->publishedCommentsNum(); ?></strong><em><?php _e('评论'); ?></em></span>
            <span class="hero-dot">·</span>
            <span class="hero-stat"><strong><?php $stat->categoriesNum(); ?></strong><em><?php _e('分类'); ?></em></span>
        </div>
    </div>
</section>

<div class="layout">
    <div class="content">
        <?php if ($this->have()): ?>
            <div class="section-head reveal" id="latest">
                <span class="section-kicker">LATEST</span>
                <h2 class="section-title">最新文章</h2>
            </div>
            <div class="post-list">
                <?php while ($this->next()): ?>
                    <article class="post-card reveal" id="post-<?php $this->cid(); ?>">
                        <?php $cover = nova_cover($this); ?>
                        <?php if (!empty($cover)): ?>
                            <a class="post-cover" href="<?php $this->permalink(); ?>" tabindex="-1" aria-hidden="true">
                                <img src="<?php echo $cover; ?>" alt="<?php $this->title(); ?>" loading="lazy">
                            </a>
                        <?php endif; ?>

                        <div class="post-card-body">
                            <div class="post-meta">
                                <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y-m-d'); ?></time>
                                <?php $this->category(','); ?>
                                <?php if ($this->user->hasLogin() && $this->user->uid == $this->authorId): ?>
                                    <a class="post-edit" href="<?php $this->options->adminUrl(); ?>write-post.php?cid=<?php $this->cid(); ?>">编辑</a>
                                <?php endif; ?>
                            </div>

                            <h2 class="post-title"><a href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a></h2>

                            <div class="post-excerpt">
                                <?php $this->excerpt(160, '…'); ?>
                            </div>

                            <div class="post-card-foot">
                                <span class="post-tags">
                                    <?php $this->tags(', ', true, ''); ?>
                                </span>
                                <a class="post-readmore" href="<?php $this->permalink(); ?>">
                                    <?php _e('阅读全文'); ?>
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6l6 6-6 6"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php if ($this->have() && ($this->_currentPage > 1 || $this->getTotal() > $this->parameter->pageSize)): ?>
                <nav class="pagination reveal" aria-label="分页">
                    <?php $this->pageLink('上一页', 'prev'); ?>
                    <?php $this->pageNav('«', '»', 3, '…', array('wrapClass' => 'page-numbers', 'currentClass' => 'current')); ?>
                    <?php $this->pageLink('下一页', 'next'); ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state reveal">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                <h2><?php _e('还没有文章'); ?></h2>
                <p><?php _e('去后台写第一篇文章吧，这个位置会展示最新内容。'); ?></p>
                <a class="btn-primary" href="<?php $this->options->adminUrl(); ?>write-post.php"><?php _e('开始写作'); ?></a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (nova_option('showSidebar', '1') !== '0'): ?>
        <?php $this->need('sidebar.php'); ?>
    <?php endif; ?>
</div>
<?php $this->need('footer.php'); ?>
