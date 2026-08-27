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
        $desc = new \Typecho\Widget\Helper\Form\Element\Text(
            'placeholder', null, '', _t('StellarAdmin 后台美化'),
            _t('提供后台侧边栏/暗色模式/主题面板/编辑器 Markdown 工具等美化功能。'
                . '启用/停用本插件即可开关美化。AI 助手已独立为 StellarAI 插件。')
        );
        $desc->input->setAttribute('disabled', 'disabled');
        $form->addInput($desc);
    }

    public static function personalConfig(Form $form)
    {
    }

    public static function render(string $header): string
    {
        $base = \Typecho\Common::url('usr/plugins/StellarAdmin/assets',
            \Typecho\Widget::widget('Widget_Options')->siteUrl);

        $html = $header;
        $cssV = filemtime(__TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarAdmin/assets/stellar.css');
        $html .= '<link rel="stylesheet" href="' . $base . '/stellar.css?v=' . $cssV . '">' . "\n";
        $jsV = filemtime(__TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarAdmin/assets/stellar.js');
        $html .= '<script src="' . $base . '/stellar.js?v=' . $jsV . '" defer></script>' . "\n";
        return $html;
    }
}
