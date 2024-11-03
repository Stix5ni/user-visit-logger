<?php


namespace app\models;



use yii\db\ActiveRecord;


class Domain extends ActiveRecord
{
    public static function tableName()
    {
        return 'domain';
    }

    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['domain_id' => 'id']);
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['visit_id' => 'id'])
                    ->via('visits');
    }
}
