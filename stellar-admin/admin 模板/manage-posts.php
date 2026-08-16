<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = \Widget\Stat::alloc();
$posts = \Widget\Contents\Post\Admin::alloc();
$isAllPosts = ('on' == $request->get('__typecho_all_posts') || 'on' == \Typecho\Cookie::get('__typecho_all_posts'));
?>
<main class="main">
    <div class="sa-content">
        <?php include 'page-title.php'; ?>

        <div class="sa-toolbar">
            <div class="sa-tabs">
                <a href="<?php $options->adminUrl('manage-posts.php'
                    . (isset($request->uid) ? '?uid=' . $request->filter('encode')->uid : '')); ?>"
                   class="sa-tab<?php if (!isset($request->status) || 'all' == $request->get('status')): ?> current<?php endif; ?>"><?php _e('可用'); ?></a>
                <a href="<?php $options->adminUrl('manage-posts.php?status=waiting'
                    . (isset($request->uid) ? '&uid=' . $request->filter('encode')->uid : '')); ?>"
                   class="sa-tab<?php if ('waiting' == $request->get('status')): ?> current<?php endif; ?>"><?php _e('待审核'); ?>
                    <?php if (!$isAllPosts && $stat->myWaitingPostsNum > 0 && !isset($request->uid)): ?>
                        <span class="sa-balloon"><?php $stat->myWaitingPostsNum(); ?></span>
                    <?php elseif ($isAllPosts && $stat->waitingPostsNum > 0 && !isset($request->uid)): ?>
                        <span class="sa-balloon"><?php $stat->waitingPostsNum(); ?></span>
                    <?php elseif (isset($request->uid) && $stat->currentWaitingPostsNum > 0): ?>
                        <span class="sa-balloon"><?php $stat->currentWaitingPostsNum(); ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php $options->adminUrl('manage-posts.php?status=draft'
                    . (isset($request->uid) ? '&uid=' . $request->filter('encode')->uid : '')); ?>"
                   class="sa-tab<?php if ('draft' == $request->get('status')): ?> current<?php endif; ?>"><?php _e('草稿'); ?>
                    <?php if (!$isAllPosts && $stat->myDraftPostsNum > 0 && !isset($request->uid)): ?>
                        <span class="sa-balloon"><?php $stat->myDraftPostsNum(); ?></span>
                    <?php elseif ($isAllPosts && $stat->draftPostsNum > 0 && !isset($request->uid)): ?>
                        <span class="sa-balloon"><?php $stat->draftPostsNum(); ?></span>
                    <?php elseif (isset($request->uid) && $stat->currentDraftPostsNum > 0): ?>
                        <span class="sa-balloon"><?php $stat->currentDraftPostsNum(); ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($user->pass('editor', true) && !isset($request->uid)): ?>
                    <span class="sa-tab-sep"></span>
                    <a href="<?php echo $request->makeUriByRequest('__typecho_all_posts=on&page=1'); ?>"
                       class="sa-tab<?php if ($isAllPosts): ?> current<?php endif; ?>"><?php _e('所有'); ?></a>
                    <a href="<?php echo $request->makeUriByRequest('__typecho_all_posts=off&page=1'); ?>"
                       class="sa-tab<?php if (!$isAllPosts): ?> current<?php endif; ?>"><?php _e('我的'); ?></a>
                <?php endif; ?>
            </div>

            <form method="get" class="sa-search" role="search">
                <?php if ('' != $request->keywords || '' != $request->category): ?>
                    <a href="<?php $options->adminUrl('manage-posts.php'
                        . (isset($request->status) || isset($request->uid) ? '?' .
                            (isset($request->status) ? 'status=' . $request->filter('encode')->status : '') .
                            (isset($request->uid) ? (isset($request->status) ? '&' : '') . 'uid=' . $request->filter('encode')->uid : '') : '')); ?>"
                       class="sa-search-clear"><?php _e('清除筛选'); ?></a>
                <?php endif; ?>
                <input type="text" placeholder="<?php _e('搜索文章…'); ?>"
                       value="<?php echo $request->filter('html')->keywords; ?>" name="keywords"/>
                <select name="category">
                    <option value=""><?php _e('全部分类'); ?></option>
                    <?php \Widget\Metas\Category\Rows::alloc()->to($category); ?>
                    <?php while ($category->next()): ?>
                        <option value="<?php $category->mid(); ?>"<?php if ($request->get('category') == $category->mid): ?> selected="true"<?php endif; ?>><?php $category->name(); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-s"><?php _e('搜索'); ?></button>
                <?php if (isset($request->uid)): ?>
                    <input type="hidden" value="<?php echo $request->filter('html')->uid; ?>" name="uid"/>
                <?php endif; ?>
                <?php if (isset($request->status)): ?>
                    <input type="hidden" value="<?php echo $request->filter('html')->status; ?>" name="status"/>
                <?php endif; ?>
            </form>
        </div>

        <form method="post" name="manage_posts" class="sa-post-list" action="<?php $security->index('/action/contents-post-edit'); ?>">
            <?php if ($posts->have()): ?>
                <?php while ($posts->next()): ?>
                    <div class="sa-post" id="<?php $posts->theId(); ?>">
                        <label class="sa-check">
                            <i class="sr-only"><?php _e('选择'); ?></i>
                            <input type="checkbox" value="<?php $posts->cid(); ?>" name="cid[]"/>
                        </label>
                        <div class="sa-post-body">
                            <div class="sa-post-title">
                                <a href="<?php $options->adminUrl('write-post.php?cid=' . $posts->cid); ?>"><?php $posts->title(); ?></a>
                                <?php
                                if ('post_draft' == $posts->type) {
                                    echo '<span class="sa-badge draft">' . _t('草稿') . '</span>';
                                } elseif ($posts->revision) {
                                    echo '<span class="sa-badge">' . _t('有修订版') . '</span>';
                                }

                                if ('hidden' == $posts->status) {
                                    echo '<span class="sa-badge hidden">' . _t('隐藏') . '</span>';
                                } elseif ('waiting' == $posts->status) {
                                    echo '<span class="sa-badge waiting">' . _t('待审核') . '</span>';
                                } elseif ('private' == $posts->status) {
                                    echo '<span class="sa-badge private">' . _t('私密') . '</span>';
                                } elseif ($posts->password) {
                                    echo '<span class="sa-badge">' . _t('密码保护') . '</span>';
                                }
                                ?>
                            </div>
                            <div class="sa-post-meta">
                                <span><?php $posts->author(); ?></span>
                                <span class="sa-dot">·</span>
                                <?php foreach ($posts->categories as $index => $category): ?>
                                    <?php if ($index > 0): ?><span class="sa-dot">·</span><?php endif; ?>
                                    <a href="<?php $options->adminUrl('manage-posts.php?category=' . $category['mid']
                                        . (isset($request->uid) ? '&uid=' . $request->filter('encode')->uid : '')
                                        . (isset($request->status) ? '&status=' . $request->filter('encode')->status : '')); ?>"><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></a>
                                <?php endforeach; ?>
                                <span class="sa-dot">·</span>
                                <a href="<?php $options->adminUrl('manage-comments.php?cid=' . ($posts->parentId ? $posts->parentId : $posts->cid)); ?>"><?php $posts->commentsNum(); ?> <?php _e('条评论'); ?></a>
                                <span class="sa-dot">·</span>
                                <?php if ('post_draft' == $posts->type || $posts->revision): ?>
                                    <?php $modifyDate = new \Typecho\Date($posts->revision ? $posts->revision['modified'] : $posts->modified); ?>
                                    <span class="sa-meta-muted"><?php _e('保存于 %s', $modifyDate->word()); ?></span>
                                <?php else: ?>
                                    <?php $posts->dateWord(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="sa-post-actions">
                            <a href="<?php $options->adminUrl('write-post.php?cid=' . $posts->cid); ?>"
                               class="sa-act" title="<?php _e('编辑'); ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </a>
                            <?php if ('post_draft' != $posts->type): ?>
                                <a href="<?php $posts->permalink(); ?>" class="sa-act" target="_blank" rel="noopener" title="<?php _e('查看'); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            <?php endif; ?>
                            <button type="button" class="sa-act sa-act-del" data-cid="<?php $posts->cid(); ?>"
                                    title="<?php _e('删除'); ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="sa-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="M9 15h6"/></svg>
                    <p><?php _e('这里空空如也，还没有任何文章'); ?></p>
                    <a class="btn btn-primary" href="<?php $options->adminUrl('write-post.php'); ?>"><?php _e('写第一篇'); ?></a>
                </div>
            <?php endif; ?>
        </form>

        <?php if ($posts->have()): ?>
            <div class="sa-list-foot">
                <div class="sa-batch">
                    <label class="sa-check sa-check-all">
                        <i class="sr-only"><?php _e('全选'); ?></i>
                        <input type="checkbox" class="typecho-table-select-all"/>
                        <span><?php _e('全选'); ?></span>
                    </label>
                    <div class="sa-batch-ops">
                        <a lang="<?php _e('你确认要删除选中的文章吗?'); ?>"
                           href="<?php $security->index('/action/contents-post-edit?do=delete'); ?>"
                           class="sa-batch-op danger"><?php _e('删除'); ?></a>
                        <?php if ($user->pass('editor', true)): ?>
                            <a lang="<?php _e('确定标记为公开吗?'); ?>"
                               href="<?php $security->index('/action/contents-post-edit?do=mark&status=publish'); ?>"
                               class="sa-batch-op"><?php _e('标记为公开'); ?></a>
                            <a lang="<?php _e('确定标记为待审核吗?'); ?>"
                               href="<?php $security->index('/action/contents-post-edit?do=mark&status=waiting'); ?>"
                               class="sa-batch-op"><?php _e('标记为待审核'); ?></a>
                            <a lang="<?php _e('确定标记为隐藏吗?'); ?>"
                               href="<?php $security->index('/action/contents-post-edit?do=mark&status=hidden'); ?>"
                               class="sa-batch-op"><?php _e('标记为隐藏'); ?></a>
                            <a lang="<?php _e('确定标记为私密吗?'); ?>"
                               href="<?php $security->index('/action/contents-post-edit?do=mark&status=private'); ?>"
                               class="sa-batch-op"><?php _e('标记为私密'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sa-pager">
                    <?php $posts->pageNav(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
include 'footer.php';
?>
