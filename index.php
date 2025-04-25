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
        <form action="javascript:void(0);" method="post" id="formInput">
            <div class="mb-3">
                <label for="moneyInput" class="form-label">เงิน</label>
                <input type="money" class="form-control" id="moneyInput" name="money" placeholder="99.99">
            </div>
            <div class="mb-3">
                <label for="moneyInput" class="form-label">กลุ่ม</label>
                <select class="form-select" id="groupSelect" name="group">
                    <option value="">เลือกกลุ่ม</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                    <option value="4">Four</option>
                </select>
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
                // console.log(form);

                var formData = new FormData(form);
                // console.log(formData);
                // return false;

                sendPost(formData).then((res)=>{
                    console.log(res);

                });
            }
            /*
            if (money === '' || group === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'กรุณากรอกข้อมูลให้ครบถ้วน!',
                });
            } else {
                var formData = new FormData(this);
                formData.append('action', 'save');
                fetch('save.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: data.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message,
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            */
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