<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<article class="single-post reveal" itemscope itemtype="http://schema.org/Article">
    <header class="single-head">
        <div class="single-meta">
            <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date('Y年m月d日'); ?></time>
            <span class="meta-dot">·</span>
            <?php $this->category(','); ?>
            <span class="meta-dot">·</span>
            <span class="reading-pill" id="reading-pill"><?php echo nova_reading_time($this->content); ?> 分钟读完</span>
            <?php if ($this->user->hasLogin() && $this->user->uid == $this->authorId): ?>
                <span class="meta-dot">·</span>
                <a class="post-edit" href="<?php $this->options->adminUrl(); ?>write-post.php?cid=<?php $this->cid(); ?>">编辑</a>
            <?php endif; ?>
        </div>
        <h1 class="single-title" itemprop="name"><?php $this->title(); ?></h1>
        <div class="single-tags">
            <?php $this->tags(' ', true, 'none'); ?>
        </div>
    </header>

    <div class="post-content" itemprop="articleBody">
        <?php $this->content(); ?>
    </div>

    <footer class="single-foot">
        <div class="single-share">
            <span><?php _e('分享：'); ?></span>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($this->permalink); ?>&text=<?php echo urlencode($this->title); ?>" target="_blank" rel="noopener nofollow">X / Twitter</a>
            <a href="https://service.weibo.com/share/share.php?url=<?php echo urlencode($this->permalink); ?>&title=<?php echo urlencode($this->title); ?>" target="_blank" rel="noopener nofollow">微博</a>
            <button type="button" class="copy-link" data-link="<?php $this->permalink(); ?>">复制链接</button>
        </div>
        <?php echo nova_neighbors($this); ?>
        <?php echo nova_related_posts($this); ?>
    </footer>
</article>

<?php $this->need('comments.php'); ?>
<?php $this->need('footer.php'); ?>
