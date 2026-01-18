<?php

declare(strict_types=1);

namespace app\repositories;

use app\exceptions\RepositoryException;
use Yii;

class BookAuthorRepository
{
    public function deleteByBookId(int $bookId): void
    {
        $result = Yii::$app->db->createCommand()
            ->delete('{{%book_author}}', ['book_id' => $bookId])
            ->execute();

        if ($result === false) {
            throw new RepositoryException('Не удалось удалить связи книги с авторами.');
        }
    }

    public function batchInsert(int $bookId, array $authorIds): void
    {
        if (empty($authorIds)) {
            return;
        }

        $validAuthorIds = [];
        foreach ($authorIds as $authorId) {
            $authorId = (int)$authorId;
            if ($authorId > 0) {
                $validAuthorIds[] = $authorId;
            }
        }

        if (empty($validAuthorIds)) {
            return;
        }

        $rows = [];
        foreach ($validAuthorIds as $authorId) {
            $rows[] = [$bookId, $authorId];
        }

        $result = Yii::$app->db->createCommand()
            ->batchInsert('{{%book_author}}', ['book_id', 'author_id'], $rows)
            ->execute();

        if ($result === false) {
            throw new RepositoryException('Не удалось сохранить связи книги с авторами.');
        }
    }

    public function replace(int $bookId, array $authorIds): void
    {
        $this->deleteByBookId($bookId);
        $this->batchInsert($bookId, $authorIds);
    }
}
