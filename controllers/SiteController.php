<?php


namespace app\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Domain;
use app\models\Contact;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Contact();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionDomains()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $domains = Domain::find()
            ->alias('d')
            ->select(['d.id', 'd.domain', 'COUNT(v.id) AS visitCount', 'COUNT(c.id) AS contactsCount'])
            ->leftJoin('visit v', 'v.domain_id = d.id')
            ->leftJoin('contact c', 'c.visit_id = v.id')
            ->where(['>=', 'd.created_at', new \yii\db\Expression('NOW() - INTERVAL 1 WEEK')])
            ->groupBy(['d.id'])
            ->asArray()
            ->all();

        return $domains;
    }

    public function actionDomainDetails($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $domain = Domain::find()
            ->alias('d')
            ->select(['d.id', 'dn.name AS name', 'db.balance AS balance', 'dc.categories AS categories'])
            ->leftJoin('domain_name dn', 'dn.domain_id = d.id')
            ->leftJoin('domain_balance db', 'db.domain_id = d.id')
            ->leftJoin('domain_categories dc', 'dc.domain_id = d.id')
            ->where(['d.id' => $id])
            ->asArray()
            ->one();

        return $domain;
    }
}
