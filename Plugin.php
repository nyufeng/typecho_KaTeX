<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 基于 KaTeX 的数学公式扩展
 *
 * @package KaTeX
 * @author Instrye
 * @version 0.0.1
 * @link https://github.com/Instrye/typecho_KaTeX
 */
class KaTeX_Plugin implements Typecho_Plugin_Interface
{
    private static $KateXVersion = '0.12.0';
    private static $cdnType = 0;

    private static $cdnList = [
        0 => 'https://cdn.jsdelivr.net/npm/katex@{version}/dist/',
        1 => 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/{version}/',
    ];

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'loadJSCSS');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
        $cdnType = new Typecho_Widget_Helper_Form_Element_Radio('cdn_type', ['0'=>_t("jsdelivr"),'1' =>_t("cdnjs"), '2' =>_t("本地")], '0',_t('CDN选择'));
        $form->addInput($cdnType);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function loadJSCSS($a, $b)
    {
        $config = Helper::options()->plugin('KaTeX');
        $cdnSrcArray = self::getCDNUrl($config->cdn_type);
        $header = "\n";
        $header .= '<link rel="stylesheet" href="' . $cdnSrcArray['katex.min.css'] . '" crossorigin="anonymous">' . "\n";
        $header .= '<script defer src="' . $cdnSrcArray['katex.min.js'] . '" crossorigin="anonymous"></script>' . "\n";
        $header .= '<script defer src="' . $cdnSrcArray['contrib/auto-render.min.js'] . '" crossorigin="anonymous"
    onload="renderMathInElement(document.body);"></script>' . "\n";
        echo $header;
    }

    private static function getCDNUrl($cdnType){
        $options = Helper::options();
        $cdnSrcArray = [];
        switch ($cdnType){
            case 0:
                $url = str_replace('{version}', self::$KateXVersion, self::$cdnList[0]);

                break;
            case 1:
                $url = str_replace('{version}', self::$KateXVersion, self::$cdnList[1]);
                break;
            default:
                $url = $options->pluginUrl . '/KaTeX/dist/';
                break;
        }
        $cdnSrcArray['katex.min.css'] = $url . 'katex.min.css';
        $cdnSrcArray['katex.min.js'] = $url . 'katex.min.js';
        $cdnSrcArray['contrib/auto-render.min.js'] = $url . 'contrib/auto-render.min.js';
        return $cdnSrcArray;
    }
}
