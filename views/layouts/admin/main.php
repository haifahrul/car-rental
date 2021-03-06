<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use app\assets\AdminAsset;

if (Yii::$app->user->isGuest) {

}

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
    <body>
    <?php $this->beginBody() ?>

        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'Car rent',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            Pjax::begin(['enablePushState' => false, 'linkSelector' => '#logout']);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Админ', 'url' => ['/admin']],
                    ['label' => 'Страницы', 'url' => ['/admin/page']],
                    ['label' => 'Автомобили', 'url' => ['/admin/car']],
                    ['label' => 'Марки', 'url' => ['/admin/company']],
                    ['label' => 'Категории', 'url' => ['/admin/category']],
                    ['label' => 'Пользователи', 'url' => ['/admin/user']],
                    ['label' => 'Заказы', 'url' => ['/admin/order']],
                    [
                        'label' => 'Выход',
                        'url' => ['/user/logout'],
                        'linkOptions' => ['id' => 'logout']
                    ],
                ],
            ]);
            Pjax::end();
            NavBar::end();
            ?>

            <div class="container">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= $content ?>
            </div>

        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

                <p class="pull-right"><?= Yii::powered() ?></p>
            </div>
        </footer>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
