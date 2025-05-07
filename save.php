<?php
include_once 'config.php';

$action = sprintf($_POST['action']);
if($action==='saveItem'){
    $money = $_POST['money'];
    $detail = $_POST['detail'];
    $group = $_POST['group'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    if(empty($date)){
        $date = date('Y-m-d');
        $time = date('H:i:s');
    }

    $sql = sprintf("INSERT INTO `money` (`id`, `money`, `detail`, `group_id`, `date`, `time`, `add_time`, `edit_time`) 
    VALUES (NULL, '%s', '%s', '%s', '%s', '%s', NOW(), NOW())", 
        $dbi->real_escape_string($money),
        $dbi->real_escape_string($detail),
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
}elseif($action==='updateItem'){

    $sql = sprintf("UPDATE `money` SET `money` = '%s', 
    `detail` = '%s', 
    `group_id` = '%s', 
    `date` = '%s', 
    `time` = '%s',
    `edit_time` = NOW() WHERE `id` = '%s' ",
        $dbi->real_escape_string($_POST['money']),
        $dbi->real_escape_string($_POST['detail']),
        $dbi->real_escape_string($_POST['group_id']),
        $dbi->real_escape_string($_POST['date']),
        $dbi->real_escape_string($_POST['time']),
        $dbi->real_escape_string($_POST['id'])
    );
    $q = $dbi->query($sql);
    if($q===false){
        $res = array('status'=>400, 'message'=>'ไม่สามารถบันทึกข้อมูลได้! '.$dbi->error);
    }else{
        $res = array('status'=>200, 'message'=>'อัพเดทข้อมูลเรียบร้อยแล้ว!');
    }
    echo json_encode($res);
    exit;

}elseif ($action==='delItem') { 
    $id = $_POST['id'];

    $sql = sprintf("DELETE FROM `money` WHERE `id` = '%s'", $dbi->real_escape_string($id));
    $q = $dbi->query($sql);
    if($q===false){
        $res = array('status'=>400, 'message'=>'ไม่สามารถดำเนินการได้! '.$dbi->error);
    }else{
        $res = array('status'=>200, 'message'=>'ลบข้อมูลเรียบร้อย!');
    }

    echo json_encode($res);
    exit;
}elseif ($action==='loadItem') { 
    $sql = sprintf("SELECT `id`,`money`,`detail`,`group_id`,`date`,`time` FROM `money` WHERE `id` = '%s' LIMIT 1;", $dbi->real_escape_string($_POST['id']));
    try{
        $q = $dbi->query($sql);
        if($q->num_rows>0){
            $data = $q->fetch_assoc();
            $res = array('status'=>200, 'data'=>$data);
        }else{
            $res = array('status'=>400, 'message'=>'ไม่พบข้อมูล  ');
        }
    }catch(Exception $e){
        $res = array('status'=>400, 'message'=>'ไม่สามารถดำเนินการได้! '.$dbi->error);
    }
    echo json_encode($res);
    exit;
}