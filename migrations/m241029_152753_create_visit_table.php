<?php

use yii\db\Migration;


class m241029_152753_create_visit_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('visit', [
            'id' => $this->primaryKey(),
            'page' => $this->string()->notNull(),
            'domain_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip' => $this->string()->notNull(),
            'user_agent' => $this->string()->notNull(),
            'browser' => $this->string()->notNull(),
            'device' => $this->string()->notNull(),
            'platform' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('fk-visit-domain', 'visit', 'domain_id', 'domain', 'id', 'CASCADE', 'CASCADE');
    }


    public function safeDown()
    {
        $this->dropForeignKey('fk-visit-domain', 'visit');
        $this->dropTable('visit');
    }
}
