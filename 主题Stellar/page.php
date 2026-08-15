<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<article class="single-post reveal" itemscope itemtype="http://schema.org/Article">
    <header class="single-head">
        <h1 class="single-title" itemprop="name"><?php $this->title(); ?></h1>
        <div class="single-meta">
            <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y年m月d日'); ?></time>
            <?php if ($this->user->hasLogin() && $this->user->uid == $this->authorId): ?>
                <span class="meta-dot">·</span>
                <a class="post-edit" href="<?php $this->options->adminUrl(); ?>write-page.php?cid=<?php $this->cid(); ?>">编辑</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="post-content" itemprop="articleBody">
        <?php $this->content(); ?>
    </div>
</article>

<?php $this->need('comments.php'); ?>
<?php $this->need('footer.php'); ?>
