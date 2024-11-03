<?php

use yii\db\Migration;


class m241029_152721_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }


    public function safeDown()
    {
        $this->dropTable('user');
    }
}
