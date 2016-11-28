<?php
/**
 * Created by PhpStorm.
 * User: guoxiaosong
 * Date: 2016/11/28
 * Time: 15:38
 */
namespace mztest\uploadOSS;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class FileUploadOSS extends InputWidget
{
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $uploadButtonOptions = [];
    /**
     * @var array the options for the underlying Bootstrap JS plugin.
     * Please refer to the corresponding Bootstrap plugin Web page for possible options.
     * For example, [this page](http://getbootstrap.com/javascript/#modals) shows
     * how to use the "Modal" plugin and the supported options (e.g. "remote").
     */
    public $clientOptions = [];
    /**
     * @var array the event handlers for the underlying Bootstrap JS plugin.
     * Please refer to the corresponding Bootstrap plugin Web page for possible events.
     * For example, [this page](http://getbootstrap.com/javascript/#modals) shows
     * how to use the "Modal" plugin and the supported events (e.g. "shown").
     */
    public $clientEvents = [];

    /**
     * @var string the template for rendering the input.
     */
    public $inputTemplate = <<< HTML
    <div class="input-group">
      {input}
      <span class="input-group-btn">
      {uploadButton}
      </span>
    </div>
HTML;

    public function init()
    {
        parent::init();
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'form-control';
        }

        if (!isset($this->options['placeholder'])) {
            $this->options['placeholder'] = '点击右侧按钮上传文件，或者直接写入文件地址';
        }

        if (!isset($this->uploadButtonOptions['class'])) {
            $this->uploadButtonOptions['class'] = 'btn btn-default fileinput-button';
        }

    }

    public function run()
    {
        $view = $this->getView();
        FileUploadAsset::register($view);


        $id = $this->getUploadInputId();
        $js = "jQuery('#$id').fileupload();";
        $view->registerJs($js);

        echo $this->renderInputGroup();
    }

    protected function getUploadInputId()
    {
        $id = $this->options['id'];

        return $id.'-upload-file';
    }

    protected function renderInputGroup()
    {
        $uploadButtonContent = ArrayHelper::remove($this->uploadButtonOptions, 'content', Yii::t('app', 'Select File'));
        $uploadButtonContent .= Html::input('file', $this->getUploadInputId(), '', ['id' => $this->getUploadInputId()]);

        $uploadButton = Html::tag('span', $uploadButtonContent, $this->uploadButtonOptions);

        if ($this->hasModel()) {
            $input =  Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::textInput($this->name, $this->value, $this->options);
        }

        $inputGroupContent = strtr($this->inputTemplate, [
            '{input}' => $input,
            '{uploadButton}' => $uploadButton,
        ]);
        return $inputGroupContent;
    }

    protected function getClientOptions()
    {
        return $this->clientOptions;
    }
}