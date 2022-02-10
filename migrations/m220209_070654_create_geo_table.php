<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%geo}}`.
 */
class m220209_070654_create_geo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//gid        | integer           | Unique ID
//code       | integer           | Unique ID
//name       | character varying | City / Town Name
//the_geom   | geometry          | Location Geometry (Polygon)
        $this->createTable('{{%geo}}', [
            'id' => $this->primaryKey(),           
            'name' => Schema::TYPE_STRING . ' not null',
            'geometry' => 'geometry',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%geo}}');
    }
}
