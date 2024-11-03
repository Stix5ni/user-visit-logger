<?php


namespace app\controllers;


use yii\rest\Controller;
use app\models\Domain;
use app\models\Visit;
use app\models\Contact;
use app\models\DomainBalance;
use app\models\DomainCategories;


class DomainController extends Controller
{
    public function actionCheck($domain)
    {
        $exists = Domain::find()->where(['domain' => $domain])->exists();

        return $this->asJson(['exists' => $exists]);
    }

    public function actionGetAll() 
    {
        $weekAgo = new \DateTime('-7 days');

        // Получаем все домены, созданные на этой неделе
        $domains = Domain::find()
            ->where(['>=', 'created_at', $weekAgo->format('Y-m-d')])
            ->andWhere(['not in', 'domain', $this->getBlacklistedDomains()]) // Метод для получения черного списка
            ->all();

        $result = [];
        foreach ($domains as $domain) {
            $visitCount = Visit::find()->where(['domain_id' => $domain->id])->count();
            $lastMonthVisits = Visit::find()->where(['domain_id' => $domain->id])
                ->andWhere(['>=', 'created_at', (new \DateTime('-1 month'))->format('Y-m-d')])
                ->count();
            $contactCount = Contact::find()->where(['visit_id' => Visit::find()->select('id')->where(['domain_id' => $domain->id])])->count();
            $visitorCount = Contact::find()->select('DISTINCT visit_id')->where(['visit_id' => Visit::find()->select('id')->where(['domain_id' => $domain->id])])->count();
            $lastContactDate = Contact::find()->where(['visit_id' => Visit::find()->select('id')->where(['domain_id' => $domain->id])])->max('created_at');

            $result[] = [
                'id' => $domain->id,
                'domain' => $domain->domain,
                'visit_count' => $visitCount,
                'last_month_visits' => $lastMonthVisits,
                'contact_count' => $contactCount,
                'visitor_count' => $visitorCount,
                'last_contact_date' => $lastContactDate,
            ];
        }

        return $this->asJson($result);
    }

    public function actionGetDomainDetails($id) 
    {
        $domain = Domain::find()->where(['id' => $id])->one();
        if (!$domain) {
            return $this->asJson(['error' => 'Domain not found']);
        }

        $balance = DomainBalance::find()->where(['domain_id' => $id])->one();
        $categories = DomainCategories::find()->where(['domain_id' => $id])->all();

        $result = [
            'domain_id' => $domain->id,
            'name' => $domain->domain,
            'balance' => $balance ? $balance->balance : 0,
            'categories' => array_map(function($cat) {
                return $cat->categories;
            }, $categories),
        ];

        return $this->asJson($result);
    }

    private function getBlacklistedDomains() 
    {
        return ['blacklisted-domain.com', 'another-blacklist-domain.com'];
    }

    public function actionDetails($id)
    {
        $domain = Domain::find()->where(['id' => $id])->one();
        if ($domain) {
            $balance = DomainBalance::find()->where(['domain_id' => $id])->one();
            $categories = DomainCategories::find()->where(['domain_id' => $id])->all();

            return $this->asJson([
                'balance' => $balance ? $balance->balance : null,
                'categories' => array_map(function($cat) {
                    return $cat->categories;
                }, $categories),
            ]);
        }

        return $this->asJson(['error' => 'Domain not found.'], 404);
    }

}

