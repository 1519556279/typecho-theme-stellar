<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<section class="comments-section reveal" id="comments">
    <h2 class="comments-title">
        <?php $this->commentsNum(_t('暂无评论'), _t('1 条评论'), _t('%d 条评论')); ?>
    </h2>

    <?php $this->comments()->to($comments); ?>

    <?php if ($comments->have()): ?>
        <ol class="comment-list">
            <?php $comments->listComments(array('avatarSize' => 44, 'defaultAvatar' => 'identicon', 'before' => '', 'after' => '')); ?>
        </ol>
        <?php $comments->pageNav('«', '»', 3, '…', array('wrapClass' => 'comment-page')); ?>
    <?php endif; ?>

    <?php if ($this->allow('comment')): ?>
        <div id="<?php $this->respondId(); ?>" class="respond">
            <h3 class="respond-title">
                <span class="respond-cancel"><?php $comments->cancelReply(); ?></span>
                <?php _e('发表评论'); ?>
            </h3>

            <form method="post" action="<?php $this->commentUrl(); ?>" id="comment-form" class="comment-form">
                <input type="hidden" name="parent" id="comment-parent" value="0">

                <?php if ($this->user->hasLogin()): ?>
                    <p class="respond-logged">
                        <?php _e('已登录：'); ?><strong><?php $this->user->screenName(); ?></strong>
                        <a href="<?php $this->options->profileUrl(); ?>"><?php _e('个人资料'); ?></a>
                        <a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a>
                    </p>
                <?php else: ?>
                    <div class="comment-form-fields">
                        <input type="text" name="author" id="comment-author" placeholder="昵称 *" value="<?php $this->remember('author'); ?>" required>
                        <input type="email" name="mail" id="comment-mail" placeholder="邮箱<?php if ($this->options->commentsRequireMail) echo ' *'; ?>" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?>>
                        <input type="url" name="url" id="comment-url" placeholder="网站（选填）" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireUrl): ?> required<?php endif; ?>>
                    </div>
                <?php endif; ?>

                <textarea name="text" id="comment-text" rows="5" placeholder="写下你的想法… Markdown 语法可用" required><?php $this->remember('text'); ?></textarea>

                <div class="comment-form-foot">
                    <p class="comment-hint"><?php _e('支持 Markdown，邮箱不会公开。'); ?></p>
                    <button type="submit" class="btn-primary"><?php _e('提交评论'); ?></button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <p class="comments-closed"><?php _e('评论已关闭。'); ?></p>
    <?php endif; ?>
</section>
