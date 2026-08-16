<?php

namespace TypechoPlugin\StellarCache;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * StellarCache —— 前台页面静态缓存（加速文章/首页访问）
 *
 * @package StellarCache
 * @author Stellar
 * @version 1.0.0
 */
class Plugin implements PluginInterface
{
    private const TTL = 300; /* 缓存 5 分钟 */

    public static function activate()
    {
        \Typecho\Plugin::factory('index.php')->begin = __CLASS__ . '::begin';
        \Typecho\Plugin::factory('index.php')->end = __CLASS__ . '::end';
        return '前台页面缓存已启用（5 分钟静态化，加速访问）';
    }

    public static function deactivate()
    {
    }

    public static function config(Form $form)
    {
    }

    public static function personalConfig(Form $form)
    {
    }

    public static function begin()
    {
        if (!self::cacheable()) {
            return;
        }
        $file = self::file();
        if (is_file($file) && time() - filemtime($file) < self::TTL) {
            echo file_get_contents($file);
            exit;
        }
        ob_start();
    }

    public static function end()
    {
        if (!self::cacheable()) {
            return;
        }
        $html = ob_get_clean();
        $dir = dirname(self::file());
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        @file_put_contents(self::file(), $html);
        echo $html;
    }

    private static function cacheable(): bool
    {
        $request = \Typecho\Request::getInstance();
        $uri = (string) $request->getRequestUri();
        if (!$request->isGet()) {
            return false;
        }
        if (\Typecho\Cookie::get('__typecho_uid')) {
            return false; /* 登录用户不缓存 */
        }
        if (preg_match('#/(admin|action|install|usr/plugins|usr/themes)/#', $uri)) {
            return false;
        }
        return true;
    }

    private static function file(): string
    {
        return __TYPECHO_ROOT_DIR__ . '/usr/plugins/StellarCache/cache/'
            . md5((string) \Typecho\Request::getInstance()->getRequestUri()) . '.html';
    }
}
