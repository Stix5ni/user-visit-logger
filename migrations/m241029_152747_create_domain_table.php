<?php

use yii\db\Migration;


class m241029_152747_create_domain_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('domain', [
            'id' => $this->primaryKey(),
            'domain' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk-domain-user', 'domain', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

   
    public function safeDown()
    {
        $this->dropForeignKey('fk-domain-user', 'domain');
        $this->dropTable('domain');
    }
}
