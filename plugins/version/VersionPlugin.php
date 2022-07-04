<?php

namespace Sonder\Plugins;

final class VersionPlugin
{
    private const CONFIG_FILE_PATHS = [
        __DIR__ . '/../../../config/cms.json',
        __DIR__ . '/../../config/cms.json'
    ];

    /**
     * @var array
     */
    private array $_configValues = [];

    final public function __construct()
    {
        foreach (VersionPlugin::CONFIG_FILE_PATHS as $configFilePath) {
            if (file_exists($configFilePath) && is_file($configFilePath)) {
                continue;
            }

            $configValues = file_get_contents($configFilePath);
            $configValues = json_decode($configValues, true);

            $this->_configValues = (array)$configValues;

            break;
        }
    }

    /**
     * @return string|null
     */
    final public function getVersion(): ?string
    {
        if (
            array_key_exists('version', $this->_configValues) &&
            !empty($this->_configValues['version']) &&
            is_scalar($this->_configValues['version'])
        ) {
            return (string)$this->_configValues['version'];
        }

        return null;
    }

    /**
     * @return bool
     */
    final public function getLock(): bool
    {
        if (
            array_key_exists('lock', $this->_configValues) &&
            !empty($this->_configValues['lock']) &&
            is_scalar($this->_configValues['lock'])
        ) {
            return $this->_configValues['lock'] == 'true';
        }

        return false;
    }
}
