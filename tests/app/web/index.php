<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$yii = __DIR__ . '/../../../vendor/yiisoft/yii/framework/yii.php';
$config = __DIR__ . '/../config/main.php';

require_once $yii;

\Yii::createWebApplication($config)->run();
