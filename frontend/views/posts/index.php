<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;


/* @var $this yii\web\View */
/* @var $posts common\models\Post */
/* @var $pagination common\models\Post */


$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?/*php echo $this->render('_search', ['model' => $searchModel]); */?>
    <?php foreach($posts as $post):?>
    <?= $post->title?>
    <?php endforeach; ?>
    <?php
    echo LinkPager::widget([
        'pagination' => $pagination,
    ]);
    ?>
    <?/*= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => '_item',
    ]) */?>
</div>
