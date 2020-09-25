<?php
error_reporting(0);
header("Content-Type: application/json");
try {
        if (is_null($module) || !($module instanceof MCRI\REDCapCon2020\REDCapCon2020)) { throw new Exception('No module object'); }
        $result = $module->lookupMaxValue($_POST['value_entered']);
} catch (Exception $ex) {
        http_response_code(500);
        $result = $ex->getMessage();
}
echo json_encode(array('result'=>$result));