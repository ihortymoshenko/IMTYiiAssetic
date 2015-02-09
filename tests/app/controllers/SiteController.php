<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class SiteController extends \CController
{
    public function actionIndex()
    {
        $this->render('index');
    }
}
