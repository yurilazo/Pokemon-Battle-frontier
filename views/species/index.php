<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<h1>Pokedéx</h1>
<ul>
<?php foreach ($species as $specie): ?>
    <li>
        <?= Html::encode("{$specie->name_specie}") ?>:
        <?= $specie->bts_specie ?>
    </li>
<?php endforeach; ?>
</ul>

<?= LinkPager::widget(['pagination' => $pagination]) ?>