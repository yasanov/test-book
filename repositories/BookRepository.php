<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class BookRepository
{
    /**
     * @param int $id
     * @return Book|null
     */
    public function findById(int $id): ?Book
    {
        return Book::findOne($id);
    }

    /**
     * @param int $id
     * @return Book|null
     */
    public function findByIdWithAuthors(int $id): ?Book
    {
        return Book::find()
            ->where(['id' => $id])
            ->with('authors')
            ->one();
    }

    /**
     * @return ActiveQuery
     */
    public function getQueryWithAuthors(): ActiveQuery
    {
        return Book::find()->with('authors');
    }

    /**
     * @param int $pageSize
     * @return ActiveDataProvider
     */
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

    /**
     * @param Book $book
     * @return bool
     */
    public function save(Book $book): bool
    {
        return $book->save();
    }

    /**
     * @param Book $book
     * @return bool
     */
    public function delete(Book $book): bool
    {
        return $book->delete() !== false;
    }
}
