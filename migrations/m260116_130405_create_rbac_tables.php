<?php

use yii\db\Migration;

/**
 * Class m260116_130405_create_rbac_tables
 * 
 * Создает таблицы для RBAC (Role-Based Access Control)
 * Использует стандартные таблицы Yii2 для DbManager
 */
class m260116_130405_create_rbac_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Таблица правил (rules)
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary()->null(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        
        $this->addPrimaryKey('pk-auth_rule', '{{%auth_rule}}', 'name');

        // Таблица элементов (роли и разрешения)
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text()->null(),
            'rule_name' => $this->string(64)->null(),
            'data' => $this->binary()->null(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        
        $this->addPrimaryKey('pk-auth_item', '{{%auth_item}}', 'name');
        $this->addForeignKey(
            'fk-auth_item-rule_name',
            '{{%auth_item}}',
            'rule_name',
            '{{%auth_rule}}',
            'name',
            'SET NULL',
            'CASCADE'
        );
        $this->createIndex('idx-auth_item-type', '{{%auth_item}}', 'type');

        // Таблица иерархии элементов (родитель-потомок)
        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        
        $this->addPrimaryKey('pk-auth_item_child', '{{%auth_item_child}}', ['parent', 'child']);
        $this->addForeignKey(
            'fk-auth_item_child-parent',
            '{{%auth_item_child}}',
            'parent',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-auth_item_child-child',
            '{{%auth_item_child}}',
            'child',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        // Таблица назначений (привязка ролей к пользователям)
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer()->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        
        $this->addPrimaryKey('pk-auth_assignment', '{{%auth_assignment}}', ['item_name', 'user_id']);
        $this->addForeignKey(
            'fk-auth_assignment-item_name',
            '{{%auth_assignment}}',
            'item_name',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('idx-auth_assignment-user_id', '{{%auth_assignment}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем таблицы RBAC в правильном порядке (сначала зависимые)
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
    }
}
