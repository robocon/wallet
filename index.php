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
        <form action="javascript:void(0);" method="post" id="formInput" class="mb-5">
            <div class="mb-3">
                <label for="moneyInput" class="form-label">เงิน</label>
                <input type="number" class="form-control" id="moneyInput" name="money" placeholder="99.99" inputmode="numeric">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" id="detailInput" name="detail" placeholder="รายละเอียด">
            </div>
            <div class="mb-3">
            <?php
            $sql = "SELECT `id`,`name` FROM `groups` WHERE `status` = '1' ORDER BY `sort` ASC";
            $q = $dbi->query($sql);
            if($q->num_rows > 0){
                ?>
                <label for="moneyInput" class="form-label">กลุ่ม</label>
                <select class="form-select" id="groupSelect" name="group">
                    <option value="">เลือกกลุ่ม</option>
                    <?php
                    while($a = $q->fetch_assoc()){
                        ?><option value="<?=$a['id'];?>"><?=$a['name'];?></option><?php
                    }
                    ?>
                </select>
                <?php
            }else{
                ?>
                <div class="alert alert-warning" role="alert">กรุณาสร้างกลุ่มให้เรียบร้อยก่อนใช้งาน</div>
                <?php
            }
            ?>
            </div>
            <div class="mb-3">
                <input class="form-check-input" type="checkbox" value="" id="checkDefault" onclick="displayDate()">
                <label class="form-check-label" for="checkDefault">กำหนดวันที่</label>
            </div>
            <div class="mb-3" id="dateInputContainer" style="display:none;">
                <div class="mb-3">
                    <input type="date" class="form-control" name="date" id="dateInput">
                </div>
                <div class="mb-3">
                    <input type="time" class="form-control" name="time" id="timeInput">
                </div>
            </div>
            <div class="mb-3 d-grid gap-2">
                <button type="submit" class="btn btn-primary">บันทึก</button>
                <input type="hidden" name="action" value="saveItem">
            </div>
        </form>

        <div class="mb-2">
            <h3>รายการวันนี้</h3>
            <?php
            $currentDate = date('Y-m-d');
            $sql = "SELECT a.`id`,a.`money`,a.`detail`,b.`name` FROM 
            (SELECT `id`,`money`,`detail`,`group_id` FROM `money` WHERE `date` = '$currentDate' ORDER BY `id` DESC ) AS a 
            LEFT JOIN `groups` AS b ON a.`group_id` = b.`id` ";
            $q = $dbi->query($sql);
            if($q->num_rows > 0){
                ?>
                <table class="table">
                    <tbody>
                        <?php
                        $total = 0;
                        while($a = $q->fetch_assoc()){
                            $detail = '';
                            if(!empty($a['detail'])){
                                $detail = '- '.$a['detail'];
                            }
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:void(0);"><?=$a['name'];?></a>&nbsp;<?=$detail;?>&nbsp;<a href="javascript:void(0);" onclick="delItem('<?=$a['id'];?>')"><i class="bi bi-trash3"></i></a>
                                </td>
                                <td align="right"><?=$a['money'];?>  บ.</td>
                            </tr>
                            <?php
                            $total += $a['money'];
                        }
                        ?>
                        <tr>
                            <td><strong>รวม</strong></td>
                            <td align="right" colspan="2"><?=number_format($total);?> บ.</td>
                        </tr>
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

        document.getElementById('formInput').onsubmit = function() {
            var money = parseFloat(document.getElementById('moneyInput').value);
            var group = document.getElementById('groupSelect').value;
            
            if( money <= 0 || group === '' ){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'กรุณากรอกข้อมูลให้ครบถ้วน!',
                });

            }else if( isNaN(money) ){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'ใส่จำนวนเงินเป็นตัวเลข!',
                }).then(() => {
                    document.getElementById('moneyInput').focus();
                });
                
            }else{

                const form = document.querySelector("#formInput");
                var formData = new FormData(form);
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