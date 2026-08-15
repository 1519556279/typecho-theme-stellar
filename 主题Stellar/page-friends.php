<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 友链页面模板：后台新建独立页面时选择「友链」模板即可
 * 友链在 设置 → 外观 → 设置外观 → 友情链接 中配置（每行：名称|链接|简介）
 */
$friendList = nova_friends();
?>
<?php $this->need('header.php'); ?>

<article class="single-post reveal">
    <header class="single-head">
        <h1 class="single-title"><?php $this->title(); ?></h1>
        <div class="single-meta">
            <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y年m月d日'); ?></time>
        </div>
    </header>

    <div class="post-content">
        <?php $this->content(); ?>
    </div>

    <?php if (!empty($friendList)): ?>
        <div class="friends-grid">
            <?php foreach ($friendList as $f): ?>
                <a class="friend-card" href="<?php echo htmlspecialchars($f['url']); ?>" target="_blank" rel="noopener nofollow">
                    <span class="friend-avatar"><?php echo htmlspecialchars(mb_substr($f['name'], 0, 1, 'UTF-8')); ?></span>
                    <span class="friend-info">
                        <span class="friend-name"><?php echo htmlspecialchars($f['name']); ?></span>
                        <?php if (!empty($f['desc'])): ?>
                            <span class="friend-desc"><?php echo htmlspecialchars($f['desc']); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="friend-arrow">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="friends-empty"><?php _e('暂无友链，请在后台「设置 → 外观 → 友情链接」中添加。'); ?></p>
    <?php endif; ?>
</article>

<?php $this->need('comments.php'); ?>
<?php $this->need('footer.php'); ?>
