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
        $base = \Typecho\Common::url('usr/plugins/StellarAdmin/assets',
            \Typecho\Widget::widget('Widget_Options')->siteUrl);
        $cssV = filemtime(__TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarAdmin/assets/stellar.css');
        $jsV = filemtime(__TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarAdmin/assets/stellar.js');

        return $header
            . '<link rel="stylesheet" href="' . $base . '/stellar.css?v=' . $cssV . '">' . "\n"
            . '<script src="' . $base . '/stellar.js?v=' . $jsV . '" defer></script>' . "\n";
    }
}
