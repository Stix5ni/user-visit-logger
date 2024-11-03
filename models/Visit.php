<?php


namespace app\models;


use yii\db\ActiveRecord;


class Visit extends ActiveRecord
{
    public static function tableName()
    {
        return 'visit';
    }

    public function getDomain()
    {
        return $this->hasOne(Domain::class, ['id' => 'domain_id']);
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['visit_id' => 'id']);
    }

}
