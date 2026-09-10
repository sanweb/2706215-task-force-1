<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Landing layout asset bundle.
 */
class LandingAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/css/normalize.css',
        '/css/landing.css',
    ];
    public $js = [
        '/js/landing.js',
    ];
    public $jsOptions = [
        'position' => View::POS_END,
    ];
    public $depends = [];
}
