<?php

namespace common\models;

use Yii;
use yii\base\Model;

class CommentForm extends Model
{
    public $comment;
    
    public function rules()
    {
        return [
            [['comment'], 'required'],
            [['comment'], 'string', 'length' => [3,250]]
        ];
    }

    public function saveComment($post_id)
    {
        $comment = new Comment;
        $comment->text = $this->comment;
        $comment->user_id = Yii::$app->user->id;
        $comment->post_id = $post_id;
        $comment->status = 1;
        $comment->date = date('Y-m-d');
        return $comment->save();

    }
}