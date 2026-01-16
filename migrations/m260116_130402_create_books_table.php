<?php

use yii\db\Migration;

/**
 * Class m260116_130402_create_books_table
 */
class m260116_130402_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'year' => $this->integer()->notNull(),
            'description' => $this->text()->null(),
            'isbn' => $this->string(20)->null()->unique(),
            'cover_image' => $this->string(500)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->createIndex('idx-books-title', '{{%books}}', 'title');
        $this->createIndex('idx-books-year', '{{%books}}', 'year');
        $this->createIndex('idx-books-isbn', '{{%books}}', 'isbn');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
