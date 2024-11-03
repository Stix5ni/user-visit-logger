<?php

use yii\db\Migration;


class m241103_124840_domain_balance extends Migration
{
    public function safeUp()
    {
        $this->createTable('domain_balance', [
            'id' => $this->primaryKey(),
            'domain_id' => $this->integer()->notNull(),
            'balance' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-domain_balance-domain_id',
            'domain_balance',
            'domain_id',
            'domain',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-domain_balance-domain_id', 'domain_balance');
        $this->dropTable('domain_balance');
    }
}
