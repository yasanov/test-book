<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Author;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class AuthorRepository
{
    /**
     * @param int $id
     * @return Author|null
     */
    public function findById(int $id): ?Author
    {
        return Author::findOne($id);
    }

    /**
     * @param int $id
     * @return Author|null
     */
    public function findByIdWithBooks(int $id): ?Author
    {
        return Author::find()
            ->where(['id' => $id])
            ->with('books')
            ->one();
    }

    /**
     * @return ActiveQuery
     */
    public function getQueryOrderedByName(): ActiveQuery
    {
        return Author::find()->orderBy('full_name');
    }

    /**
     * @param int $limit
     * @return Author[]
     */
    public function findOrderedByName(int $limit = 1000): array
    {
        return $this->getQueryOrderedByName()->limit($limit)->all();
    }

    /**
     * @param int $pageSize
     * @return ActiveDataProvider
     */
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

    /**
     * @param Author $author
     * @return bool
     */
    public function save(Author $author): bool
    {
        return $author->save();
    }

    /**
     * @param Author $author
     * @return bool
     */
    public function delete(Author $author): bool
    {
        return $author->delete() !== false;
    }
}
