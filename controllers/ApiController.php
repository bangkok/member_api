<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\FriendRequest;
use app\models\Member;


class ApiController extends Controller
{

    public function actionIndex()
    {
        echo $this->render('index');
    }

    /**
     * просмотреть список своих друзей
     * @param $id
     * @param int $n
     */
    public function actionFriends($id, $n = 1)
    {
        $friends = Member::getFriends($id, $n, true);

        echo $this->renderAjax('friends', ['friends' => $friends]);
    }

    public function actionFriendsbylevel($id, $n = 1)
    {
        $friendsByLevel = Member::getFriendsByLevel($id, $n, true);

        echo $this->renderAjax('friends_by_level', ['friendsByLevel' => $friendsByLevel]);
    }

    public function actionSendfriendrequest($from, $to)
    {
        $members = Member::findByIds([$from, $to])->all();

        if (count($members) == 2
           and !Member::isFriends($members[0], $members[1])
        ) {
            $friendRequest = FriendRequest::add($from, $to);
        } else {
            $friendRequest = new FriendRequest();
            $friendRequest->addError('from', 'Members are already friends');
        }

        echo $this->renderAjax('friend_request', ['friendRequest' => $friendRequest]);
    }

    public function actionShowfriendrequest($to)
    {
        $friendRequests = FriendRequest::findFor($to)->asArray()->all();

        $membersIds = array_map(function($req){
            return $req['from'];
        }, $friendRequests);

        $members = Member::findByIds($membersIds)->indexBy('_id')->asArray()->all();

        foreach ($friendRequests as &$friendRequest) {
            $friendRequest['memberFrom'] = $members[$friendRequest['from']];
        }

        echo $this->renderAjax('show_friend_request', ['data' => $friendRequests]);
    }

    public function actionAcceptfriendrequest($id)
    {
        $friendRequest = FriendRequest::findById($id);

        if ($friendRequest) {
            $friendRequest->accept();
        } else {
            throw new \yii\web\NotFoundHttpException;
        }
        echo $this->renderAjax('friend_request', ['friendRequest' => $friendRequest]);
    }

    public function actionRemovefriendrequest($id)
    {
        $friendRequest = FriendRequest::findById($id);

        if ($friendRequest) {
            $friendRequest->delete();
        } else {
            throw new \yii\web\NotFoundHttpException;
        }
        echo $this->renderAjax('friend_request', ['friendRequest' => $friendRequest]);
    }

    public function actionMembergen($n)
    {
        set_time_limit(360);

        $total = $count = Member::find()->count();
        $groupNum = 0;
        while ($n > $groupNum) {
            $groupNum++;

            $members = array_fill($total+1, rand(100, 200), []);

            foreach ($members as $id => &$member) {

                $member = new Member;

                $member->_id = $id;

                $member->name = 'member_'. $groupNum .'_'. $id;
            }
            unset($member);

            foreach ($members as $id => $member) {

                $friendIds = array_rand($members, rand(3, 5));

                foreach ($friendIds as $friendId) {
                    $member->addFriend($members[$friendId]);
                }
            }
            if ($total) {
                foreach ($members as $member) {

                    $distantFriendIds = array_map(function() use ($total) {
                        return rand(1, $total);
                    }, array_fill(0, rand(3, 5), null));

                    $distantFriends = Member::findByIds($distantFriendIds)->all();

                    foreach ($distantFriends as $friend) {
                        $friend->addFriend($member)->save();
                    }
                }
            }
            foreach ($members as $member) {
                $member->save();
            }
            $total += count($members);

            echo $groupNum ."\t| ". count($members) ." <br>\n";
        }
        echo "\n<br>Created ". ($total - $count);
        echo "\n<br>Total ". $total;
    }

}
