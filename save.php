<?php
include_once 'config.php';

$action = sprintf($_POST['action']);
if($action==='saveItem'){
    $money = $_POST['money'];
    $group = $_POST['group'];

    $date = $_POST['date'];
    if($date !== ''){
        list($year, $month, $day) = explode('-', $date);

    }else{
        $year = date('Y');
        $month = date('m');
        $day = date('d');
    }

    $sql = sprintf("INSERT INTO `money` (`id`, `money`, `group_id`, `year`, `month`, `day`, `add_time`, `edit_time`) 
    VALUES (NULL, '%s', '%s', '$year', '$month', '$day', NOW(), NOW())", 
        $dbi->real_escape_string($money), 
        $dbi->real_escape_string($group)
    );
    $q = $dbi->query($sql);
    if($q===false){
        $res = array('status'=>400, 'message'=>'ไม่สามารถบันทึกข้อมูลได้! '.$dbi->error);
    }else{
        $res = array('status'=>200, 'message'=>'บันทึกข้อมูลเรียบร้อยแล้ว!');
    }

    echo json_encode($res);
    exit;
}elseif ($action==='saveGroup') {
    $name = $_POST['name'];

    $sql = sprintf("INSERT INTO `groups` (`id`, `name`, `parent`, `status`, `add_time`, `edit_time`) 
    VALUES (NULL, %s, NULL, '1', NOW(), NOW());",
        $dbi->real_escape_string($name)
    );

    $q = false;
    try{
        $q = $dbi->query($sql);
    }catch (mysqli_sql_exception $e) {
        // error_log($e->__toString());
    }
    
    if(!$q){
        $res = array('status'=>400, 'message'=>'ไม่สามารถบันทึกข้อมูลได้! '.$dbi->error);
    }else{
        $res = array('status'=>200, 'message'=>'บันทึกข้อมูลเรียบร้อยแล้ว!');
    }

    echo json_encode($res);
    exit;
}elseif ($action==='delGroup') {
    $id = $_POST['id'];
    $sql = sprintf("DELETE FROM `groups` WHERE id = %s", $dbi->real_escape_string($id));
    try{
        $q = $dbi->query($sql);
    }catch (mysqli_sql_exception $e) {
        // error_log($e->__toString());
    }
    if(!$q){
        $res = array('status'=>400, 'message'=>'ไม่สามารถลบข้อมูลได้! '.$dbi->error);
    }else{
        $res = array('status'=>200, 'message'=>'ลบข้อมูลเรียบร้อยแล้ว!');
    }
    echo json_encode($res);
    exit;
}