<?php
/**
 * Created by PhpStorm.
 * User: Костя
 * Date: 21.09.2015
 * Time: 4:26
 */

/**@var $friendRequest \yii\mongodb\ActiveRecord */
echo json_encode([
    'status' => !$friendRequest->hasErrors(),
    'errors'=> $friendRequest->getErrors()
]);

