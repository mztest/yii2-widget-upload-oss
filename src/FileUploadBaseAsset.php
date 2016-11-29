<?php
/**
 * Created by PhpStorm.
 * User: guoxiaosong
 * Date: 2016/11/29
 * Time: 15:36
 */
namespace mztest\uploadOSS;

use yii\web\AssetBundle;

/**
 * FileUploadBaseAsset
 */
class FileUploadBaseAsset extends AssetBundle
{
    public $sourcePath = __DIR__ .'/assets';
    
    public $js = [
        'main.js',
    ];

}