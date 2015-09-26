<?php
/**
 * Created by PhpStorm.
 * User: Костя
 * Date: 26.09.2015
 * Time: 20:06
 */

use yii\helpers\Html;
use \yii\helpers\Url;
?>

<ul>
    <?php foreach ($data as $request):?>
        <li>
            <span><?=$request['memberFrom']['name']?></span>
            <?=Html::a('accept', Url::toRoute(['api/acceptfriendrequest', 'id'=>strval($request['_id'])]))?>
            || <?=Html::a('remove', Url::toRoute(['api/removefriendrequest', 'id'=>strval($request['_id'])]))?>
        </li>
    <?php endforeach?>
</ul>

<div>see json in console</div>
<script>console.log(<?=json_encode($data)?>)</script>