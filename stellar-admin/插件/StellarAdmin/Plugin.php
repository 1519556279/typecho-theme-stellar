<?php

namespace TypechoPlugin\StellarAdmin;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Stellar Admin —— 星辰风格后台美化（与前台 Stellar 主题同风格：毛玻璃/渐变/暗色/动效）
 *
 * @package StellarAdmin
 * @author Stellar
 * @version 1.0.0
 */
class Plugin implements PluginInterface
{
    public static function activate()
    {
        \Typecho\Plugin::factory('admin/header.php')->header = __CLASS__ . '::render';
        return 'Stellar 后台美化已启用，刷新页面即可看到效果';
    }

    public static function deactivate()
    {
    }

    public static function config(Form $form)
    {
        $ui = new \Typecho\Widget\Helper\Form\Element\Select(
            'sa_enable_ui',
            ['1' => '启用', '0' => '停用'],
            '1',
            _t('后台美化'),
            _t('停用后不再加载美化样式（侧边栏/暗色模式/编辑器 Markdown 工具），后台恢复 Typecho 原版外观')
        );
        $ai = new \Typecho\Widget\Helper\Form\Element\Select(
            'sa_enable_ai',
            ['1' => '启用', '0' => '停用'],
            '1',
            _t('AI 助手'),
            _t('停用后 AI 对话/命令面板/润色全部关闭；可单独停用任一功能，互不影响（如换其他美化插件时只保留 AI）')
        );
        $provider = new \Typecho\Widget\Helper\Form\Element\Select(
            'ai_provider',
            [
                'zhipu' => '智谱 AI（默认免费 glm-4.7-flash）',
                'deepseek' => 'DeepSeek',
                'qwen' => '通义千问（DashScope）',
                'kimi' => 'Kimi（月之暗面）',
                'openai' => 'OpenAI 兼容（自定义）',
            ],
            'zhipu',
            _t('AI 服务商'),
            _t('用于「AI 润色」与「命令面板」')
        );
        $key = new \Typecho\Widget\Helper\Form\Element\Text(
            'ai_key', null, '', _t('API Key'),
            _t('对应服务商的 API Key。智谱可到 open.bigmodel.cn 申请（glm-4.7-flash 免费）')
        );
        $base = new \Typecho\Widget\Helper\Form\Element\Text(
            'ai_base_url', null, '', _t('Base URL（可选）'),
            _t('留空使用服务商默认地址；OpenAI 兼容服务需填写，如 https://api.xxx.com/v1')
        );
        $model = new \Typecho\Widget\Helper\Form\Element\Text(
            'ai_model', null, '', _t('模型名（可选）'),
            _t('留空使用服务商默认模型；也可点「检测可用模型」列出该服务商全部可用模型，点击即可填入')
            . '<br><button type="button" class="btn btn-s" id="sa-detect-models">检测可用模型</button>'
            . '<div id="sa-model-list" style="margin-top:10px"></div>'
        );
        $form->addInput($ui);
        $form->addInput($ai);
        $form->addInput($provider);
        $form->addInput($key);
        $form->addInput($base);
        $form->addInput($model);
    }

    public static function personalConfig(Form $form)
    {
    }

    public static function render(string $header): string
    {
        $cfg = \Typecho\Widget::widget('Widget_Options')->plugin('StellarAdmin');
        $ui = ($cfg->sa_enable_ui ?? '1') !== '0';
        $ai = ($cfg->sa_enable_ai ?? '1') !== '0';
        $base = \Typecho\Common::url('usr/plugins/StellarAdmin/assets',
            \Typecho\Widget::widget('Widget_Options')->siteUrl);

        $html = $header;
        if ($ui || $ai) {
            /* 传给 stellar.js 的功能开关（美化 / AI 独立控制） */
            $html .= '<script>window.__saFlags={ui:' . ($ui ? 'true' : 'false') . ',ai:' . ($ai ? 'true' : 'false') . '};</script>' . "\n";
        }
        if ($ui) {
            $cssV = filemtime(__TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarAdmin/assets/stellar.css');
            $html .= '<link rel="stylesheet" href="' . $base . '/stellar.css?v=' . $cssV . '">' . "\n";
        }
        if ($ui || $ai) {
            $jsV = filemtime(__TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarAdmin/assets/stellar.js');
            $html .= '<script src="' . $base . '/stellar.js?v=' . $jsV . '" defer></script>' . "\n";
        }
        return $html;
    }
}
