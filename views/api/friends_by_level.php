<?php
/**
 * Created by PhpStorm.
 * User: Костя
 * Date: 21.09.2015
 * Time: 4:10
 */

//echo json_encode($friendsByLevel);

var_dump(array_map('count', $friendsByLevel));

echo "<div>see json in console</div>
<script>console.log(".json_encode(array_map('array_values',$friendsByLevel)).")</script>";