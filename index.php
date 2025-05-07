<?php
if(!is_file('config.php')){
    die('Please create config.php file from config.example.php first!');
}
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
                <input type="text" class="form-control" id="moneyInput" name="money" placeholder="99.99" inputmode="decimal">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" id="detailInput" name="detail" placeholder="รายละเอียด">
            </div>
            <div class="mb-3">
            <?php
            $sql = "SELECT `id`,`name` FROM `groups` WHERE `status` = '1' ORDER BY `sort` ASC";
            $q = $dbi->query($sql);
            $selectItems = ''; // <<-- for js
            if($q->num_rows > 0){
                ?>
                <label for="moneyInput" class="form-label">กลุ่ม</label>
                <select class="form-select" id="groupSelect" name="group">
                    <option value="">เลือกกลุ่ม</option>
                    <?php
                    while($a = $q->fetch_assoc()){
                        $selectItems .= '<option value="'.$a['id'].'">'.$a['name'].'</option>';
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
                                    <a href="javascript:void(0);" onclick="editItem(<?=$a['id'];?>)"><?=$a['name'];?></a>&nbsp;<?=$detail;?>&nbsp;<a href="javascript:void(0);" onclick="delItem('<?=$a['id'];?>')"><i class="bi bi-trash3"></i></a>
                                </td>
                                <td align="right"><?=$a['money'];?>  บ.</td>
                            </tr>
                            <?php
                            $total += $a['money'];
                        }
                        ?>
                        <tr>
                            <td><strong>รวม</strong></td>
                            <td align="right" colspan="2"><?=number_format($total,2);?> บ.</td>
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

        async function editItem(id){
            let res = await loadItem(id);
            if(res.status===200){
                loadForm(res.data);
            }else{
                showError(res.message);
            }
        }

        async function loadItem(id){
            let formData = new FormData();
            formData.append('id', id);
            formData.append('action', 'loadItem');
            let data = await sendPost(formData);
            return data;
        }

        async function loadForm(data){
            const selectOption = '<?=$selectItems;?>';
            const { value: formValues } = await Swal.fire({
            showCancelButton: true,
            cancelButtonText: "ยกเลิก",
            html: `
            <div>
                <input id="editMoney" type="text" class="swal2-input" inputmode="decimal" value="${data.money}">
                <input id="editDetail" type="text" class="swal2-input" value="${data.detail}">
                <select id="editSelect" class="swal2-input">${selectOption}</select>
                <input id="editDate" type="date" class="swal2-input" value="${data.date}">
                <input id="editTime" type="time" class="swal2-input" value="${data.time}">
                <input id="itemId" type="hidden" value="${data.id}"
            </div> 
            `,
            focusConfirm: false,
            preConfirm: () => {
                return [
                    document.getElementById("editMoney").value,
                    document.getElementById("editDetail").value,
                    document.getElementById("editSelect").value,
                    document.getElementById("editDate").value,
                    document.getElementById("editTime").value,
                    document.getElementById("itemId").value
                ];
            }
            });
            if (formValues) {
                let formData = new FormData();
                formData.append('money', formValues[0]);
                formData.append('detail', formValues[1]);
                formData.append('group_id', formValues[2]);
                formData.append('date', formValues[3]);
                formData.append('time', formValues[4]);
                formData.append('id', formValues[5]);
                formData.append('action', 'updateItem');
                
                await sendPost(formData).then((res)=>{
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
                        showError(res.message);
                    }
                });
            }
        }

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
                
                showError('กรุณากรอกข้อมูลให้ครบถ้วน');

            }else if( isNaN(money) ){
                
                showError('ใส่จำนวนเงินเป็นตัวเลข!');
                
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
                        showError(res.message);
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
                            showError(res.message);
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

        async function showError(msg){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: msg
            });
        }
    </script>
</body>
</html>