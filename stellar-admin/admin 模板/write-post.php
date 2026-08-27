<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$post = \Widget\Contents\Post\Edit::alloc()->prepare();
?>
<main class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <form class="row typecho-page-main typecho-post-area" action="<?php $security->index('/action/contents-post-edit'); ?>" method="post" name="write_post">
            <div class="col-mb-12 col-tb-9" role="main">
                <?php if ($post->draft): ?>
                    <?php if ($post->draft['cid'] != $post->cid): ?>
                        <?php $postModifyDate = new \Typecho\Date($post->draft['modified']); ?>
                        <cite
                            class="edit-draft-notice"><?php _e('你正在编辑的是保存于 %s 的修订版, 你也可以 <a href="%s">删除它</a>', $postModifyDate->word(),
                                $security->getIndex('/action/contents-post-edit?do=deleteDraft&cid=' . $post->cid)); ?></cite>
                    <?php else: ?>
                        <cite class="edit-draft-notice"><?php _e('当前正在编辑的是未发布的草稿'); ?></cite>
                    <?php endif; ?>
                    <input name="draft" type="hidden" value="<?php echo $post->draft['cid'] ?>"/>
                <?php endif; ?>

                <p class="title">
                    <label for="title" class="sr-only"><?php _e('标题'); ?></label>
                    <input type="text" id="title" name="title" autocomplete="off" value="<?php $post->title(); ?>"
                           placeholder="<?php _e('标题'); ?>" class="w-100 text title"/>
                </p>
                <?php $permalink = \Typecho\Common::url($options->routingTable['post']['url'], $options->index);
                [,$permalink] = array_pad(explode(':', $permalink, 2), 2, '');
                $permalink = ltrim($permalink, '/');
                $permalink = preg_replace("/\[([_a-z0-9-]+)[^\]]*\]/i", "{\\1}", $permalink);
                if ($post->have()) {
                    $permalink = preg_replace_callback(
                        "/\{(cid|category|year|month|day)\}/i",
                        function ($matches) use ($post) {
                            $key = $matches[1];
                            return $post->getRouterParam($key);
                        },
                        $permalink
                    );
                }
                $input = '<input type="text" id="slug" name="slug" autocomplete="off" value="' . htmlspecialchars($post->slug ?? '') . '" class="mono" />';
                ?>
                <p class="mono url-slug">
                    <label for="slug" class="sr-only"><?php _e('网址缩略名'); ?></label>
                    <?php echo preg_replace("/\{slug\}/i", $input, $permalink); ?>
                </p>
                <p>
                    <label for="text" class="sr-only"><?php _e('文章内容'); ?></label>
                </p>
                <div class="sa-editor-bar">
                    <button type="button" class="sa-editor-btn" id="sa-md-toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
                        <?php _e('Markdown 语法'); ?>
                    </button>
                    <button type="button" class="sa-editor-btn" id="sa-undo-btn" hidden>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                        <?php _e('撤回'); ?>
                    </button>
                    <button type="button" class="sa-editor-btn" id="sa-redo-btn" hidden>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 14 5-5-5-5"/><path d="M20 9H9.5A5.5 5.5 0 0 0 4 14.5v0A5.5 5.5 0 0 0 9.5 20H13"/></svg>
                        <?php _e('重做'); ?>
                    </button>
                </div>
                <div class="sa-md-guide" id="sa-md-guide" hidden>
                    <p class="sa-md-tip"><em>💡 点击任意语法即可插入到光标位置（可再点「撤回」还原）</em></p>
                    <div class="sa-md-guide-grid">
                        <div class="sa-md-item">
                            <code data-insert="# {{标题}}"># 一级标题</code><code data-insert="## {{二级标题}}">## 二级标题</code><code data-insert="### {{三级标题}}">### 三级标题</code>
                            <p><em># 后面加空格再输入标题文字</em>，最多支持 6 级（######）</p>
                        </div>
                        <div class="sa-md-item">
                            <code data-insert="**{{加粗文字}}**">**加粗文字**</code><code data-insert="*{{斜体文字}}*">*斜体文字*</code><code data-insert="~~{{删除线}}~~">~~删除线~~</code>
                            <p>符号成对包裹中间的文字，即可实现对应效果</p>
                        </div>
                        <div class="sa-md-item">
                            <code data-insert="- {{列表项}}">- 列表项</code><code data-insert="1. {{编号项}}">1. 编号项</code><code data-insert="- [ ] {{待办事项}}">- [ ] 待办事项</code>
                            <p>减号或数字加点 + 空格，可嵌套（前面加空格缩进）</p>
                        </div>
                        <div class="sa-md-item">
                            <code data-insert="&gt; {{引用文字}}">&gt; 引用文字</code><code data-insert="```{{语言}}
{{代码}}
```">```代码块```</code><code data-insert="`{{行内代码}}`">`行内代码`</code>
                            <p>三个反引号包住多行代码（可写语言名高亮）</p>
                        </div>
                        <div class="sa-md-item">
                            <code data-insert="[{{链接文字}}](https://网址)">[链接文字](https://网址)</code><code data-insert="![{{图片描述}}](https://图片地址)">![图片描述](https://图片地址)</code>
                            <p>方括号放文字，圆括号放地址</p>
                        </div>
                        <div class="sa-md-item">
                            <code data-insert="---">---</code>
                            <p>三个减号单独一行 = 分隔线</p>
                        </div>
                        <div class="sa-md-item">
                            <code data-insert="| {{列1}} | {{列2}} |
| --- | --- |">| 列1 | 列2 |</code>
                            <p>竖线分隔的表格，第二行写 --- 对齐</p>
                        </div>
                        <div class="sa-md-item sa-md-item-hot">
                            <code data-insert="&#60;!--more--&#62;">&lt;!--more--&gt;</code>
                            <p><em>首页截断标记</em>：插入该标记后，首页只显示标记前的内容作为摘要，点击「阅读全文」展开</p>
                        </div>
                    </div>
                    <div class="sa-md-tip">💡 写作小贴士：先写 <strong># 一级标题</strong> 开头；段落之间空一行；想换行不换段就在行尾加两个空格</div>
                </div>
                <textarea style="height: <?php $options->editorSize(); ?>px" autocomplete="off" id="text"
                          name="text" class="w-100 mono"><?php echo htmlspecialchars($post->text); ?></textarea>

                <?php include 'custom-fields.php'; ?>

                <p class="submit">
                    <span class="left">
                        <button type="button" id="btn-cancel-preview" class="btn"><i
                                class="i-caret-left"></i> <?php _e('取消预览'); ?></button>
                    </span>
                    <span class="right">
                        <input type="hidden" name="do" value="publish" />
                        <input type="hidden" name="cid" value="<?php $post->cid(); ?>"/>
                        <button type="button" id="btn-preview" class="btn"><i
                                class="i-exlink"></i> <?php _e('预览文章'); ?></button>
                        <button type="submit" name="do" value="save" id="btn-save"
                                class="btn"><?php _e('保存草稿'); ?></button>
                        <button type="submit" name="do" value="publish" class="btn primary"
                                id="btn-submit"><?php _e('发布文章'); ?></button>
                        <?php if ($options->markdown && (!$post->have() || $post->isMarkdown)): ?>
                            <input type="hidden" name="markdown" value="1"/>
                        <?php endif; ?>
                    </span>
                </p>

                <?php \Typecho\Plugin::factory('admin/write-post.php')->call('content', $post); ?>
            </div>

            <div id="edit-secondary" class="col-mb-12 col-tb-3" role="complementary">
                <ul class="typecho-option-tabs">
                    <li class="active w-50"><a href="#tab-advance"><?php _e('选项'); ?></a></li>
                    <li class="w-50"><a href="#tab-files" id="tab-files-btn"><?php _e('附件'); ?></a></li>
                </ul>


                <div id="tab-advance" class="tab-content">
                    <section class="typecho-post-option" role="application">
                        <label for="date" class="typecho-label"><?php _e('发布日期'); ?></label>
                        <p><input class="typecho-date w-100" type="text" name="date" id="date" autocomplete="off"
                                  value="<?php $post->have() && $post->created > 0 ? $post->date('Y-m-d H:i') : ''; ?>"/>
                        </p>
                    </section>

                    <section class="typecho-post-option category-option">
                        <label class="typecho-label"><?php _e('分类'); ?></label>
                        <?php \Widget\Metas\Category\Rows::alloc()->to($category); ?>
                        <ul>
                            <?php $categories = array_column($post->categories, 'mid'); ?>
                            <?php while ($category->next()): ?>
                                <li><?php echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $category->levels); ?><input
                                        type="checkbox" id="category-<?php $category->mid(); ?>"
                                        value="<?php $category->mid(); ?>" name="category[]"
                                        <?php if (in_array($category->mid, $categories)): ?>checked="true"<?php endif; ?>/>
                                    <label
                                        for="category-<?php $category->mid(); ?>"><?php $category->name(); ?></label>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </section>

                    <section class="typecho-post-option">
                        <label for="token-input-tags" class="typecho-label"><?php _e('标签'); ?></label>
                        <p><input id="tags" name="tags" type="text" value="<?php $post->have() ? $post->tags(',', false) : ''; ?>"
                                  class="w-100 text"/></p>
                    </section>

                    <?php \Typecho\Plugin::factory('admin/write-post.php')->call('option', $post); ?>

                    <details id="advance-panel">
                        <summary class="btn btn-xs"><?php _e('高级选项'); ?> <i class="i-caret-down"></i></summary>

                        <?php if ($user->pass('editor', true)): ?>
                            <section class="typecho-post-option visibility-option">
                                <label for="visibility" class="typecho-label"><?php _e('公开度'); ?></label>
                                <p>
                                    <select id="visibility" name="visibility">
                                        <?php if ($user->pass('editor', true)): ?>
                                            <option
                                                value="publish"<?php if (($post->status == 'publish' && !$post->password) || !$post->status): ?> selected<?php endif; ?>><?php _e('公开'); ?></option>
                                            <option
                                                value="hidden"<?php if ($post->status == 'hidden'): ?> selected<?php endif; ?>><?php _e('隐藏'); ?></option>
                                            <option
                                                value="password"<?php if (strlen($post->password ?? '') > 0): ?> selected<?php endif; ?>><?php _e('密码保护'); ?></option>
                                            <option
                                                value="private"<?php if ($post->status == 'private'): ?> selected<?php endif; ?>><?php _e('私密'); ?></option>
                                        <?php endif; ?>
                                        <option
                                            value="waiting"<?php if (!$user->pass('editor', true) || $post->status == 'waiting'): ?> selected<?php endif; ?>><?php _e('待审核'); ?></option>
                                    </select>
                                </p>
                                <p id="post-password"<?php if (strlen($post->password ?? '') == 0): ?> class="hidden"<?php endif; ?>>
                                    <label for="protect-pwd" class="sr-only">内容密码</label>
                                    <input type="text" name="password" id="protect-pwd" class="text-s"
                                           value="<?php $post->password(); ?>" size="16"
                                           placeholder="<?php _e('内容密码'); ?>" autocomplete="off"/>
                                </p>
                            </section>
                        <?php endif; ?>

                        <section class="typecho-post-option allow-option">
                            <label class="typecho-label"><?php _e('权限控制'); ?></label>
                            <ul>
                                <li><input id="allowComment" name="allowComment" type="checkbox" value="1"
                                           <?php if ($post->allow('comment')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowComment"><?php _e('允许评论'); ?></label></li>
                                <li><input id="allowPing" name="allowPing" type="checkbox" value="1"
                                           <?php if ($post->allow('ping')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowPing"><?php _e('允许被引用'); ?></label></li>
                                <li><input id="allowFeed" name="allowFeed" type="checkbox" value="1"
                                           <?php if ($post->allow('feed')): ?>checked="true"<?php endif; ?> />
                                    <label for="allowFeed"><?php _e('允许在聚合中出现'); ?></label></li>
                            </ul>
                        </section>

                        <section class="typecho-post-option">
                            <label for="trackback" class="typecho-label"><?php _e('引用通告'); ?></label>
                            <p><textarea id="trackback" class="w-100 mono" name="trackback" rows="2"></textarea></p>
                            <p class="description"><?php _e('每一行一个引用地址, 用回车隔开'); ?></p>
                        </section>

                        <?php \Typecho\Plugin::factory('admin/write-post.php')->call('advanceOption', $post); ?>
                    </details><!-- end #advance-panel -->

                    <?php if ($post->have()): ?>
                        <?php $modified = new \Typecho\Date($post->modified); ?>
                        <section class="typecho-post-option">
                            <p class="description">
                                <br>&mdash;<br>
                                <?php _e('本文由 <a href="%s">%s</a> 撰写',
                                    \Typecho\Common::url('manage-posts.php?uid=' . $post->author->uid, $options->adminUrl), $post->author->screenName); ?>
                                <br>
                                <?php _e('最后更新于 %s', $modified->word()); ?>
                            </p>
                        </section>
                    <?php endif; ?>
                </div><!-- end #tab-advance -->

                <div id="tab-files" class="tab-content hidden">
                    <?php include 'file-upload.php'; ?>
                </div><!-- end #tab-files -->
            </div>
        </form>
    </div>
</main>

<?php
include 'copyright.php';
include 'common-js.php';
include 'form-js.php';
include 'write-js.php';

\Typecho\Plugin::factory('admin/write-post.php')->trigger($plugged)->call('richEditor', $post);
if (!$plugged) {
    include 'editor-js.php';
}

include 'file-upload-js.php';
include 'custom-fields-js.php';
\Typecho\Plugin::factory('admin/write-post.php')->call('bottom', $post);
include 'footer.php';
?>
