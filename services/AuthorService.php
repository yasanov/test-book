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

    /**
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function getDataProvider(int $pageSize = 20): ActiveDataProvider
    {
        return $this->authorRepository->getDataProvider($pageSize);
    }

    /**
     * @param int $id
     * @return Author
     * @throws NotFoundException
     */
    public function getById(int $id): Author
    {
        $author = $this->authorRepository->findByIdWithBooks($id);
        if ($author === null) {
            throw new NotFoundException('Автор не найден.');
        }

        return $author;
    }

    /**
     * @param array $data
     * @return Author
     */
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

    /**
     * @param int $id
     * @param array $data
     * @return Author
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function update(int $id, array $data): Author
    {
        $author = $this->getById($id);
        $author->load($data);

        if (!$this->authorRepository->save($author)) {
            throw new ServiceException('Не удалось обновить автора.');
        }

        return $author;
    }

    /**
     * @param int $id
     * @return void
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function delete(int $id): void
    {
        $author = $this->getById($id);
        if (!$this->authorRepository->delete($author)) {
            throw new ServiceException('Не удалось удалить автора.');
        }
    }
}
