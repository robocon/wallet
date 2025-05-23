<?php
include_once 'config.php';

$action = sprintf($_POST['action']);
if ($action==='saveGroup') {
    $name = $_POST['name'];

    $setSort = 1;
    $sql = "SELECT `sort` FROM `groups` ORDER BY `sort` DESC LIMIT 1";
    $q = $dbi->query($sql);
    if($q->num_rows > 0){
        $a = $q->fetch_assoc();
        $setSort = $a['sort'] + 1;
    }
    

    $sql = sprintf("INSERT INTO `groups` (`id`, `name`, `parent`, `status`, `add_time`, `edit_time`,`sort`) 
    VALUES (NULL, '%s', NULL, '1', NOW(), NOW(), '%s');",
        $dbi->real_escape_string($name),
        $dbi->real_escape_string($setSort)
    );

    $q = false;
    try{
        $q = $dbi->query($sql);
        $res = array('status'=>200, 'message'=>'บันทึกข้อมูลเรียบร้อยแล้ว!');

    }catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        $res = array('status'=>400, 'message'=>'ไม่สามารถบันทึกข้อมูลได้! '.$error);
    }

    echo json_encode($res);
    exit;

}elseif($action==='updateGroup'){
    $id = $_POST['id'];
    $name = $_POST['name'];

    $sql = sprintf("UPDATE `groups` SET `name` = '%s', `edit_time` = NOW() WHERE id = '%s'",
        $dbi->real_escape_string($name),
        $dbi->real_escape_string($id)
    );

    $q = false;
    try{
        $q = $dbi->query($sql);
        $res = array('status'=>200, 'message'=>'บันทึกข้อมูลเรียบร้อยแล้ว!');

    }catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        $res = array('status'=>400, 'message'=>'ไม่สามารถบันทึกข้อมูลได้! '.$error);
    }

    echo json_encode($res);

    exit;

}elseif ($action==='delGroup') {
    $id = $_POST['id'];
    $sql = sprintf("DELETE FROM `groups` WHERE id = '%s'", $dbi->real_escape_string($id));
    
    try{
        $q = $dbi->query($sql);
        $res = array('status'=>200, 'message'=>'ลบข้อมูลเรียบร้อยแล้ว!');
    }catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        $res = array('status'=>400, 'message'=>'ไม่สามารถลบข้อมูลได้! '.$error);
    }

    echo json_encode($res);
    exit;

}