# yii2-widget-upload-oss
A widget for uploading files to AliYun OSS

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require mztest/yii2-widget-upload-oss
```

or add

```
"mztest/yii2-single-file-upload": "*"
```

to the require section of your `composer.json` file.

Usage
-----

1. Set signature action at your controller
       
       ```php
       public function actions()
           {
               return [
                   'upload' => [
                       'oss-signature' => [
                           'class' => 'mztest\uploadOSS\actions\Signature',
                           'accessKeyId' => 'Your aliyunOSS access key id here.',
                           'accessKeySecret' => 'Your aliyunOSS access secret id here.',
                           'host' => 'Your aliyunOSS upload bucket url',
                       ],
                   ],
               ];
           }
       ```
2. Simply use it in your code by  :
   
       ```php
       <?= \mztest\uploadOSS\FileUploadOSS::widget(); ?>
       ```
   
       or
   
       ```php
       <?= $form->field($model, 'url')->widget(FileUploadOSS::className(), [
           'signatureAction' => ['oss-signature']
       ]) ?>
       ```