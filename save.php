<?php
include_once 'config.php';

$action = sprintf($_POST['action']);
if($action==='saveItem'){
    $money = $_POST['money'];
    $group = $_POST['group'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    if(empty($date)){
        $date = date('Y-m-d');
        $time = date('H:i:s');
    }

    $sql = sprintf("INSERT INTO `money` (`id`, `money`, `group_id`, `date`, `time`, `add_time`, `edit_time`) 
    VALUES (NULL, '%s', '%s', '%s', '%s', NOW(), NOW())", 
        $dbi->real_escape_string($money), 
        $dbi->real_escape_string($group),
        $dbi->real_escape_string($date),
        $dbi->real_escape_string($time)
    );
    $q = $dbi->query($sql);
    if($q===false){
        $res = array('status'=>400, 'message'=>'ไม่สามารถบันทึกข้อมูลได้! '.$dbi->error);
    }else{
        $res = array('status'=>200, 'message'=>'บันทึกข้อมูลเรียบร้อยแล้ว!');
    }

    echo json_encode($res);
    exit;
}