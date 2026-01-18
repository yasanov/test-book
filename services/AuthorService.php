<?php

declare(strict_types=1);

namespace app\services;

use app\exceptions\NotFoundException;
use app\exceptions\ServiceException;
use app\models\Author;
use app\repositories\AuthorRepository;
use yii\data\ActiveDataProvider;

class AuthorService
{
    public function __construct(
        private readonly AuthorRepository $authorRepository
    ) {
    }

    public function getDataProvider(int $pageSize = 20): ActiveDataProvider
    {
        return $this->authorRepository->getDataProvider($pageSize);
    }

    public function getById(int $id): Author
    {
        $author = $this->authorRepository->findByIdWithBooks($id);
        if ($author === null) {
            throw new NotFoundException('Автор не найден.');
        }

        return $author;
    }

    public function create(array $data): Author
    {
        $author = new Author();
        $author->loadDefaultValues();
        $author->load($data);

        if (!$this->authorRepository->save($author)) {
            throw new ServiceException('Не удалось сохранить автора.');
        }

        return $author;
    }

    public function update(int $id, array $data): Author
    {
        $author = $this->getById($id);
        $author->load($data);

        if (!$this->authorRepository->save($author)) {
            throw new ServiceException('Не удалось обновить автора.');
        }

        return $author;
    }

    public function delete(int $id): void
    {
        $author = $this->getById($id);
        if (!$this->authorRepository->delete($author)) {
            throw new ServiceException('Не удалось удалить автора.');
        }
    }
}
