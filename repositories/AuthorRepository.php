<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Author;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class AuthorRepository
{
    public function findById(int $id): ?Author
    {
        return Author::findOne($id);
    }

    public function findByIdWithBooks(int $id): ?Author
    {
        return Author::find()
            ->where(['id' => $id])
            ->with('books')
            ->one();
    }

    public function getQueryOrderedByName(): ActiveQuery
    {
        return Author::find()->orderBy('full_name');
    }

    public function findOrderedByName(int $limit = 1000): array
    {
        return $this->getQueryOrderedByName()->limit($limit)->all();
    }

    public function getDataProvider(int $pageSize = 20): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Author::find(),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'full_name' => SORT_ASC,
                ],
            ],
        ]);
    }

    public function save(Author $author): bool
    {
        return $author->save();
    }

    public function delete(Author $author): bool
    {
        return $author->delete() !== false;
    }
}
