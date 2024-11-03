<?php


namespace app\controllers;


use Yii;
use yii\web\Controller;
use app\models\Contact;
use app\models\Visit;


class ContactController extends Controller
{
    public function actionSave() 
    {
        $model = new Contact();
        
        // Создаем переменные для name, phone и email
        $name = Yii::$app->request->post('Contact')['name'] ?? null;
        $phone = Yii::$app->request->post('Contact')['phone'] ?? null;
        $email = Yii::$app->request->post('Contact')['email'] ?? null;
    
        // Проверяем, если данные загружены
        if ($name && $phone && $email) {
            $lastVisit = Visit::find()->orderBy(['created_at' => SORT_DESC])->one();
            
            if ($lastVisit) {
                $model->visit_id = $lastVisit->id;
            }
            

            $model->info = json_encode([
                'name' => $name,
                'phone' => $phone,
                'email' => $email
            ]);
            
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Ваш контакт успешно сохранен.');

                return $this->redirect(['site/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении: ' . json_encode($model->errors));
            }
        }
        
        // В случае ошибки, перенаправляем обратно на главное действие
        return $this->redirect(['site/index']);
    }
    
    public function actionList($domainId)
    {
        $contacts = Contact::find()
            ->joinWith('visit')
            ->where(['visit.domain_id' => $domainId])
            ->all();

        $result = [];

        foreach ($contacts as $contact) {
            $result[] = [
                'id' => $contact->id,
                'info' => json_decode($contact->info, true),
                'created_at' => $contact->created_at,
            ];
        }

        return $this->asJson($result);
    }

}
