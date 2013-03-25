<?php

class Migration
{
    public $modx;
    public $config = array();

    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
        $this->config = array_merge(array(
            'component_name' => null,
            'package_name' => null,
            'namespace' => null,
            'migrations_path' => null,
        ), $options);
    }

    /**
     * Get the current package version (the one being installed)
     *
     * @param array $options
     * @return string The current package version
     */
    public function getVersion($options)
    {
        $tmpVersion = explode('/', $options['topic']);
        foreach ($tmpVersion as $key => $value) {
            if (!$value || $value == '') {
                unset($tmpVersion[$key]);
            }
        }

        return end($tmpVersion);
    }

    /**
     * Get the previous installed version
     *
     * @return string
     */
    public function getPreviousVersion()
    {
        $name = $this->config['package_name'];
        $key = $this->config['component_name'] . '.current_version';
        /** @var modSystemSetting $setting */
        $setting = $this->modx->getObject('modSystemSetting', $key);
        if ($setting) return $setting->get('value');

        $this->modx->addPackage('modx.transport', $this->modx->getOption('core_path') . 'model/');

        $c = $this->modx->newQuery('modTransportPackage');
        $c->where(array(
            'package_name' => $name,
            'installed:!=' => null,
        ));
        $c->sortby('installed', 'DESC');
        $c->limit(1);

        $total = $this->modx->getCount('modTransportPackage', $c);
        if (!$total) return false;
        /** @var $package modTransportPackage */
        $package = $this->modx->getObject('modTransportPackage', $c);
        if (!$package) return false;

        $version = str_replace($name .'-', '', $package->get('signature'));

        return $version;
    }

    /**
     * Returns the path of a migration file
     *
     * @param string $for The version number
     * @return string
     */
    public function getMigration($for)
    {
        return $this->config['migrations_path'] . $for . '.php';
    }

    /**
     * Sets the current version system setting
     *
     * @param string $version
     */
    public function setCurrentVersion($version)
    {
        $key = $this->config['package_name'] . '.current_version';
        /** @var $setting modSystemSetting */
        $setting = $this->modx->getObject('modSystemSetting', $key);
        if (!$setting) {
            $this->modx->log(modX::LOG_LEVEL_INFO, 'No system setting found for previous version, let\'s create it');
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('key', $key);
            $setting->set('namespace', $this->config['namespace']);
            $setting->set('area', 'system');
        }
        $setting->set('value', $version);
        $setting->save();
        unset($setting);
    }
}
