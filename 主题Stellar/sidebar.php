<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<aside class="sidebar" id="sidebar">
    <!-- 博主卡片 -->
    <div class="widget widget-author reveal">
        <div class="widget-author-head">
            <?php if (nova_option('showAvatar', '0') === '1'): ?>
                <img class="widget-avatar" src="<?php echo nova_avatar_uri($this->options->title, $this->options->title); ?>" alt="<?php $this->options->title(); ?>" loading="lazy">
            <?php endif; ?>
            <div>
                <div class="widget-author-name"><?php $this->options->title(); ?></div>
                <div class="widget-author-desc"><?php echo nova_option('siteDesc', $this->options->description); ?></div>
            </div>
        </div>
        <?php $social = array(
            'socialGithub'   => array('github', 'GitHub'),
            'socialWeibo'    => array('weibo', '微博'),
            'socialTwitter'  => array('x', 'X/Twitter'),
            'socialEmail'    => array('mail', '邮箱'),
            'socialQQ'       => array('qq', 'QQ'),
            'socialWechat'   => array('wechat', '微信'),
            'socialTelegram' => array('telegram', 'Telegram'),
            'socialBili'     => array('bili', '哔哩哔哩'),
        ); ?>
        <?php $hasSocial = false; foreach ($social as $key => $cfg) { if (!empty(nova_option($key))) { $hasSocial = true; break; } } ?>
        <?php if ($hasSocial): ?>
            <div class="widget-social">
                <?php foreach ($social as $key => $cfg): ?>
                    <?php $val = nova_option($key); ?>
                    <?php if (!empty($val)): ?>
                        <?php $type = $cfg[0]; $copy = ''; $tip = ''; ?>
                        <?php if ($type === 'mail'): $href = 'mailto:' . $val; $tip = $val; ?>
                        <?php elseif ($type === 'wechat'): $href = '#'; $copy = $val; $tip = '微信号: ' . $val; ?>
                        <?php elseif ($type === 'qq'): $href = 'https://wpa.qq.com/msgrd?v=3&uin=' . $val . '&site=qq&menu=yes'; $tip = 'QQ: ' . $val; ?>
                        <?php elseif ($type === 'telegram'): $href = 'https://t.me/' . ltrim($val, '@'); $tip = '@' . ltrim($val, '@'); ?>
                        <?php else: $href = $val; $tip = preg_replace('#^https?://#', '', $val); ?>
                        <?php endif; ?>
                        <a href="<?php echo $href; ?>" data-tip="<?php echo htmlspecialchars($tip); ?>"<?php echo $copy ? ' data-copy="' . htmlspecialchars($copy) . '"' : ''; ?><?php echo $type === 'wechat' ? '' : ' target="_blank" rel="noopener"'; ?> title="<?php echo $cfg[1]; ?>">
                            <?php if ($cfg[0] === 'github'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 .5A11.5 11.5 0 0 0 .5 12a11.5 11.5 0 0 0 7.86 10.92c.58.1.79-.25.79-.56v-2.17c-3.2.7-3.87-1.36-3.87-1.36-.53-1.33-1.28-1.69-1.28-1.69-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.76 2.7 1.25 3.36.96.1-.75.4-1.26.73-1.55-2.55-.29-5.23-1.28-5.23-5.68 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11.1 11.1 0 0 1 5.8 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.69 5.38-5.25 5.66.41.36.78 1.06.78 2.14v3.18c0 .31.2.67.8.56A11.5 11.5 0 0 0 23.5 12 11.5 11.5 0 0 0 12 .5z"/></svg>
                            <?php elseif ($cfg[0] === 'weibo'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10.5 10.8c-2.3.4-4 2-3.8 3.6.2 1.5 2.2 2.5 4.5 2.2 2.3-.4 4-2 3.8-3.6-.2-1.5-2.2-2.5-4.5-2.2zm-.2 4.4c-1.1.2-2-.3-2.1-1.1-.1-.8.7-1.6 1.8-1.8 1.1-.2 2 .3 2.1 1.1.1.8-.7 1.6-1.8 1.8z"/><path d="M16.9 5.6c.1-.5-.1-1-.6-1.2-.5-.1-1 .1-1.2.6l-.5 1.8c-.4-.2-.9-.4-1.5-.5C8.9 5.9 5 8.2 4.6 11.4c-.4 3.2 3 5.8 7.5 5.8 4.5 0 8.1-2.5 7.7-5.7-.2-1.7-1.4-3.1-3-3.9l.1-.2zm-2.3 7.6c-.4 1.9-3.2 3.3-6.2 3.2-3-.1-5.3-1.6-4.9-3.4.4-1.8 3.1-3.1 6.1-3 3 .1 5.4 1.5 5 3.2z"/></svg>
                            <?php elseif ($cfg[0] === 'x'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18.9 1.2h3.7l-8.1 9.3L24 22.8h-7.5l-5.9-7.7-6.7 7.7H.2l8.6-9.9L0 1.2h7.7l5.3 7 6-7zm-1.3 19.4h2L6.6 3.3h-2.2l13.2 17.3z"/></svg>
                            <?php elseif ($cfg[0] === 'qq'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2C8.5 2 5.6 4.6 4.9 8c-.3 1.4-.1 2.9.3 4.3-.4.8-1 2.4-1.3 3.7-.3 1.2-.1 2 .8 1.8 1.9-.4 3.2-1.3 4.1-2.2.5.2 1.1.3 1.7.4.4 1.7 1.5 3.1 2.4 4.2-.3 1-.6 1.8-.9 2.6-.6 1.5-.4 2.4 1.2 2.4h1.6c1.6 0 1.8-.9 1.2-2.4-.3-.8-.6-1.6-.9-2.6.9-1.1 2-2.5 2.4-4.2.6-.1 1.2-.2 1.7-.4.9.9 2.2 1.8 4.1 2.2.9.2 1.1-.6.8-1.8-.3-1.3-.9-2.9-1.3-3.7.4-1.4.6-2.9.3-4.3C18.4 4.6 15.5 2 12 2z"/></svg>
                            <?php elseif ($cfg[0] === 'wechat'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9.3 3.5C5.5 3.5 2.5 6 2.5 9.2c0 1.9 1.1 3.6 2.8 4.7l-.7 2.4 2.7-1.4c.4.1.9.2 1.3.2 0-.2-.1-.4-.1-.7 0-3 2.9-5.4 6.4-5.4h.7c-.3-3.4-3.3-5.5-6.3-5.5z"/><path d="M21.5 14.2c0-2.7-2.7-4.9-6-4.9s-6 2.2-6 4.9 2.7 4.9 6 4.9c.7 0 1.4-.1 2-.3l2 1-.5-1.6c1.6-.8 2.5-2.3 2.5-4zm-8.7-.8c-.4 0-.7-.3-.7-.7s.3-.7.7-.7.7.3.7.7-.3.7-.7.7zm5.4 0c-.4 0-.7-.3-.7-.7s.3-.7.7-.7.7.3.7.7-.3.7-.7.7z"/></svg>
                            <?php elseif ($cfg[0] === 'telegram'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21.9 4.6L2.4 11.9c-1.1.4-1 1.4 0 1.7l5 1.6 1.9 5.9c.3 1 1 1 1.6.4l2.7-2.7 5.1 3.8c.9.5 1.6.2 1.8-.8l3.1-15.5c.3-1.3-.5-2-1.7-1.7zM8.5 14.7l9.8-6.2c.4-.2.8.2.5.6l-8.1 7.5-.3 3-1.9-4.9z"/></svg>
                            <?php elseif ($cfg[0] === 'bili'): ?><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M8.5 5.6c-.1 1-.2 2-.2 3H5.7C3.7 8.6 2 10.3 2 12.3v5.4C2 19.6 3.7 21 5.7 21h12.6c2 0 3.7-1.4 3.7-3.3v-5.4c0-2-1.7-3.7-3.7-3.7h-2.6c0-1-.1-2-.2-3-.1-1-.5-1.9-1.1-2.6l-1.3-1.4c-.3-.3-.8-.3-1.1 0-.3.3-.3.8 0 1.1l1.3 1.4c.4.4.6.9.6 1.5 0 .4-.1.8-.3 1.1H8.1c-.2-.3-.3-.7-.3-1.1 0-.6.2-1.1.6-1.5l1.3-1.4c.3-.3.3-.8 0-1.1-.3-.3-.8-.3-1.1 0L7.3 3C6.8 3.7 6.5 4.6 6.5 5.6z"/><circle cx="7.5" cy="13.5" r="1.5"/><circle cx="16.5" cy="13.5" r="1.5"/></svg>
                            <?php else: ?><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 最新文章 -->
    <div class="widget reveal">
        <h3 class="widget-title"><?php _e('最新文章'); ?></h3>
        <ul class="widget-list widget-latest">
            <?php $this->widget('Widget_Contents_Post_Recent')->to($latest); ?>
            <?php while ($latest->next()): ?>
                <li>
                    <a href="<?php $latest->permalink(); ?>">
                        <span class="widget-latest-title"><?php $latest->title(); ?></span>
                        <time><?php $latest->date('m-d'); ?></time>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- 分类 -->
    <div class="widget reveal">
        <h3 class="widget-title"><?php _e('分类'); ?></h3>
        <ul class="widget-list widget-cats">
            <?php $this->widget('Widget_Metas_Category_List')->to($cats); ?>
            <?php while ($cats->next()): ?>
                <li><a href="<?php $cats->permalink(); ?>"><span><?php $cats->name(); ?></span><span class="count"><?php $cats->count(); ?></span></a></li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- 标签云 -->
    <div class="widget reveal">
        <h3 class="widget-title"><?php _e('标签'); ?></h3>
        <?php $this->widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
        <?php if ($tags->have()): ?>
            <div class="widget-tagcloud">
                <?php while ($tags->next()): ?>
                    <a href="<?php $tags->permalink(); ?>" style="font-size:<?php echo round(12 + $tags->count() / 5, 1); ?>px"><?php $tags->name(); ?></a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 友情链接 -->
    <?php $friendList = nova_friends(); ?>
    <?php if (!empty($friendList)): ?>
        <div class="widget reveal">
            <h3 class="widget-title"><?php _e('友情链接'); ?></h3>
            <ul class="widget-list widget-friends">
                <?php foreach ($friendList as $f): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($f['url']); ?>" target="_blank" rel="noopener nofollow">
                            <span class="widget-latest-title"><?php echo htmlspecialchars($f['name']); ?></span>
                            <?php if (!empty($f['desc'])): ?>
                                <span class="widget-friend-desc"><?php echo htmlspecialchars($f['desc']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 近期评论 -->
    <div class="widget reveal">
        <h3 class="widget-title"><?php _e('近期评论'); ?></h3>
        <ul class="widget-list widget-comments">
            <?php $this->widget('Widget_Comments_Recent')->to($recentComments); ?>
            <?php while ($recentComments->next()): ?>
                <li class="widget-comment-item">
                    <a href="<?php $recentComments->permalink(); ?>">
                        <span class="widget-comment-author"><?php if ($recentComments->authorId && $recentComments->authorId == $recentComments->ownerId) { echo htmlspecialchars($this->options->title); } else { $recentComments->author(); } ?></span>
                        <span class="widget-comment-text"><?php $recentComments->excerpt(42); ?></span>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</aside>
