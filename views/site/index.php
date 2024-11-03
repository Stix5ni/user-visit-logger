<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Отслеживание посетителей';
?>

<h1><?= Html::encode($this->title) ?></h1>

<style>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.popup-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    width: 300px;
    text-align: left;
}

.close {
    cursor: pointer;
    float: right;
    font-size: 20px;
}
</style>

<?php $form = ActiveForm::begin(['action' => ['contact/save'], 'method' => 'post']); ?>

    <?= $form->field($model, 'name')->textInput(['required' => true]) ?>
    <?= $form->field($model, 'phone')->textInput() ?>
    <?= $form->field($model, 'email')->input('email') ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<div id="app">
    <domain-table></domain-table>
</div>


<!-- Подключаем pixel.js -->
<script src="/js/pixel.js"></script>


<!-- Подключаем vue.js и domainTable.js -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="/js/domainTable.js"></script>
