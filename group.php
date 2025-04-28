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
            <h3>จัดการกลุ่ม</h3>
            <a href="javascript:void(0);" class="btn btn-primary" onclick="addFormGroup()">เพิ่ม ➕</a>
        </div>
        <?php
        $q = $dbi->query("SELECT * FROM `groups` WHERE `status` = 1 ORDER BY `id` ASC");
        if($q->num_rows > 0){
            ?>
            <table class="table">
                <?php
                while($row = $q->fetch_assoc()){
                    ?>
                    <tr>
                        <td>
                            <a href="javascript:void(0);" onclick="editGroup('<?=$row['id'];?>','<?=$row['name'];?>')"><?=$row['name'];?></a>
                        </td>
                        <td width="5%"><a href="javascript:void(0);"><i class="bi bi-arrow-down-circle"></i></a></td>
                        <td width="5%"><a href="javascript:void(0);"><i class="bi bi-arrow-up-circle"></i></a></td>
                        <td width="5%">
                            <a href="javascript:void(0);" onclick="delGroupBtn('<?=$row['id'];?>')"><i class="bi bi-trash3"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
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
        async function addFormGroup(){
            const name = await loadForm();
            if (name!==undefined) {

                let formData = new FormData();
                formData.append('name', name);
                formData.append('action', 'saveGroup');

                sendPost(formData).then((res)=>{
                    if(res.status===200){
                        Swal.fire({
                            icon: 'success',
                            title: 'เรียบร้อย',
                            text: res.message
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: res.message,
                        });
                    }
                });

            }
        }

        async function editGroup(id, inputValue){
            const name = await loadForm(inputValue);
            if(name!==undefined){

                let formData = new FormData();
                formData.append('name', name);
                formData.append('id', id);
                formData.append('action', 'updateGroup');

                sendPost(formData).then((res)=>{
                    if(res.status===200){
                        Swal.fire({
                            icon: 'success',
                            title: 'เรียบร้อย',
                            text: res.message,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: res.message,
                        });
                    }
                });
            }
        }

        async function loadForm(inputValue = ''){
            const { value: name } = await Swal.fire({
                title: "ชื่อกลุ่ม",
                input: "text",
                inputValue,
                showCancelButton: true,
                cancelButtonText: "ยกเลิก",
                confirmButtonText: "บันทึก",
                allowOutsideClick: false,
                inputValidator: (value) => {
                    if (!value) {
                        return "ใส่ชื่อด้วยจ้า";
                    }
                }
            });
            return name;
        }

        async function sendPost(formData){
            const response = await fetch('saveGroup.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            return data;
        }

        function delGroupBtn(id){
            Swal.fire({
                title: "แน่ใจว่าจะลบ?",
                showCancelButton: true,
                cancelButtonText: "ยกเลิก",
                confirmButtonColor: "#d33",
                confirmButtonText: "ยืนยันการลบ"
            }).then((result) => {
                
                if (result.isConfirmed) {

                    let formData = new FormData();
                    formData.append('id', id);
                    formData.append('action', 'delGroup');
                    sendPost(formData).then((res)=>{
                        if(res.status===200){
                            Swal.fire({
                                icon: 'success',
                                title: 'เรียบร้อย',
                                text: res.message,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }else{
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
    </script>
</body>
</html>