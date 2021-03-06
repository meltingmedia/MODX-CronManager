<?php
/**
 * The base Cron Manager class
 *
 * @package cronmanager
 * @subpackage components
 */

class CronManager
{
    public $modx;
    public $config = array();

    public function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $basePath = $this->modx->getOption('cronmanager.core_path', $config, $this->modx->getOption('core_path') . 'components/cronmanager/');
        $assetsUrl = $this->modx->getOption('cronmanager.assets_url', $config, $this->modx->getOption('assets_url') . 'components/cronmanager/');

        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'chunksPath' => $basePath.'elements/chunks/',
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php',
        ), $config);

        //$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($this->config, true));

        $this->modx->addPackage('cronmanager', $this->config['modelPath']);
    }
}
