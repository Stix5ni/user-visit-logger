<?php


namespace app\models;


use yii\db\ActiveRecord;


class DomainBalance extends ActiveRecord
{
    public static function tableName()
    {
        return 'domain_balance';
    }

    public function rules()
    {
        return [
            [['domain_id', 'balance'], 'required'],
            [['domain_id'], 'integer'],
            [['balance'], 'number'],
        ];
    }

    public function getDomain()
    {
        return $this->hasOne(Domain::class, ['id' => 'domain_id']);
    }
}
