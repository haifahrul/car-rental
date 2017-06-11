<?php

namespace app\controllers;

use app\models\Order;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Car;
use app\models\Company;
use app\models\Category;
use app\models\forms\UserRegisterForm;
use app\models\forms\OrderForm;

/**
 * Class SiteController
 *
 * @property \app\models\Car $_car
 */
class SiteController extends Controller
{
    private $_car;

    /**
     * @inheritdoc
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
        $model = Car::find()->all();
        $companies = Company::find()->all();
        $categories = Category::find()->all();
        return $this->render('index', ['model' => $model, 'companies' => $companies, 'categories' => $categories]);
    }

    /**
     * Car company page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCompany()
    {
        if (!$company = Company::findOne(['slug' => Yii::$app->request->get('value')]))
            throw new NotFoundHttpException('Такая марка не найдена');
        $model = Car::findAll(['company_id' => $company->id]);

        return $this->render('company', ['model' => $model, 'company' => $company]);
    }

    /**
     * Detail car page
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCar()
    {
        if (!$model = Car::findOne(['slug' => Yii::$app->request->get('value')]))
            throw new NotFoundHttpException('Такой автомобиль не найден');

        return $this->render('detail', ['model' => $model]);
    }

    /**
     * Car's category
     */
    public function actionCategory()
    {
        if (!$category = Category::findOne(['slug' => Yii::$app->request->get('value')]))
            throw new NotFoundHttpException();

        $model = Car::find()->where(['category_id' => $category->id])->all();

        return $this->render('category', ['model' => $model, 'category' => $category]);
    }

    /**
     * Create (and register user) new order
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionOrder($id)
    {
        if (!$car = Car::findOne($id))
            throw new NotFoundHttpException('Страница не найдена');

        $order = new Order();
        $order->car = $car;

        if (Yii::$app->request->isAjax) {
            $order->start_rent = strtotime(Yii::$app->request->get('start_rent'));
            $order->end_rent = strtotime(Yii::$app->request->get('end_rent'));

            return $order->getAjaxOrderInfo();
        }

        if (Yii::$app->request->isPost) {
            $order->validate();
        }


        // For Ajax
        /*if (Yii::$app->request->get('start_rent')) {
            return $this->getAjaxOrderInfo();
        }

        // Create order and register if user is guest
        if (Yii::$app->request->post()) {

            // Register new user
            if (Yii::$app->user->isGuest) {
                $userRegisterForm = new UserRegisterForm();
                $userRegisterForm->register(Yii::$app->request->post());
            }

            $this->saveOrder();
        }*/

        return $this->render('order', [
            'orderModel' => $order,
            'registerModel' => new UserRegisterForm(),
            'car' => $car
        ]);
    }

    /**
     * Return json with discount and amount for ajax
     *
     * @return string
     */
    private function getAjaxOrderInfo()
    {
        $request = Yii::$app->request;

        $data['discount'] = $this->_car->getDiscount($request->get('start_rent'), $request->get('end_rent'));
        $data['amount'] = $this->_car->getAmount($data['discount']);

        return json_encode($data);
    }

    /**
     * Save new order
     *
     * @return string
     */
    private function saveOrder()
    {
        $orderModel = new OrderForm();
        $orderModel->save(Yii::$app->request->post());
        Yii::$app->session->setFlash('orderConfirm', 'Ваш заказ принят');

        return $this->render('order');
    }
}
