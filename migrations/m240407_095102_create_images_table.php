<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%images}}`.
 */
class m240407_095102_create_images_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%images}}', [
            'id' => $this->primaryKey(),
            'filename' => $this->string()->notNull(),
            'uploaded_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%images}}');
    }
}
