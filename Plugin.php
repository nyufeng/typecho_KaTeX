<?php

namespace TypechoPlugin\typecho_KaTeX;

use Typecho\Plugin\Exception;
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Radio;
use Utils\Helper;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 基于 KaTeX 的数学公式扩展
 *
 * @package typecho_KaTeX
 * @author Feng
 * @version 0.0.2
 * @link https://github.com/nyufeng/typecho_KaTeX
 */
class Plugin implements PluginInterface
{
    private static $KateXVersion = '0.15.1';
    private static $cdnType = 0;

    private static $cdnList = [
        0 => 'https://cdn.jsdelivr.net/npm/katex@{version}/dist/',
        1 => 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/{version}/',
    ];

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        \Typecho\Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'loadJSCSS');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Form $form 配置面板
     * @return void
     */
    public static function config(Form $form)
    {
        /** 分类名称 */
        $cdnType = new Radio('cdn_type', ['0'=>_t("jsdelivr"),'1' =>_t("cdnjs"), '2' =>_t("本地")], '0',_t('CDN选择'));
        $version = new Form\Element\Text('cdn_version', null, null, _t('设置版本'));
        $form->addInput($cdnType);
        $form->addInput($version);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Form $form
     * @return void
     */
    public static function personalConfig(Form $form){}

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public static function loadJSCSS()
    {
        $config = Helper::options()->plugin('typecho_KaTeX');
        $cdnSrcArray = self::getCDNUrl($config->cdn_type, $config->version ?? self::$KateXVersion);
        $header = "\n";
        $header .= '<link rel="stylesheet" href="' . $cdnSrcArray['katex.min.css'] . '" crossorigin="anonymous">' . "\n";
        $header .= '<script defer src="' . $cdnSrcArray['katex.min.js'] . '" crossorigin="anonymous"></script>' . "\n";
        $header .= '<script defer src="' . $cdnSrcArray['contrib/auto-render.min.js'] . '" crossorigin="anonymous"
    onload="renderMathInElement(document.body);"></script>' . "\n";
        echo $header;
    }

    private static function getCDNUrl($cdnType, $version): array
    {
        $options = Helper::options();
        $cdnSrcArray = [];
        switch ($cdnType){
            case 0:
                $url = str_replace('{version}', $version, self::$cdnList[0]);

                break;
            case 1:
                $url = str_replace('{version}', $version, self::$cdnList[1]);
                break;
            default:
                $url = $options->pluginUrl . '/typecho_KaTeX/dist/';
                break;
        }
        $cdnSrcArray['katex.min.css'] = $url . 'katex.min.css';
        $cdnSrcArray['katex.min.js'] = $url . 'katex.min.js';
        $cdnSrcArray['contrib/auto-render.min.js'] = $url . 'contrib/auto-render.min.js';
        return $cdnSrcArray;
    }
}
