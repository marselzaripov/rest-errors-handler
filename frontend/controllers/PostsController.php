<?php

namespace frontend\controllers;

use Yii;

use frontend\models\PostSearch;
use yii\web\Controller;


class PostsController extends Controller
{
    /*public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);


    }*/
    public function actionIndex()
    {
        $data = PostSearch::getAll(5);
        /*  $popular = Post::getPopular();
          $recent = Post::getRecent();
          $categories = Categoty::getAll();*/

        return $this->render('index',[
            'posts'=>$data['posts'],
            'pagination'=>$data['pagination'],
            /*'popular'=>$popular,
           'recent'=>$recent,
          'categories'=>$categories*/
        ]);
    }
}
