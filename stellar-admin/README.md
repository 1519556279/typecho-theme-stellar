# Stellar Admin · Typecho 星辰后台美化 + AI 助手

> 为 Typecho 博客系统打造的**前沿风格后台**：侧边栏布局、毛玻璃设计、暗色模式、6 套配色、全新文章列表与设置表单，内置 **AI 润色** 与 **命令面板**。与前台主题 **Stellar（星辰）** 同品牌体系。
## ✨ 特性

**布局（完全自研，弃用官方老框架）**
- 左侧固定侧边栏（240px），**可折叠为图标栏**（hover 提示，状态记忆）
- **PJAX 局部刷新**：点击菜单**左侧栏不动、右侧内容平滑切换**（告别整页闪白），支持浏览器前进/后退；复杂编辑页自动整页跳转兜底
- 顶部工具栏：面包屑 + 主题/帮助/命令按钮 + 用户菜单
- 移动端（<900px）自动切换为抽屉式侧边栏；编辑器全屏自动隐藏侧边栏

**视觉**
- 毛玻璃侧边栏与顶栏、渐变强调色、圆角卡片体系
- **暗色模式**：View Transition 圆形扩散动画，跟随系统 + 手动切换 + 记忆
- **6 套配色**（靛紫/天蓝/翡翠/玫瑰/琥珀/紫罗兰），亮暗自动适配
- 导航菜单自动匹配 22 个线性图标

**写文章引导**
- 编辑器上方「**Markdown 语法**」按钮：展开语法速查（标题 #/##、粗体、列表、引用、代码块、链接、表格、`<!--more-->` 摘要截断等），再也不用查文档
- 顶栏「**帮助**」按钮：按当前页面显示操作说明（发布流程、状态含义、设置项解释、永久链接格式等）

**🤖 AI 润色助手**
- 写文章时点「**AI 润色**」，选中片段或全文交给大模型优化表达（保持 Markdown 结构），确认后一键替换
- 支持主流 API：**智谱（免费 glm-4.7-flash）/ DeepSeek / 通义千问 / Kimi（kimi-k3）/ OpenAI 兼容**，插件设置里选服务商填 Key
- 设置面板带「**检测可用模型**」按钮：自动列出该服务商全部可用模型，点击填入（默认免费模型置顶标注）
- **AI 润色 / 智能优化 / 撤回 / 重做**：润色不满意可连续撤回，重做恢复

**⚡ 命令面板（宝塔式 API 调用）**
- 顶栏 `</>` 按钮打开命令面板，**自然语言**下达指令，AI 解析后自动执行
- 支持动作：`create_post` 发布文章（含分类/标签）、`update_post` 修改、`delete_post` 删除、`list_posts` 查看、`get_stats` 统计、`update_option` 改站点信息
- 示例：「发布一篇标题为你好世界的文章」「查看最近的5篇文章」「把站点标题改为我的博客」
- 仅管理员可用；配置项白名单限制，操作结果逐条回显

**🤖 独立 AI 助手页面（ai-console.php）**
- 侧边栏「AI 助手」入口进入，大屏三栏布局：
  - **对话区**：多轮对话（有上下文记忆），快捷指令一键填入（生成文章/润色/摘要/标签/翻译）
  - **命令执行卡**：自然语言操作后台（发布/修改/删除文章、统计、改站点信息）
  - **连接状态**：实时显示服务商与模型，一键测试连接
- AI 生成的文章带「复制全文」按钮
- **🌐 联网功能**：对话中直接发链接（如「看看 https://xxx.com」），AI 自动抓取网页并基于内容回答（SSRF 防护：拒绝内网/回环/重定向绕过）

**页面**
- 文章管理：卡片式列表（状态徽标、单行删除、批量操作、全选、分页、空状态）
- 设置页：两栏卡片化表单；登录页：星空渐变 + 毛玻璃卡片 + 星辰粒子

## 📦 适用环境

- Typecho **1.3.x**（其他版本需验证）
- PHP 7.4+（建议 8.x，需启用 curl 扩展）
- 现代浏览器（Chrome / Edge / Firefox / Safari）

## 🚀 部署方式（说明）

> 本包由两部分组成：**admin 模板（替换文件）** + **插件（加入文件）**。
> 替换模板是为了摆脱官方老布局骨架（官方 2008 年布局）；Typecho 升级时会还原官方模板，**升级后重新覆盖 admin 模板即可**（建议先备份 `admin/`）。

### 步骤 1：备份（重要）

```bash
# 在服务器站点根目录执行
cp -r admin admin.bak
```

### 步骤 2：替换 admin 模板（7 个文件）

把 `admin 模板/` 里的文件上传覆盖到站点根目录 `admin/`：

| 文件 | 作用 |
| --- | --- |
| `header.php` | 页面头 + 应用壳 |
| `menu.php` | 侧边栏 + 顶栏（含帮助/命令按钮、AI 助手入口） |
| `footer.php` | 页面尾 |
| `page-title.php` | 页头 |
| `copyright.php` | 页脚版权 |
| `manage-posts.php` | 文章管理（卡片列表） |
| `write-post.php` | 写文章页（Markdown 引导插入 + AI 润色/智能优化/撤回重做） |
| `ai-console.php` | **独立 AI 助手页面**（对话/命令/连接状态） |

### 步骤 3：加入插件

把 `插件/StellarAdmin/` 整个目录上传到站点根目录 `usr/plugins/` 下：

```
usr/plugins/StellarAdmin/
├── Plugin.php       插件主文件（注入样式/脚本 + AI 配置面板）
├── ai.php           AI 端点（润色 + 命令执行，校验后台管理员会话）
└── assets/
    ├── stellar.css  全部样式（新框架 + 组件库）
    └── stellar.js   全部交互（主题/折叠/帮助/AI/命令）
```

### 步骤 4：启用插件并配置 AI

后台 → 插件 → 找到 **StellarAdmin** → **启用** → **设置**：

| 配置项 | 说明 |
| --- | --- |
| AI 服务商 | 智谱（免费）/ DeepSeek / 通义千问 / Kimi / OpenAI 兼容 |
| API Key | 对应服务商的 Key（智谱 open.bigmodel.cn 申请，glm-4.7-flash 免费） |
| Base URL | 留空用默认；OpenAI 兼容服务必填 |
| 模型名 | 留空用默认（智谱 glm-4.7-flash、DeepSeek deepseek-chat、Kimi kimi-k3）；或点「检测可用模型」选择 |

### 验证

- 写文章页：点「Markdown 语法」看引导，「AI 润色」试试智能改写
- 顶栏 `?` 帮助按钮：按页面显示操作说明
- 顶栏 `</>` 命令按钮：输入「查看站点统计」试试

## 📁 目录介绍

```
stellar-admin/
├── README.md              本说明文档（部署 + 使用 + API）
├── LICENSE.txt
├── 插件/                   （加入文件，放 usr/plugins/ 下）
│   └── StellarAdmin/
│       ├── Plugin.php      插件入口：admin/header.php 钩子注入 CSS/JS；设置面板配置 AI
│       ├── ai.php          AI 端点：POST {action: polish|command}，会话鉴权，多服务商
│       └── assets/         stellar.css 新框架样式 / stellar.js 全部交互
└── admin 模板/              （替换文件，覆盖 admin/ 下同名文件）
    ├── header.php / footer.php / page-title.php / copyright.php   页面壳
    ├── menu.php            侧边栏 + 顶栏（品牌区显示站点标题）
    ├── manage-posts.php    文章管理卡片列表
    └── write-post.php      写文章页（引导面板 + AI 按钮）
```

## 🔌 AI 接口说明（ai.php）

| 动作 | 请求体 | 说明 |
| --- | --- | --- |
| `polish` | `{"action":"polish","text":"原文","mode":"通用"}` | AI 润色（mode: 通用/简洁/正式/口语化/auto 智能优化），返回 `{"ok":true,"text":"润色后"}` |
| `command` | `{"action":"command","command":"发布一篇…"}` | AI 解析并执行，返回结果数组 |
| `chat` | `{"action":"chat","messages":[{"role":"user","content":"…"}]}` | 多轮对话；消息含 URL 时自动联网抓取，返回 `web` 元信息 |
| `models` | `{"action":"models"}` | 检测当前服务商可用模型列表（默认模型置顶） |
| `ping` | `{"action":"ping"}` | 连接与配置检查 |

### 附带：StellarCache 前台缓存插件（可选）

`插件/StellarCache/` —— 前台页面 HTML 静态缓存（5 分钟），显著加速文章/首页访问（宝塔等动态环境推荐）：

```
usr/plugins/StellarCache/
└── Plugin.php   index.php begin/end 钩子：GET + 非登录页面缓存到 cache/ 目录
```

- 启用：后台 → 插件 → StellarCache → 启用（无配置项）
- 登录用户、后台、action 请求自动跳过缓存
- 发布文章后最多 5 分钟旧缓存（TTL 可改 Plugin.php 顶部 `TTL` 常量）

- 鉴权：仅 Typecho 后台已登录的 **administrator**（Cookie 会话）+ **同源校验**（Origin/Referer，防 CSRF）
- 联网抓取 SSRF 防护：拒绝内网/回环/保留地址（IPv4+IPv6）、逐跳校验重定向、CURLOPT_RESOLVE 固定 IP 防 DNS 重绑定
- 执行器动作白名单：create_post / update_post / delete_post / list_posts / get_stats / update_option（key 仅限 title/description/keywords）
- 直接调用示例：`POST /usr/plugins/StellarAdmin/ai.php`，Header `Content-Type: application/json`，携带登录 Cookie

## 🗑 卸载

1. 后台停用插件，删除 `usr/plugins/StellarAdmin/`
2. 用 `admin.bak` 恢复官方模板
3. 前台主题不受影响

## 📄 License

MIT
