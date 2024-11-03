<?php


namespace app\models;


use yii\db\ActiveRecord;


class DomainCategories extends ActiveRecord
{
    public static function tableName()
    {
        return 'domain_categories';
    }

    public function rules()
    {
        return [
            [['domain_id', 'categories'], 'required'],
            [['domain_id'], 'integer'],
            [['categories'], 'string', 'max' => 255],
        ];
    }

    public function getDomain()
    {
        return $this->hasOne(Domain::class, ['id' => 'domain_id']);
    }
}
