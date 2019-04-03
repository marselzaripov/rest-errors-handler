<?php
use yii\helpers\Html;
use yii\widgets\ListView;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Публикации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?/*php echo $this->render('_search', ['model' => $searchModel]); */?>

    <!--main content start-->
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <?= ListView::widget([
                        'dataProvider' => $dataProvider,
                        'itemOptions' => ['class' => 'item'],
                        'itemView' => '_item',
                    ]) ?>
                </div>
                <?= $this->render('/partials/sidebar', [
                    'popular'=>$popular,
                    'recent'=>$recent,
                    'categories'=>$categories
                ]);?>
            </div>
        </div>
    </div>
</div>