<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;

/**
 * AuthorSubscription model
 *
 * @property int $id
 * @property int $author_id
 * @property string $email
 * @property string $phone
 * @property int $created_at
 *
 * @property Author $author
 */
class AuthorSubscription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%author_subscriptions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false, // Только created_at
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['author_id', 'email', 'phone'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^[\d\s\-\+\(\)]+$/', 'message' => 'Некорректный формат телефона'],
            [['author_id', 'email', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'email', 'phone'], 'message' => 'Вы уже подписаны на этого автора'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор',
            'email' => 'Email',
            'phone' => 'Телефон',
            'created_at' => 'Дата подписки',
        ];
    }

    /**
     * Gets query for [[Author]]
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Check if subscription exists for author, email and phone
     *
     * @param int $authorId
     * @param string $email
     * @param string $phone
     * @return bool
     */
    public static function exists(int $authorId, string $email, string $phone): bool
    {
        return static::find()
            ->where(['author_id' => $authorId, 'email' => $email, 'phone' => $phone])
            ->exists();
    }
}
