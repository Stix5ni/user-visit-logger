<?php

use yii\db\Migration;


class m241029_152757_create_contact_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('contact', [
            'id' => $this->primaryKey(),
            'visit_id' => $this->integer()->notNull(),
            'info' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk-contact-visit', 'contact', 'visit_id', 'visit', 'id', 'CASCADE', 'CASCADE');
    }


    public function safeDown()
    {
        $this->dropForeignKey('fk-contact-visit', 'contact');
        $this->dropTable('contact');
    }
}
