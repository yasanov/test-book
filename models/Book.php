<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * Book model
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_image
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 * @property UploadedFile|null $coverImageFile
 */
class Book extends ActiveRecord
{
    /**
     * @var UploadedFile|null
     */
    public ?UploadedFile $coverImageFile = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%books}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'year'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'trim'],
            [['year'], 'integer', 'min' => 1000, 'max' => 9999],
            [['description'], 'string'],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'match', 'pattern' => '/^[0-9\-X]+$/', 'message' => 'ISBN должен содержать только цифры, дефисы и X'],
            [['cover_image'], 'string', 'max' => 500],
            [['coverImageFile'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 5 * 1024 * 1024],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_image' => 'Обложка',
            'coverImageFile' => 'Файл обложки',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    /**
     * Gets query for [[Authors]]
     *
     * @return ActiveQuery
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /**
     * Gets authors names as string
     *
     * @return string
     */
    public function getAuthorsNames(): string
    {
        $authors = $this->authors;
        if (empty($authors)) {
            return '';
        }
        return implode(', ', array_map(function (Author $author): string {
            return $author->full_name;
        }, $authors));
    }

    /**
     * Upload cover image
     *
     * @return bool
     */
    public function uploadCoverImage(): bool
    {
        if ($this->coverImageFile === null) {
            return true;
        }

        $uploadPath = Yii::getAlias('@webroot/uploads/books');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $fileName = (string)time() . '_' . Yii::$app->security->generateRandomString(10) . '.' . $this->coverImageFile->extension;
        $filePath = $uploadPath . '/' . $fileName;

        if ($this->coverImageFile->saveAs($filePath)) {
            // Удаляем старое изображение, если оно существует
            if ($this->cover_image !== null && $this->cover_image !== '' && file_exists(Yii::getAlias('@webroot') . $this->cover_image)) {
                @unlink(Yii::getAlias('@webroot') . $this->cover_image);
            }
            $this->cover_image = '/uploads/books/' . $fileName;
            return true;
        }

        return false;
    }

    /**
     * Gets cover image URL
     *
     * @return string|null
     */
    public function getCoverImageUrl(): ?string
    {
        if ($this->cover_image !== null && $this->cover_image !== '') {
            return $this->cover_image;
        }
        return null;
    }

    /**
     * Before save event
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            // Загружаем изображение, если оно было загружено
            if ($this->coverImageFile !== null) {
                $this->uploadCoverImage();
            }
            return true;
        }
        return false;
    }

    /**
     * After delete event
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        // Удаляем файл обложки
        if ($this->cover_image !== null && $this->cover_image !== '' && file_exists(Yii::getAlias('@webroot') . $this->cover_image)) {
            @unlink(Yii::getAlias('@webroot') . $this->cover_image);
        }
    }
}
