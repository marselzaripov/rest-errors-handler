<?php

namespace common\models;

use common\models\query\PostQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\Linkable;
use yii\data\Pagination;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $title
 * @property string $content
 *
 * @property User $user
 */
class Post extends ActiveRecord implements Linkable
{
    public static function tableName()
    {
        return '{{%post}}';
    }

    public function rules()
    {
        return [
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'title' => 'Title',
            'content' => 'Content',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return PostQuery
     */
    public static function find()
    {
        return new PostQuery(get_called_class());
    }

    public function extraFields()
    {
        return [
            'author' => 'user',
        ];
    }

    public function getLinks()
    {
        return [
            'self' => Url::to(['post/view', 'id' => $this->id], true),
        ];
    }



    public static function getAll($pageSize = 5)
    {
        // build a DB query to get all articles
        $query = Post::find();

        // get the total number of articles (but do not fetch the article data yet)
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count, 'pageSize'=>$pageSize]);

        // limit the query using the pagination and retrieve the articles
        $posts = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $data['posts'] = $posts;
        $data['pagination'] = $pagination;

        return $data;
    }
/*
    public static function getPopular()
    {
        return Post::find()->orderBy('viewed desc')->limit(3)->all();
    }

    public static function getRecent()
    {
        return Post::find()->orderBy('date asc')->limit(4)->all();
    }
*/



    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id'=>'id']);
    }

    public function getPostComments()
    {
        return $this->getComments()->where(['status'=>1])->all();
    }
    
    
}
