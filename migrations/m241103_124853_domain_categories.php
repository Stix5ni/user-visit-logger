<?php

use yii\db\Migration;


class m241103_124853_domain_categories extends Migration
{
    public function safeUp()
    {
        $this->createTable('domain_categories', [
            'id' => $this->primaryKey(),
            'domain_id' => $this->integer()->notNull(),
            'categories' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-domain_categories-domain_id',
            'domain_categories',
            'domain_id',
            'domain',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-domain_categories-domain_id', 'domain_categories');
        $this->dropTable('domain_categories');
    }
}
