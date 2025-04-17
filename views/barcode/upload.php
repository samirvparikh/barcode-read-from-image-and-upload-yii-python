<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Upload Image with Barcode';
?>

<h2><?= Html::encode($this->title) ?></h2>

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data', 'id' => 'uploadForm']
]); ?>

<?= $form->field($model, 'imageFile')->fileInput() ?>
<?= $form->field($model, 'barcode') ?>
<?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>

<p id="status"></p>