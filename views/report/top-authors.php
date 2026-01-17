<?php

/** @var yii\web\View $this */
/** @var array $topAuthors */
/** @var int $selectedYear */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'ТОП-10 авторов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-top-authors">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['report/top-authors'],
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= Html::label('Год', 'year') ?>
            <?= Html::textInput('year', $selectedYear, [
                'type' => 'number',
                'min' => 1000,
                'max' => 9999,
                'class' => 'form-control',
                'id' => 'year',
            ]) ?>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label>
            <div>
                <?= Html::submitButton('Показать', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <hr>

    <?php if (empty($topAuthors)): ?>
        <p class="text-muted">Нет данных за выбранный год.</p>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Место</th>
                    <th>Автор</th>
                    <th>Количество книг</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topAuthors as $index => $author): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= Html::encode($author['full_name']) ?></td>
                        <td><?= $author['books_count'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
