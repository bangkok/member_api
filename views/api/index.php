<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use \yii\helpers\Url;
use yii\bootstrap\Tabs;

echo Html::radioList('type', 0, ['get', 'ajax'], ['id'=>'type']);

echo Tabs::widget([
    'items' => [
        [
            'label' => 'Send friend request',
            'content' => renderSendFriendRequest(),
            'active' => true
        ],
        [
            'label' => 'Friend requests',
            'content' => renderShowFriendRequests(),
        ],
        [
            'label' => 'Friends',
            'content' => renderShowMemberFriends(),
        ],
        [
            'label' => 'Friends of friends',
            'content' => renderShowFriendsOfFriends(),
        ],
        [
            'label' => 'Friends by level',
            'content' => renderFriendsByLevel(),
        ],
        [
            'label' => 'Member generate',
            'content' => renderMemberGenerate(),
        ],
    ],
]);

?><script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $('body').on('submit', 'form', function(e){
            if ($('#type input:checked').val() == 1){
                $.ajax(this.action, {
                    method: 'get',
                    data: $(this).serialize(),
                    success: function(resp){
                        $('#result').html(resp);
                    }
                });
                e.preventDefault();
            };
        });
    });

</script>
<div id="result" style="margin: 10px; border: 1px "></div>
<?php


function renderSendFriendRequest() {ob_start()?>
    <div class="row">
        <div class="col-lg-3">
            <h3>Send friend request</h3>
            <?= Html::beginForm(Url::toRoute('api/sendfriendrequest'), 'get', ['target'=>'_blank']);?>
            <div class="form-group">
                <?= renderFromField()?>
            </div>
            <div class="form-group">
                <?= renderToField()?>
            </div>
            <div class="form-group">
                <?= renderSubmit()?>
            </div>
            <?= Html::endForm()?>
        </div>
    </div>
<?php return ob_get_clean();
}

function renderShowFriendRequests() {ob_start()?>
    <div class="row">
        <div class="col-lg-3">
            <h3>Show friend requests</h3>
            <?= Html::beginForm(Url::toRoute('api/showfriendrequest'), 'get', ['target'=>'_blank']);?>
                <div class="form-group">
                    <?= renderToField()?>
                </div>
                <div class="form-group">
                    <?= renderSubmit()?>
                </div>
            <?= Html::endForm()?>
        </div>
    </div>
<?php return ob_get_clean();
}

function renderShowMemberFriends() {ob_start()?>
    <div class="row">
        <div class="col-lg-3">
            <h3>Show member friends</h3>
            <?= Html::beginForm(Url::toRoute('api/friends'), 'get', ['target'=>'_blank']);?>
                <div class="form-group">
                    <?= renderIdField()?>
                </div>
                <div class="form-group">
                    <?= renderSubmit()?>
                </div>
            <?= Html::endForm()?>
        </div>
    </div>
<?php return ob_get_clean();
}

function renderShowFriendsOfFriends() {ob_start()?>
    <div class="row">
        <div class="col-lg-3">
            <h3>Show friends of friends</h3>
            <?= Html::beginForm(Url::toRoute('api/friends'), 'get', ['target'=>'_blank'])?>
            <div class="form-group">
                <?= renderIdField()?>
            </div>
            <div class="form-group">
                <?= renderLevelField()?>
            </div>
            <div class="form-group">
                <?= renderSubmit()?>
            </div>
            <?= Html::endForm()?>
        </div>
    </div>
    <?php return ob_get_clean();
}

function renderFriendsByLevel() {ob_start()?>
    <div class="row">
        <div class="col-lg-3">
            <h3>Show friends by level</h3>
            <?= Html::beginForm(Url::toRoute('api/friendsbylevel'), 'get', ['target'=>'_blank'])?>
            <div class="form-group">
                <?= renderIdField()?>
            </div>
            <div class="form-group">
                <?= renderLevelField()?>
            </div>
            <div class="form-group">
                <?= renderSubmit()?>
            </div>
            <?= Html::endForm()?>
        </div>
    </div>
    <?php return ob_get_clean();
}

function renderMemberGenerate() {ob_start()?>
    <div class="row">
        <div class="col-lg-3">
            <h3>Member generate</h3>
            <?= Html::beginForm(Url::toRoute('api/membergen'), 'get', ['target'=>'_blank'])?>
            <div class="form-group">
                <?= renderLevelField()?>
            </div>
            <div class="form-group">
                <?= renderSubmit()?>
            </div>
            <?= Html::endForm()?>
        </div>
    </div>
    <?php return ob_get_clean();
}

function renderIdField(){
    return Html::label('Member id', 'id', ['class'=>'control-label'])
    . Html::input('number', 'id', null, ['class'=>'form-control', 'required'=>true, 'min'=>1]);
}
function renderLevelField(){
    return Html::label('Friends level', 'n', ['class'=>'control-label'])
    . Html::input('number', 'n', null, ['class'=>'form-control', 'required'=>true, 'min'=>0]);
}
function renderFromField() {
    return Html::label('From (member id)', 'from', ['class'=>'control-label'])
    . Html::input('number', 'from', null, ['class'=>'form-control', 'required'=>true, 'min'=>0]);
}
function renderToField() {
    return Html::label('To (member id)', 'to', ['class'=>'control-label'])
    . Html::input('number', 'to', null, ['class'=>'form-control', 'required'=>true, 'min'=>0]);
}
function renderSubmit(){
    return Html::submitInput(null, ['class'=>'btn btn-primary']);
}