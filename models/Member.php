<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * This is the model class for collection "member".
 *
 * @property \MongoId|string $_id
 * @property mixed $name
 * @property mixed $friends
 */
class Member extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['local', 'member'];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'friends',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'friends'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => 'Name',
            'friends' => 'Friends',
        ];
    }

    public static function findByIds(array $ids)
    {
        return self::find()->where(['in', '_id', array_values(array_map('intval', array_unique($ids)))]);
    }

    public static function getFriends($id, $level = 1, $asArray = false)
    {
        $friendsIds = self::getFriendsIdsByLevel($id, $level)[$level];

        return self::findByIds($friendsIds)->asArray($asArray)->all();
    }

    public static function getFriendsIds(array $ids)
    {
        $friendsIds = [];

 //       Yii::getLogger()->log('query _id', Yii\log\Logger::LEVEL_PROFILE_BEGIN);

        //$members = self::findByIds($ids)->select(['friends'])->asArray()->all();
        $members = self::find()->getCollection()->find(['in', '_id', $ids], ['friends']);

//        Yii::getLogger()->log('query _id', Yii\log\Logger::LEVEL_PROFILE_END);

        // array_column
        foreach($members as $member) {
            $friendsIds[] = $member['friends'];
        }
        // array unchunk
        $friendsIds = call_user_func_array('array_merge', $friendsIds);

        return array_unique($friendsIds);
    }

    /**
     * @param $id
     * @param int $level
     * @return array
     */
    public static function getFriendsIdsByLevel($id, $level = 0)
    {
        $n = 0;
        $friendsIdsByLevel = [[(int) $id]];

        while ($n < $level) {

            $friendsIdsByLevel[] = self::getFriendsIds($friendsIdsByLevel[$n++]);

        }
        return $friendsIdsByLevel;
    }

    /**
     * @param $id
     * @param int $level
     * @param bool $asArray
     * @return array
     */
    public static function getFriendsByLevel($id, $level = 0, $asArray = false)
    {
        $friendsIdsByLevel = self::getFriendsIdsByLevel($id, $level);

        $friendsIds = count($friendsIdsByLevel) > 1
            // array unchunk
            ? call_user_func_array('array_merge', $friendsIdsByLevel)
            : $friendsIdsByLevel[0];

 //       Yii::getLogger()->log('query data', Yii\log\Logger::LEVEL_PROFILE_BEGIN);
        if ($asArray) {
            $cursor = self::find()->getCollection()->find(['in', '_id', $friendsIds]);
            $friendsByIds=[];
            foreach($cursor as $row) {
                $friendsByIds[$row['_id']] = $row;
            }
        } else {
            $friendsByIds = self::findByIds($friendsIds)->indexBy('_id')
                ->asArray()->all();
        }
//        Yii::getLogger()->log('query data', Yii\log\Logger::LEVEL_PROFILE_END);

        return array_map(function($friendsIds) use ($friendsByIds) {
            return array_intersect_key($friendsByIds, array_flip($friendsIds));
        }, $friendsIdsByLevel);
    }

    public function addFriend(self $friend)
    {
        self::makeFriends($this, $friend);
        return $this;
    }
    public static function makeFriends(self $member1, self $member2, $save = false)
    {
        $member1->_mergeFriends([$member2->_id]);
        $member2->_mergeFriends([$member1->_id]);

        return !$save || $member1->save() && $member2->save();
    }
    private function _mergeFriends(array $friendIds)
    {
        $this->friends = array_values(array_unique(array_merge($this->friends ?: [], $friendIds)));
    }

    public static function isFriends(self $member1, self $member2)
    {
        return array_search($member1->_id, $member2->friends) !== false;
    }
}
