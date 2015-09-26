<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "friend_request".
 *
 * @property \MongoId|string $_id
 * @property mixed $from
 * @property mixed $to
 */
class FriendRequest extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['local', 'friend_request'];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'from',
            'to',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'safe'],
            [['from', 'to'], 'required'],
            [['from', 'to'], 'unique', 'targetAttribute'=>['from', 'to']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'from' => 'From',
            'to' => 'To',
        ];
    }

    public static function findById($id)
    {
        return self::findOne(new \MongoId($id));
    }

    public static function findFor($id)
    {
        return self::find()->where(['to' => $id]);
    }

    /**
     * @param $from
     * @param $to
     * @return FriendRequest
     */
    public static function add($from, $to)
    {
        $model = new self;

        $model->setAttributes(['from'=>$from, 'to'=>$to]);

        $model->save();

        return $model;
    }

    public function accept()
    {
        $members = Member::findByIds([$this->from, $this->to])->indexBy('_id')->all();

        Member::makeFriends($members[$this->from], $members[$this->to], true);

        $this->addErrors($members[$this->from]->getErrors());
        $this->addErrors($members[$this->to]->getErrors());

        if (!$this->hasErrors()) {
            $this->delete();
        }

        return $this;
    }
}
