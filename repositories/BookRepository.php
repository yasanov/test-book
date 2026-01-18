<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class BookRepository
{
    public function findById(int $id): ?Book
    {
        return Book::findOne($id);
    }

    public function findByIdWithAuthors(int $id): ?Book
    {
        return Book::find()
            ->where(['id' => $id])
            ->with('authors')
            ->one();
    }

    public function getQueryWithAuthors(): ActiveQuery
    {
        return Book::find()->with('authors');
    }

    public function getDataProvider(int $pageSize = 20): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->getQueryWithAuthors(),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);
    }

    public function save(Book $book): bool
    {
        return $book->save();
    }

    public function delete(Book $book): bool
    {
        return $book->delete() !== false;
    }
}
