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
use yii\helpers\Json;
use yii\widgets\InputWidget;

class FileUploadOSS extends InputWidget
{
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $progressBarContainerOptions = [];
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

    public $progressBarTemplate = <<<HTML
    <div class="file-info"></div>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
            0%
        </div>
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
            $this->uploadButtonOptions['class'] = 'btn btn-primary fileinput-button';
        }

        if (!isset($this->progressBarContainerOptions['id'])) {
            $this->progressBarContainerOptions['id'] = $this->getProgressBarContainerId();
        }

    }

    public function run()
    {
        $view = $this->getView();
        FileUploadAsset::register($view);

        $js = [];
        $id = $this->getUploadInputId();
        $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
        $js[] = "jQuery('#$id').fileupload($options);";

        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event', $handler);";
            }
        }
        $view->registerJs(implode("\n", $js));

        echo $this->renderProgressBar();
        echo $this->renderInputGroup();
    }

    protected function getUploadInputId()
    {
        $id = $this->options['id'];

        return $id.'-upload-file';
    }

    protected function getProgressBarContainerId()
    {
        $id = $this->options['id'];

        return $id.'-progress-bar';
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

    protected function renderProgressBar()
    {
        return Html::tag('div', $this->progressBarTemplate, $this->progressBarContainerOptions);
    }

    protected function getClientOptions()
    {
        $clientOptions = [
            'autoUpload' => false,
            'formData' => [],
        ];
        $this->clientOptions = ArrayHelper::merge($clientOptions, $this->clientOptions);
        return $this->clientOptions;
    }
}