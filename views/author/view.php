<?php

/** @var yii\web\View $this */
/** @var app\models\Author $model */

use yii\bootstrap5\Html;

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->can('updateAuthor')): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->can('deleteAuthor')): ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <table class="table table-striped table-bordered">
        <tr>
            <th>ФИО</th>
            <td><?= Html::encode($model->full_name) ?></td>
        </tr>
        <tr>
            <th>Количество книг</th>
            <td><?= count($model->books) ?></td>
        </tr>
    </table>

    <?php if (!empty($model->books)): ?>
        <h3>Книги автора</h3>
        <ul>
            <?php foreach ($model->books as $book): ?>
                <li><?= Html::a(Html::encode($book->title), ['book/view', 'id' => $book->id]) ?> (<?= $book->year ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="mb-0">Подписаться на уведомления о новых книгах</h3>
        </div>
        <div class="card-body">
            <?php if (Yii::$app->user->isGuest): ?>
                <p class="text-muted">Укажите ваш email или телефон (хотя бы одно поле обязательно), чтобы получать SMS-уведомления о новых книгах этого автора.</p>
                <?= $this->render('_subscription', ['author' => $model]) ?>
            <?php else: ?>
                <p class="text-muted">Подписка на уведомления доступна только для неавторизованных пользователей. 
                <?= Html::a('Выйти из системы', ['site/logout'], ['class' => 'btn btn-sm btn-outline-secondary']) ?> для подписки.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
