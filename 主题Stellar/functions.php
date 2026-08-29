<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 相关文章推荐（同分类，排除当前文章）
 */
function nova_related_posts($archive, $limit = 2)
{
    $db = Typecho_Db::get();
    $options = Typecho_Widget::widget('Widget_Options');

    $row = $db->fetchRow($db->select()->from('table.relationships')
        ->join('table.metas', 'table.metas.mid = table.relationships.mid', Typecho_Db::INNER_JOIN)
        ->where('table.relationships.cid = ?', $archive->cid)
        ->where('table.metas.type = ?', 'category')
        ->limit(1));

    if (!$row) {
        return '';
    }

    $posts = $db->fetchAll($db->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created')
        ->from('table.contents')
        ->join('table.relationships', 'table.relationships.cid = table.contents.cid', Typecho_Db::INNER_JOIN)
        ->where('table.relationships.mid = ?', $row['mid'])
        ->where('table.contents.cid <> ?', $archive->cid)
        ->where('table.contents.type = ?', 'post')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $options->gmtTime)
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit($limit));

    if (empty($posts)) {
        return '';
    }

    $html = '<div class="related-posts"><h3 class="related-title">相关阅读</h3><div class="related-list">';
    foreach ($posts as $p) {
        $url = \Typecho\Router::url('post', array('cid' => $p['cid'], 'slug' => $p['slug']), $options->index);
        $html .= '<a class="related-item" href="' . $url . '"><span class="related-arrow">→</span><span class="related-name">' . htmlspecialchars($p['title']) . '</span></a>';
    }
    $html .= '</div></div>';

    return $html;
}

/**
 * 后台外观设置面板
 */
function themeConfig($form)
{
    $accents = array(
        'default' => '靛紫（默认）',
        'blue'    => '天蓝',
        'emerald' => '翡翠绿',
        'rose'    => '玫瑰粉',
        'amber'   => '琥珀橙',
        'violet'  => '紫罗兰',
    );
    $accent = new Typecho_Widget_Helper_Form_Element_Select('accent', $accents, 'default', _t('主题配色'), _t('切换整套强调色，亮色/暗色模式自动适配'));
    $form->addInput($accent);

    $showAvatar = new Typecho_Widget_Helper_Form_Element_Select('showAvatar', array('1' => '显示', '0' => '隐藏'), '0', _t('博主头像'), _t('首页与侧边栏是否显示博主头像（评论头像不受影响）'));
    $form->addInput($showAvatar);

    $desc = new Typecho_Widget_Helper_Form_Element_Text('siteDesc', NULL, '记录思考，分享美好', _t('站点简介'), _t('显示在首页 Hero 区域与侧边栏'));
    $form->addInput($desc);

    $friends = new Typecho_Widget_Helper_Form_Element_Textarea('friends', NULL, '', _t('友情链接'), _t('每行一个，格式：名称|链接|简介（简介可省略）。如：Typecho|https://typecho.org|轻量博客程序'));
    $form->addInput($friends);

    $github = new Typecho_Widget_Helper_Form_Element_Text('socialGithub', NULL, '', _t('GitHub 链接'), _t('留空则不显示'));
    $form->addInput($github);

    $weibo = new Typecho_Widget_Helper_Form_Element_Text('socialWeibo', NULL, '', _t('微博链接'), _t('留空则不显示'));
    $form->addInput($weibo);

    $twitter = new Typecho_Widget_Helper_Form_Element_Text('socialTwitter', NULL, '', _t('Twitter/X 链接'), _t('留空则不显示'));
    $form->addInput($twitter);

    $email = new Typecho_Widget_Helper_Form_Element_Text('socialEmail', NULL, '', _t('邮箱地址'), _t('留空则不显示'));
    $form->addInput($email);

    $qq = new Typecho_Widget_Helper_Form_Element_Text('socialQQ', NULL, '', _t('QQ 号'), _t('留空则不显示，点击跳转 QQ 临时会话'));
    $form->addInput($qq);

    $wechat = new Typecho_Widget_Helper_Form_Element_Text('socialWechat', NULL, '', _t('微信号'), _t('留空则不显示，点击复制微信号'));
    $form->addInput($wechat);

    $telegram = new Typecho_Widget_Helper_Form_Element_Text('socialTelegram', NULL, '', _t('Telegram 用户名'), _t('留空则不显示，填用户名如 kaka，自动拼接 t.me/kaka'));
    $form->addInput($telegram);

    $bili = new Typecho_Widget_Helper_Form_Element_Text('socialBili', NULL, '', _t('哔哩哔哩主页链接'), _t('留空则不显示'));
    $form->addInput($bili);

    $showSidebar = new Typecho_Widget_Helper_Form_Element_Select('showSidebar', array('1' => '显示', '0' => '隐藏'), '1', _t('侧边栏'), _t('桌面端右侧边栏开关'));
    $form->addInput($showSidebar);
}

/**
 * 解析友情链接配置（每行：名称|链接|简介）
 */
function nova_friends()
{
    $raw = trim((string)nova_option('friends'));
    if ('' === $raw) {
        return array();
    }
    $list = array();
    foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
        $line = trim($line);
        if ('' === $line || 0 === strpos($line, '#')) {
            continue;
        }
        $parts = array_map('trim', explode('|', $line));
        $name = $parts[0] ?? '';
        $url = $parts[1] ?? '';
        if ('' === $name || '' === $url) {
            continue;
        }
        $list[] = array(
            'name' => $name,
            'url'  => $url,
            'desc' => $parts[2] ?? '',
        );
    }
    return $list;
}

/**
 * 获取主题设置项
 */
function nova_option($key, $default = '')
{
    return Typecho_Widget::widget('Widget_Options')->{$key} ?: $default;
}

/**
 * 统计文章字数
 */
function nova_word_count($content)
{
    $text = preg_replace('/<[^>]+>/', '', $content);
    $text = trim(preg_replace('/\s+/', ' ', $text));
    return mb_strlen($text, 'UTF-8');
}

/**
 * 估算阅读时间（分钟）
 */
function nova_reading_time($content)
{
    $count = nova_word_count($content);
    $minutes = (int) max(1, ceil($count / 300));
    return $minutes;
}

/**
 * 提取文章首图（无则返回空）
 */
function nova_cover($archive)
{
    $content = $archive->content;
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $m)) {
        return $m[1];
    }
    // 附件字段
    $attach = $archive->fields->cover;
    if (!empty($attach)) {
        return $attach;
    }
    return '';
}

/**
 * 本地默认头像：按邮箱/昵称生成渐变色字母头像（SVG data-uri，完全本地、不依赖外网）
 */
function nova_avatar_uri($name, $mail = '')
{
    $seed = $mail ?: $name;
    $palettes = array(
        array('#6366f1', '#8b5cf6'),
        array('#0ea5e9', '#6366f1'),
        array('#10b981', '#0ea5e9'),
        array('#f59e0b', '#ef4444'),
        array('#ec4899', '#8b5cf6'),
        array('#14b8a6', '#10b981'),
        array('#f43f5e', '#ec4899'),
        array('#f97316', '#f59e0b'),
    );
    $i = abs(crc32($seed)) % count($palettes);
    list($c1, $c2) = $palettes[$i];
    $letter = mb_strtoupper(mb_substr(trim((string)$name), 0, 1, 'UTF-8'));
    if ('' === $letter) {
        $letter = '?';
    }
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/>'
        . '</linearGradient></defs>'
        . '<rect width="100" height="100" rx="26" fill="url(#g)"/>'
        . '<text x="50" y="50" dy=".36em" font-family="Arial, sans-serif" font-size="46" font-weight="700" fill="#fff" text-anchor="middle">'
        . htmlspecialchars($letter, ENT_QUOTES, 'UTF-8') . '</text></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * 评论头像：先显示本地默认字母头像（永不破图），QQ 邮箱用 QQ 头像、其它邮箱用 cravatar 真实头像，
 * 由 JS 预加载 data-src 成功后平滑替换；真实头像不存在或不可达时保持本地默认头像
 */
function nova_comment_avatar($comments)
{
    $mail = is_string($comments->mail) ? trim($comments->mail) : '';
    $name = (string)$comments->author;
    $local = nova_avatar_uri($name, $mail);
    if (preg_match('/^(\d{5,12})@qq\.com$/i', $mail, $m)) {
        $real = 'https://q1.qlogo.cn/g?b=qq&amp;nk=' . $m[1] . '&amp;s=100';
    } else {
        $hash = md5(strtolower($mail));
        $real = 'https://cravatar.cn/avatar/' . $hash . '?d=404&s=100';
    }
    echo '<img src="' . $local . '" data-src="' . $real . '" data-fallback="' . $local . '" alt="';
    $comments->author();
    echo '" loading="lazy">';
}

/**
 * 自定义评论列表渲染回调
 */
function threadedComments($comments, $options)
{
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        } else {
            $commentClass .= ' comment-by-user';
        }
    }
    ?>
    <li id="<?php $comments->theId(); ?>" class="comment-item<?php echo $commentClass;
    if ($comments->levels > 0) {
        echo ' comment-child';
    } ?>">
        <div class="comment-avatar">
            <?php nova_comment_avatar($comments); ?>
        </div>
        <div class="comment-main">
            <div class="comment-head">
                <span class="comment-author"><?php
                    if ($comments->authorId && $comments->authorId == $comments->ownerId) {
                        echo htmlspecialchars(Typecho_Widget::widget('Widget_Options')->title);
                    } else {
                        $comments->author();
                    }
                ?></span>
                <time datetime="<?php $comments->date('c'); ?>"><?php $comments->date('Y-m-d H:i'); ?></time>
            </div>
            <div class="comment-text"><?php $comments->content(); ?></div>
            <div class="comment-actions">
                <?php if ('approved' !== $comments->status): ?>
                    <em class="comment-pending"><?php _e('评论正等待审核'); ?></em>
                <?php endif; ?>
                <?php
                // 注意：$comments->options 在回调中为 null（Typecho 1.3 受保护属性），需用全局 Options
                $novaCommentOpt = Typecho_Widget::widget('Widget_Options');
                ?>
                <?php if ($novaCommentOpt->commentsThreaded && !$comments->isTopLevel && $comments->parameter->allowComment): ?>
                    <button type="button" class="comment-reply-btn" data-coid="<?php $comments->coid(); ?>"><?php _e('回复'); ?></button>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($comments->children): ?>
            <ol class="comment-children"><?php $comments->threadedComments(); ?></ol>
        <?php endif; ?>
    </li>
    <?php
}

/**
 * 上一篇 / 下一篇
 */
function nova_neighbors($archive)
{
    $db = Typecho_Db::get();
    $options = Typecho_Widget::widget('Widget_Options');

    $prev = $db->fetchRow($db->select()->from('table.contents')
        ->where('table.contents.cid < ?', $archive->cid)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', 'post')
        ->where('table.contents.created < ?', $options->gmtTime)
        ->order('table.contents.cid', Typecho_Db::SORT_DESC)
        ->limit(1));

    $next = $db->fetchRow($db->select()->from('table.contents')
        ->where('table.contents.cid > ?', $archive->cid)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', 'post')
        ->where('table.contents.created < ?', $options->gmtTime)
        ->order('table.contents.cid', Typecho_Db::SORT_ASC)
        ->limit(1));

    $html = '<div class="post-nav">';
    if ($prev) {
        $html .= '<a class="post-nav-item post-nav-prev" href="' . \Typecho\Router::url(
            'post', array('cid' => $prev['cid'], 'slug' => $prev['slug']), $options->index
        ) . '"><span class="post-nav-label">← 上一篇</span><span class="post-nav-title">' . htmlspecialchars($prev['title']) . '</span></a>';
    } else {
        $html .= '<div class="post-nav-item post-nav-prev is-empty"><span class="post-nav-label">← 上一篇</span><span class="post-nav-title">没有更早的文章</span></div>';
    }
    if ($next) {
        $html .= '<a class="post-nav-item post-nav-next" href="' . \Typecho\Router::url(
            'post', array('cid' => $next['cid'], 'slug' => $next['slug']), $options->index
        ) . '"><span class="post-nav-label">下一篇 →</span><span class="post-nav-title">' . htmlspecialchars($next['title']) . '</span></a>';
    } else {
        $html .= '<div class="post-nav-item post-nav-next is-empty"><span class="post-nav-label">下一篇 →</span><span class="post-nav-title">没有更新的文章</span></div>';
    }
    $html .= '</div>';

    return $html;
}
