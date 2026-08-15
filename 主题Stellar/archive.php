<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<!-- 归档页头 -->
<section class="archive-head reveal">
    <div class="archive-kicker">
        <?php if ($this->is('category')): ?>
            <?php _e('分类'); ?>
        <?php elseif ($this->is('tag')): ?>
            <?php _e('标签'); ?>
        <?php elseif ($this->is('search')): ?>
            <?php _e('搜索'); ?>
        <?php elseif ($this->is('author')): ?>
            <?php _e('作者'); ?>
        <?php else: ?>
            <?php _e('归档'); ?>
        <?php endif; ?>
    </div>
    <h1 class="archive-title">
        <?php $this->archiveTitle(array('category' => _t('%s'), 'search' => _t('“%s”'), 'tag' => _t('#%s'), 'author' => _t('%s')), '', ''); ?>
    </h1>
    <?php if ($this->is('category')): ?>
        <p class="archive-desc"><?php echo $this->getDescription(); ?></p>
    <?php elseif ($this->is('search')): ?>
        <p class="archive-desc"><?php _e('共找到'); ?> <?php echo $this->getTotal(); ?> <?php _e('条结果'); ?></p>
    <?php endif; ?>
</section>

<div class="layout">
    <div class="content">
        <?php if ($this->have()): ?>
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
                            </div>
                            <h2 class="post-title"><a href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a></h2>
                            <div class="post-excerpt"><?php $this->excerpt(140, '…'); ?></div>
                            <div class="post-card-foot">
                                <span class="post-tags"><?php $this->tags(', ', true, 'none'); ?></span>
                                <a class="post-readmore" href="<?php $this->permalink(); ?>">
                                    <?php _e('阅读全文'); ?>
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6l6 6-6 6"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <nav class="pagination reveal" aria-label="分页">
                <?php $this->pageLink('上一页', 'prev'); ?>
                <?php $this->pageNav('«', '»', 3, '…', array('wrapClass' => 'page-numbers', 'currentClass' => 'current')); ?>
                <?php $this->pageLink('下一页', 'next'); ?>
            </nav>
        <?php else: ?>
            <div class="empty-state reveal">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <h2><?php _e('没有找到相关内容'); ?></h2>
                <p><?php _e('换个关键词再试试，或者返回首页逛逛。'); ?></p>
                <a class="btn-primary" href="<?php $this->options->siteUrl(); ?>"><?php _e('返回首页'); ?></a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (nova_option('showSidebar', '1') !== '0'): ?>
        <?php $this->need('sidebar.php'); ?>
    <?php endif; ?>
</div>
<?php $this->need('footer.php'); ?>
