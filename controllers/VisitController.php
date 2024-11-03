<?php


namespace app\controllers;


use Yii;
use yii\web\Controller;
use app\models\Visit;
use app\models\Domain;


class VisitController extends Controller
{
    public function actionSave() 
    {
        $data = Yii::$app->request->getBodyParams();
        
        if (empty($data)) {
            return $this->asJson(['success' => false, 'error' => 'Invalid JSON']);
        }
    
        Yii::info('Received data: ' . json_encode($data), __METHOD__);
    

        $visit = new Visit();
        
        // Устанавливаем значения
        $visit->page = $data['page'] ?? null;
        $visit->ip = Yii::$app->request->userIP;
        $visit->user_agent = $data['userAgent'] ?? null;
        $visit->browser = $data['browser'] ?? null;
        $visit->device = $data['device'] ?? null;
        $visit->platform = $data['platform'] ?? null;
    
        
        $domain = Domain::find()->where(['domain' => $data['domain']])->one();
    
        if ($domain) {
            $visit->domain_id = $domain->id;
        } else {
            return $this->asJson(['success' => false, 'error' => 'Domain not found']);
        }
    
        // Проверяем все поля
        Yii::info('Visit data: ' . json_encode($visit->attributes), __METHOD__);
    
        
        if ($visit->save()) {
            return $this->asJson(['success' => true]);
        }
    
        return $this->asJson(['success' => false, 'errors' => $visit->errors]);
    }

    public function actionGetVisitorsWithContacts($domainId) 
    {
        $visits = Visit::find()
            ->select(['id', 'page', 'created_at'])
            ->where(['domain_id' => $domainId])
            ->with('contacts')
            ->all();
    
        $visitorsData = [];
        foreach ($visits as $visit) {
            $visitorsData[] = [
                'id' => $visit->id,
                'page' => $visit->page,
                'created_at' => $visit->created_at,
                'contacts' => $visit->contacts,
            ];
        }
    
        return $this->asJson(['success' => true, 'visitors' => $visitorsData]);
    } 
    
}
