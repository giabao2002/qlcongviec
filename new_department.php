<?php if (!isset($conn)) {
    include 'db_connect.php';
} ?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form action="" id="manage-department">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="" class="control-label">Tên phòng ban</label>
                            <input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2) : ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Lãnh đạo</label>
                                <select class="form-control form-control-sm select2" name="director_id" required>
                                    <option></option>
                                    <?php
                                    $managers = $conn->query("SELECT *,concat(lastname,' ',firstname) as name FROM users where type = 2 order by concat(lastname,' ',firstname) asc ");
                                    while ($row = $managers->fetch_assoc()) :
                                    ?>
                                        <option value="<?php echo $row['id'] ?>" <?php echo isset($manager_id) && $manager_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="manager_id" value="<?php echo $_SESSION['login_id'] ?>">
                    <?php endif; ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="" class="control-label">Quản lý phòng ban</label>
                            <select class="form-control form-control-sm select2" name="manager_id" required>
                                <option></option>
                                <?php
                                $managers = $conn->query("
                                SELECT users.*, CONCAT(users.lastname, ' ', users.firstname) AS name
                                FROM users
                                LEFT JOIN department ON users.id = department.manager_id
                                WHERE users.type = 3 AND department.manager_id IS NULL
                                ORDER BY CONCAT(users.lastname, ' ', users.firstname) ASC
                                ");
                                while ($row = $managers->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo isset($manager_id) && $manager_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="control-label">Thành viên</label>
                            <select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]" required>
                                <option></option>
                                <?php
                                $depResult = $conn->query("SELECT user_ids FROM department");
                                $userIds = [];
                                while ($row = $depResult->fetch_assoc()) {
                                    $ids = explode(',', $row['user_ids']);
                                    foreach ($ids as $id) {
                                        $userIds[$id] = true;
                                    }
                                }
                                $uniqueUserIds = array_keys($userIds);
                                if (count($uniqueUserIds) > 0) {
                                    $notInCondition = "AND id NOT IN (" . implode(',', $uniqueUserIds) . ")";
                                } else {
                                    $notInCondition = "";
                                }
                                $employees = $conn->query("SELECT *, CONCAT(lastname, ' ', firstname) AS name FROM users WHERE type = 4 $notInCondition ORDER BY CONCAT(lastname, ' ', firstname) ASC");
                                while ($row = $employees->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'], explode(',', $user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer border-top border-info">
            <div class="d-flex w-100 justify-content-center align-items-center">
                <button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-department">Lưu</button>
                <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=department_list'">Hủy</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#manage-department').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_department',
            data: new FormData(form[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast('Lưu dữ liệu thành công', "success");
                    setTimeout(function() {
                        location.href = 'index.php?page=department_list'
                    }, 2000)
                }
            },
        })
    })
</script>