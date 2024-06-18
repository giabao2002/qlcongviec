<?php if (!isset($conn)) {
  include 'db_connect.php';
} ?>

<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <div class="card-body">
      <form action="" id="manage-project">

        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Tên dự án</label>
              <input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="">Trạng thái</label>
              <select name="status" id="status" class="custom-select custom-select-sm" required>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Chờ</option>
                <option value="3" <?php echo isset($status) && $status == 3 ? 'selected' : '' ?>>Tạm dừng</option>
                <option value="5" <?php echo isset($status) && $status == 5 ? 'selected' : '' ?>>Xong</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Ngày bắt đầu</label>
              <input type="date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="<?php echo isset($start_date) ? date("Y-m-d", strtotime($start_date)) : '' ?>" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Ngày kết thúc</label>
              <input type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d", strtotime($end_date)) : '' ?>" required>
            </div>
          </div>
        </div>
        <div class="row">
          <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2) : ?>
            <div class="col-md-6">
              <div class="form-group">
                <label for="" class="control-label">Phòng ban</label>
                <select class="form-control form-control-sm select2" id="department_id" name="department_id" required>
                  <option></option>
                  <?php
                  $department = $conn->query("SELECT * FROM department");
                  while ($row = $department->fetch_assoc()) :
                  ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo isset($department_id) && $department_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>
          <?php else : ?>
            <input type="hidden" name="department_id" value="<?php echo $_SESSION['login_id'] ?>">
          <?php endif; ?>
        </div>
        <div class="row">
          <div class="col-md-10">
            <div class="form-group">
              <label for="" class="control-label">Mô tả</label>
              <textarea name="description" id="" cols="30" rows="10" class="summernote form-control" required>
						<?php echo isset($description) ? $description : '' ?>
					</textarea>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer border-top border-info">
      <div class="d-flex w-100 justify-content-center align-items-center">
        <button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-project">Lưu</button>
        <button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=project_list'">Hủy</button>
      </div>
    </div>
  </div>
</div>
<script>
  $('#manage-project').submit(function(e) {
    e.preventDefault()
    var form = $(this);

    // Kiểm tra xem có trường nào để trống hay không
    var isValid = true;
    form.find('input, textarea, select').each(function() {
      if ($(this).prop('required') && $(this).val() == '') {
        isValid = false;
      }
    });

    if (isValid) {
      start_load()
      $.ajax({
        url: 'ajax.php?action=save_project',
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
              location.href = 'index.php?page=project_list'
            }, 1500)
          }
        }
      })
    } else {
      alert_toast('Vui lòng nhập đủ thông tin!', "error");
    }
  })
</script>