<?php

declare(strict_types=1);

namespace app\services;

use app\exceptions\NotFoundException;
use app\exceptions\ServiceException;
use app\models\Book;
use app\models\Author;
use app\repositories\AuthorRepository;
use app\repositories\BookAuthorRepository;
use app\repositories\BookRepository;
use app\services\StorageService;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly BookAuthorRepository $bookAuthorRepository,
        private readonly StorageService $storageService
    ) {
    }

    /**
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function getDataProvider(int $pageSize = 20): ActiveDataProvider
    {
        return $this->bookRepository->getDataProvider($pageSize);
    }

    /**
     * @param int $id
     * @return Book
     * @throws NotFoundException
     */
    public function getById(int $id): Book
    {
        $book = $this->bookRepository->findByIdWithAuthors($id);
        if ($book === null) {
            throw new NotFoundException('Книга не найдена.');
        }

        return $book;
    }

    /**
     * @param array $data
     * @param array $authorIds
     * @param UploadedFile|null $coverImageFile
     * @return Book
     */
    public function create(array $data, array $authorIds, ?UploadedFile $coverImageFile = null): Book
    {
        $book = new Book();
        $book->loadDefaultValues();
        $book->load($data);
        
        if ($coverImageFile !== null) {
            $book->coverImageFile = $coverImageFile;
            $s3Path = $this->storageService->uploadFile($coverImageFile);
            $book->cover_image = $s3Path;
        }

        if (!$this->bookRepository->save($book)) {
            throw new ServiceException('Не удалось сохранить книгу.');
        }

        $this->bookAuthorRepository->replace($book->id, $authorIds);

        return $book;
    }

    /**
     * @param int $id
     * @param array $data
     * @param array $authorIds
     * @param UploadedFile|null $coverImageFile
     * @return Book
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function update(int $id, array $data, array $authorIds, ?UploadedFile $coverImageFile = null): Book
    {
        $book = $this->getById($id);
        $oldCoverImage = $book->cover_image;
        $book->load($data);

        if ($coverImageFile !== null) {
            $book->coverImageFile = $coverImageFile;
            $s3Path = $this->storageService->uploadFile($coverImageFile, $oldCoverImage);
            $book->cover_image = $s3Path;
        }

        if (!$this->bookRepository->save($book)) {
            throw new ServiceException('Не удалось обновить книгу.');
        }

        $this->bookAuthorRepository->replace($book->id, $authorIds);

        return $book;
    }

    /**
     * @param int $id
     * @return void
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function delete(int $id): void
    {
        $book = $this->getById($id);
        $coverImage = $book->cover_image;
        
        if (!$this->bookRepository->delete($book)) {
            throw new ServiceException('Не удалось удалить книгу.');
        }

        if ($coverImage !== null && $coverImage !== '') {
            $this->storageService->deleteFile($coverImage);
        }
    }

    /**
     * @param Book $book
     * @return array
     */
    public function getSelectedAuthorIds(Book $book): array
    {
        return array_map(function ($author) {
            return $author->id;
        }, $book->authors);
    }

}
