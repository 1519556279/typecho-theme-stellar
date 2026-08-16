# Stellar（星辰）Typecho 主题 更新日志

## v1.1.7（2026-08-17）
- **fix**: 分类页 500 —— 空归档（分类下无文章）时 Archive row 为空，header.php 的 `og:url` 调 `$this->permalink()` 导致 `Router::url(NULL)` TypeError。`og:url` 改用 `$this->request->getRequestUrl()`（当前请求 URL，更标准，空归档/404 均安全）
- **fix**: 清理 `tags()` 占位符 —— 无标签的文章卡片不再显示 `none` 字样（`'none'` → `''`，index/archive/post 三处）

## v1.1.6（2026-08-17）
- **fix**: 移动端右侧溢出根治 —— `white-space: nowrap` 标题（侧栏最新文章、上一篇/下一篇）按 min-content 撑开 grid/flex 轨道（首页 422px、文章页 398px，均超 375px 视口，真机可横向滚动）
- 修复：`.post-nav-item` / `.widget` / `.widget-list li a` / `.related-item` 加 `min-width: 0`
- 修复：`.layout` / `.sidebar` / `.post-nav` / `.related-list` / `.friends-grid` 的 `1fr` 列定义全部改为 `minmax(0, 1fr)`

## v1.1.5（2026-08-16）
- 修复：`.post-excerpt` / `.post-title` 加 `overflow-wrap: break-word` + `word-break: break-word`（长单词/URL 换行）
- 修复：抽屉 drawer 隐藏态加 `visibility: hidden`（iOS 渲染残留）

## v1.1.4（2026-08-16）
- 修复：抽屉 drawer 隐藏态视觉残留

## v1.1.3（2026-08-16）
- iOS Safari 横向滚动加固：`html, body { overflow-x: hidden; max-width: 100% }` + `body { position: relative }`

## v1.1.2（2026-08-16）
- `.post-content` 加 `overflow-wrap: break-word`（长词/URL 防溢出）

## v1.1.1（2026-08-16）
- 修复：移动端横向溢出（`html, body { overflow-x: hidden }`）
- CSS/JS 版本参数（?v= 强制刷新缓存）
