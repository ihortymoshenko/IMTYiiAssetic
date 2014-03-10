<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic;

/**
 * This class overrides the core Yii class
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class ClientScript extends \CClientScript
{
    /**
     * {@inheritDoc}
     */
    public function renderCoreScripts()
    {
        if ($this->coreScripts === null) {
            return;
        }

        $cssFiles = array();
        $jsFiles  = array();

        foreach ($this->coreScripts as $name => $package) {
            $baseUrl = $this->getPackageBaseUrl($name);

            if (!empty($package['js'])) {
                if (!isset($package['combineTo'])) {
                    foreach ($package['js'] as $js) {
                        $jsFiles[$baseUrl . '/' . $js] = $baseUrl . '/' . $js;
                    }
                } else {
                    if (!isset($baseUrl['js'])) {
                        $jsFiles[$baseUrl] = $baseUrl;
                    } else {
                        $jsFiles[$baseUrl['js']] = $baseUrl['js'];
                    }
                }
            }

            if (!empty($package['css'])) {
                if (!isset($package['combineTo'])) {
                    foreach ($package['css'] as $css) {
                        $cssFiles[$baseUrl . '/' . $css] = '';
                    }
                } else {
                    if (!isset($baseUrl['css'])) {
                        $cssFiles[$baseUrl] = '';
                    } else {
                        $cssFiles[$baseUrl['css']] = '';
                    }
                }
            }
        }

        foreach ($this->cssFiles as $cssFile => $media) {
            $cssFiles[$cssFile] = $media;
        }

        $this->cssFiles = $cssFiles;

        if (isset($this->scriptFiles[$this->coreScriptPosition])) {
            foreach ($this->scriptFiles[$this->coreScriptPosition] as $url => $value) {
                $jsFiles[$url] = $value;
            }
        }

        $this->scriptFiles[$this->coreScriptPosition] = $jsFiles;
    }

    /**
     * {@inheritDoc}
     */
    public function getPackageBaseUrl($name)
    {
        if (!isset($this->coreScripts[$name])) {
            return false;
        }

        $package = $this->coreScripts[$name];

        if (isset($package['baseUrl'])) {
            $baseUrl = $package['baseUrl'];

            if ($baseUrl === '' || $baseUrl[0] !== '/' && strpos($baseUrl, '://') === false) {
                $baseUrl = \Yii::app()->getRequest()->getBaseUrl() . '/' . $baseUrl;
            }

            $baseUrl = rtrim($baseUrl, '/');
        } elseif (isset($package['basePath'])) {
            $baseUrl = \Yii::app()->getAssetManager()->publish(
                \Yii::getPathOfAlias($package['basePath']),
                false,
                -1,
                null,
                !isset($package['filtersByExt']) ? array() : $package['filtersByExt'],
                !isset($package['combineTo'])    ? null    : $package['combineTo']
            );
        } else {
            $baseUrl = $this->getCoreScriptUrl();
        }

        return $this->coreScripts[$name]['baseUrl'] = $baseUrl;
    }
}
