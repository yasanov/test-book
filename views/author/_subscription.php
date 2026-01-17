<?php

/** @var yii\web\View $this */
/** @var app\models\Author $author */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\AuthorSubscription;

$model = new AuthorSubscription();
$model->author_id = $author->id;
?>

<?php $form = ActiveForm::begin([
    'action' => ['subscription/subscribe', 'authorId' => $author->id],
    'method' => 'post',
]); ?>

<?= $form->field($model, 'email')->textInput(['type' => 'email', 'maxlength' => true]) ?>

<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

<?= Html::activeHiddenInput($model, 'author_id') ?>

<div class="form-group">
    <?= Html::submitButton('Подписаться', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
