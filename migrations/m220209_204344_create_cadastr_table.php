<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%cadastr}}`.
 */
class m220209_204344_create_cadastr_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cadastrs}}', [
            'id' => $this->primaryKey(),
            'number' => Schema::TYPE_STRING . ' not null',
            'lat' => Schema::TYPE_STRING . ' not null',
            'lng' => Schema::TYPE_STRING . ' not null',
        ]);
        $this->createIndex('cadastr_name', 'cadastrs', ['number'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cadastr}}');
    }
}
