<?php

use yii\db\Migration;

/**
 * Class m260116_130404_create_author_subscriptions_table
 */
class m260116_130404_create_author_subscriptions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%author_subscriptions}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'email' => $this->string(255)->notNull(),
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->addForeignKey(
            'fk-author_subscriptions-author_id',
            '{{%author_subscriptions}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-author_subscriptions-author_id', '{{%author_subscriptions}}', 'author_id');
        $this->createIndex('idx-author_subscriptions-email', '{{%author_subscriptions}}', 'email');
        $this->createIndex('idx-author_subscriptions-phone', '{{%author_subscriptions}}', 'phone');
        // Уникальный индекс для комбинации author_id + email + phone (один гость может подписаться только один раз)
        $this->createIndex('idx-author_subscriptions-unique', '{{%author_subscriptions}}', ['author_id', 'email', 'phone'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%author_subscriptions}}');
    }
}
