<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>

	<div class="col-2">
		<div>Aerodactyl</div>139
	</div>
	<div class="col-2">
		<div>Heracross</div><?= if( $hp_result ) echo $hp_result else echo 301 ?>
	</div>

    <?= $form->field($model, 'attacking_pokemon') ?>

    <?= $form->field($model, 'id_move') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>