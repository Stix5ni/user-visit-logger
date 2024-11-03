<?php


namespace app\models;


use yii\db\ActiveRecord;


class Contact extends ActiveRecord
{
    public $name;
    public $phone;
    public $email;

    public static function tableName()
    {
        return 'contact';
    }

    public function getVisit()
    {
        return $this->hasOne(Visit::class, ['id' => 'visit_id']);
    }

    public function getName() {
        return json_decode($this->info)->name ?? null;
    }

    public function getPhone() {
        return json_decode($this->info)->phone ?? null;
    }

    public function getEmail() {
        return json_decode($this->info)->email ?? null;
    }
}
