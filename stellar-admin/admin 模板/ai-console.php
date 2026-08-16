<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<main class="main">
    <div class="sa-content sa-ai">
        <div class="sa-page-head">
            <h2>AI 助手</h2>
            <div class="sa-ai-meta">
                <span class="sa-ai-status" id="sa-ai-status"></span>
                <button type="button" class="btn btn-s" id="sa-ai-test">测试连接</button>
            </div>
        </div>

        <div class="sa-ai-grid">
            <div class="sa-ai-main">
                <div class="sa-chat" id="sa-chat">
                    <div class="sa-chat-msg bot">
                        <span class="sa-chat-avatar">✦</span>
                        <div class="sa-chat-bubble">你好！我是 <b>Stellar AI</b> 写作助手。<br>可以帮你：<br>· 生成文章（给主题或要点即可）<br>· 润色 / 改写 / 翻译<br>· 提取摘要、推荐标签<br>· 解释代码或 Markdown 语法<br><br>试试下面的快捷指令，或直接输入你的需求。</div>
                    </div>
                </div>
                <div class="sa-chat-tools">
                    <button type="button" class="sa-cmd-chip" data-prompt="请为「搭建个人博客」写一篇完整的 Markdown 文章，包含标题、小标题和要点。">📝 生成文章</button>
                    <button type="button" class="sa-cmd-chip" data-prompt="请帮我润色下面这段文字，保持原意：">✨ 润色</button>
                    <button type="button" class="sa-cmd-chip" data-prompt="请为下面这篇文章提取一段 50 字以内的摘要：">📄 提取摘要</button>
                    <button type="button" class="sa-cmd-chip" data-prompt="请为下面这篇文章推荐 5 个中文标签（逗号分隔）：">🏷️ 标签建议</button>
                    <button type="button" class="sa-cmd-chip" data-prompt="请把下面这篇文章翻译成英文：">🌐 翻译英文</button>
                </div>
                <div class="sa-chat-input">
                    <textarea id="sa-chat-text" rows="3" placeholder="输入你的需求，Ctrl+Enter 发送…"></textarea>
                    <button type="button" class="btn btn-primary" id="sa-chat-send">发送</button>
                </div>
            </div>

            <div class="sa-ai-side">
                <div class="sa-ai-card">
                    <h4>⚡ 命令执行</h4>
                    <p class="sa-ai-desc">用自然语言操作后台：发布 / 修改 / 删除文章、查看统计、改站点信息。</p>
                    <textarea id="sa-cmd-input" rows="2" placeholder="例如：发布一篇标题为「你好世界」的文章"></textarea>
                    <button type="button" class="btn btn-primary btn-s" id="sa-cmd-send">执行</button>
                    <div class="sa-cmd-result" id="sa-cmd-result"></div>
                </div>
                <div class="sa-ai-card">
                    <h4>💡 使用提示</h4>
                    <ul class="sa-ai-tips">
                        <li>AI 服务商与模型在 <b>插件 → StellarAdmin → 设置</b> 中配置</li>
                        <li>写文章页有「AI 润色」「智能优化」按钮与 Markdown 语法插入</li>
                        <li>对话有上下文记忆，可连续追问</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
