<?php
include_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เป๋าตัง</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="sweetalert2.all.min.js"></script>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container" style="margin-top:5em;">
        <div class="mb-3" style="display: flex; justify-content: space-between;">
            <h3>รายการย้อนหลัง ( 7วัน )</h3>
        </div>
        <div>
            <form action="item.php" method="post" class="mb-5">
                <div class="mb-3">
                    <input type="date" class="form-control" name="dateStart" id="dateInput" placeholder="วันที่">
                </div>
                <div class="mb-3">
                    <input class="form-check-input" type="checkbox" id="checkDefault" onclick="displayDate()">
                    <label class="form-check-label" for="checkDefault">ถึงวันที่</label>
                </div>
                <div class="mb-3" style="display:none;" id="dateInputContainer">
                    <input type="date" class="form-control" name="dateEnd" id="dateInput" placeholder="ถึงวันที่">
                </div>
                <div class="mb-3 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                    <input type="hidden" name="action" value="saveItem">
                </div>
            </form>
        </div>
        <?php
        $defLastTime = strtotime('-7 days');

        $currentDate = date('Y-m-d',$defLastTime);

        $dateStart = $_POST['dateStart'] ?? '';
        $dateEnd = $_POST['dateEnd'] ?? '';
        if(!empty($dateStart) && !empty($dateEnd)){
            $whereDate = "`date` >= '$dateStart' AND `date` <= '$dateEnd'";
        }if(!empty($dateStart) && empty($dateEnd)){
            $whereDate = "`date` = '$dateStart' ";
        }else{
            $whereDate = "`date` >= '$currentDate'";
        }

        $sql = "SELECT a.`date`,a.`id`,a.`money`,a.`detail`,b.`name`,c.`sum`,c.`count` FROM 
        (SELECT `id`,`money`,`detail`,`group_id`,`date` FROM `money` WHERE $whereDate ORDER BY `id` DESC ) AS a 
        LEFT JOIN `groups` AS b ON a.`group_id` = b.`id` 
        LEFT JOIN (SELECT `date`,SUM(`money`) AS `sum`, COUNT(`id`) AS `count` FROM `money` WHERE $whereDate GROUP BY `date`) AS c ON a.`date` = c.`date`
        ORDER BY a.`date` DESC";
        $q = $dbi->query($sql);
        $rows = $q->num_rows;
        if($rows > 0){
            ?>
            <table class="table">
                <tbody>
                    <?php
                    $totalEachDay = 0;
                    $afterSum = $afterDate = '';
                    $i = 1;
                    $itemI = 1;
                    while($a = $q->fetch_assoc()){
                        
                        $detail = '';
                        if(!empty($a['detail'])){
                            $detail = '- '.$a['detail'];
                        }
                        
                        if($afterDate != $a['date']){

                            $itemI = 1;

                            ?>
                            <tr>
                                <td colspan="2" style="background-color: #eee;" align="center"><strong><?=$a['date'];?></strong></td>
                            </tr>
                            <?php
                        }

                        ?>
                        <tr>
                            <td>
                                <a href="javascript:void(0);"><?=$a['name'];?></a>&nbsp;<?=$detail;?>&nbsp;<a href="javascript:void(0);" onclick="delItem('<?=$a['id'];?>')"><i class="bi bi-trash3"></i></a>
                            </td>
                            <td align="right"><?=$a['money'];?> บ.</td>
                        </tr>
                        <?php

                        if($a['count']==$itemI){
                            ?>
                            
                                <tr>
                                    <td><strong>รวม</strong></td>
                                    <td align="right"><?=number_format($a['sum'],2);?> บ.</td>
                                </tr>
                                
                            <?php
                        }
                        
                        $afterDate = $a['date'];
                        $afterSum = $a['sum'];
                        $i++;
                        $itemI++;
                    }
                    ?>
                    
                </tbody>
            </table>
            <?php
        }else{
            ?>
            <div class="alert alert-warning" role="alert">ไม่พบข้อมูล</div>
            <?php
        }
        ?>
    </div>
    <script>
        function displayDate() {
            var checkBox = document.getElementById("checkDefault");
            var dateInput = document.getElementById("dateInputContainer");
            if (checkBox.checked == true){
                dateInput.style.display = "";
            } else {
                dateInput.style.display = "none";
            }
        }

        async function delItem(id){
            Swal.fire({
                title: "แน่ใจว่าจะลบ?",
                showCancelButton: true,
                cancelButtonText: "ยกเลิก",
                confirmButtonColor: "#d33",
                confirmButtonText: "ยืนยันการลบ"
            }).then((result)=>{
                if(result.isConfirmed){

                    let formData = new FormData();
                    formData.append('id', id);
                    formData.append('action', 'delItem');

                    sendPost(formData).then((res)=>{
                        if (res.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: res.message,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: res.message,
                            });
                        }
                    });
                }
            });
        }

        async function sendPost(formData){
            const response = await fetch('save.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            return data;
        }

    </script>
</body>
</html>