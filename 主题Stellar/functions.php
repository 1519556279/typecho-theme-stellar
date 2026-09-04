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

    /* ---------- 站点 Logo ---------- */
    $logoIcon = new Typecho_Widget_Helper_Form_Element_Select(
        'logoIcon',
        array(
            'star'   => '五角星（默认）',
            'letter' => '字母（站点名首字母）',
            'heart'  => '爱心',
            'bolt'   => '闪电',
            'book'   => '书本',
            'moon'   => '月亮',
            'flower' => '花朵',
        ),
        'star',
        _t('站点 Logo 图标'),
        _t('导航栏与浏览器标签页显示的图标样式')
    );
    $form->addInput($logoIcon);

    $logoImage = new Typecho_Widget_Helper_Form_Element_Text(
        'logoImage', NULL, '', _t('Logo 图片地址'),
        _t('填图片 URL 后优先使用图片（支持 png/jpg/svg），覆盖上方图标。留空则用图标。可上传图片后粘贴链接')
    );
    $form->addInput($logoImage);
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
 * 内置 Logo 图标 SVG（统一描边风格 + 固定 20px，与默认五角星一致）
 */
function nova_logo_svg($icon)
{
    $paths = array(
        'star'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 4.9L20 8l-3.6 3.9L17.5 18 12 15.2 6.5 18l1.1-6.1L4 8l5.6-1.1z"/></svg>',
        'letter' => '', // 字母图标特殊处理（用首字母文字）
        'heart'  => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20.5S5.5 16.4 3.1 12.9C1.5 10.4 2.7 7 5.5 7c1.6 0 3 .9 3.7 2.3.4-.7 1-1.3 1.8-1.8.4-.2.7-.5 1-.5 2.8 0 4 3.4 2.4 6.4C18.5 16.4 12 20.5 12 20.5z"/></svg>',
        'bolt'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>',
        'book'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
        'moon'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>',
        'flower' => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 9a3 3 0 0 0 0-6 3 3 0 0 0 0 6zm0 6a3 3 0 0 0 0 6 3 3 0 0 0 0-6zM9 12a3 3 0 0 0-6 0 3 3 0 0 0 6 0zm6 0a3 3 0 0 0 6 0 3 3 0 0 0-6 0z"/></svg>',
    );
    return isset($paths[$icon]) ? $paths[$icon] : $paths['star'];
}

/**
 * 站点 Logo 图标 HTML（图片优先，否则内置图标；letter 图标用站点名首字母）
 */
function nova_logo_html($size = 20)
{
    $img = trim((string)nova_option('logoImage', ''));
    if ($img !== '') {
        return '<img class="brand-logo-img" src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars(Typecho_Widget::widget('Widget_Options')->title) . '" width="' . $size . '" height="' . $size . '" style="width:' . $size . 'px;height:' . $size . 'px;border-radius:6px;object-fit:cover;display:block;">';
    }
    $icon = (string)nova_option('logoIcon', 'star');
    if ($icon === 'letter') {
        $ch = mb_substr(Typecho_Widget::widget('Widget_Options')->title, 0, 1, 'UTF-8');
        return '<span class="brand-logo-letter" style="font-size:' . round($size * 1.1) . 'px;font-weight:800;line-height:1;">' . htmlspecialchars($ch) . '</span>';
    }
    return nova_logo_svg($icon);
}

/**
 * favicon 内联 SVG：渐变圆角底 + 白色图形（data-uri 编码，供 <link rel="icon"> 使用）
 */
function nova_favicon_svg($icon)
{
    $title = Typecho_Widget::widget('Widget_Options')->title;
    // 白色图形内容
    if ($icon === 'letter') {
        $ch = mb_substr($title, 0, 1, 'UTF-8');
        $inner = '<text x="12" y="16.5" font-size="13" font-weight="800" fill="#fff" text-anchor="middle" font-family="-apple-system,Segoe UI,PingFang SC,sans-serif">' . htmlspecialchars($ch) . '</text>';
    } else {
        // 复用 nova_logo_svg 的图形，但改为白色描边/填充、放大居中
        $svg = nova_logo_svg($icon);
        // 提取 path，替换颜色为白色
        $svg = str_replace('stroke="currentColor"', 'stroke="#ffffff"', $svg);
        $svg = str_replace('fill="currentColor"', 'fill="#ffffff"', $svg);
        $svg = str_replace('fill="none" stroke="#ffffff"', 'fill="none" stroke="#ffffff"', $svg);
        $svg = preg_replace('#width="[^"]*" height="[^"]*"#', '', $svg);
        $inner = $svg;
    }
    $raw = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="#5b5bd6"/><stop offset="1" stop-color="#8b5cf6"/>'
        . '</linearGradient></defs>'
        . '<rect width="24" height="24" rx="5.5" fill="url(#g)"/>'
        . $inner
        . '</svg>';
    return 'data:image/svg+xml,' . rawurlencode($raw);
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
