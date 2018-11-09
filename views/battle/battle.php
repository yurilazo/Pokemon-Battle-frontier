<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); var_dump($effectiveness) ?>

	<div class="col-2">
		<div>Aerodactyl</div>139
	</div>
	<div class="col-2">
		<div>Heracross</div>
	</div>

    <?= $form->field($model, 'codebattlemove') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>