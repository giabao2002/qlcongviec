<?php
if (!isset($conn)) {
  include 'db_connect.php';
}
include 'common.php';
if(isset($filename)){
  $file_info_json = getFileInfo($filename, "assets/pdf/projects/");
}
?>

<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <div class="card-body">
      <form action="" id="manage-project" enctype="multipart/form-data">

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
        <div class="row">
          <div class="col-md-10">
            <div class="form-group">
              <label for="" class="control-label">Tệp pdf</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="pdf_file[]" accept=".pdf" multiple>
                <label class="custom-file-label" style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" for="custom-file-input">Thêm tệp tin</label>
              </div>
              <div id="file-names" style="margin-top: 10px;"></div>
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
<script src="common.js"></script>
<script>
  let selectedFiles = [];
  <?php if (isset($file_info_json)) : ?>
    selectedFiles = <?php echo $file_info_json; ?>;
  <?php endif; ?>

  if (selectedFiles) {
    selectedFiles.forEach(createFileFromData);
    renderPDF(selectedFiles);
  }

  $('.custom-file-input').on('change', function() {
    var newFiles = Array.from(this.files);
    newFiles = newFiles.filter(newFile => !selectedFiles.some(selectedFile => selectedFile.name === newFile.name));
    if (checkFileSize(newFiles, selectedFiles, 40)) {
      selectedFiles = [...selectedFiles, ...newFiles];
      renderPDF(selectedFiles);
    } else {
      this.value = '';
    }
  });

  function removeFile(index) {
    selectedFiles = removeFileFromList(index, selectedFiles);
    renderPDF(selectedFiles);
  }

  $('#manage-project').submit(function(e) {
    e.preventDefault()
    var form = $(this);

    // Kiểm tra xem có trường nào để trống hay không
    var isValid = true;
    form.find('input, textarea, select').each(function() {
      if ($(this).prop('required') && $(this).val() == '') {
        isValid = false;
      }
      if ($(this).prop('name') == 'pdf_file' && $(this).val() != '') {
        var fileExtension = ['pdf'];
        var files = $(this)[0].files;
        for (var i = 0; i < files.length; i++) {
          if ($.inArray(files[i].name.split('.').pop().toLowerCase(), fileExtension) == -1) {
            isValid = false;
            alert_toast('Chỉ cho phép tệp PDF.', 'error');
            break;
          }
        }
      }
    });

    var startDate = new Date(form.find('input[name="start_date"]').val());
    var endDate = new Date(form.find('input[name="end_date"]').val());
    if (startDate > endDate) {
        isValid = false;
        alert_toast('Ngày bắt đầu không thể sau ngày kết thúc.', 'error');
    }


    if (isValid) {
      start_load()
      let formData = new FormData(form[0]);
      formData.delete('pdf_file[]');
      selectedFiles.forEach((file, index) => {
        formData.append(`pdf_file[]`, file);
      });


      $.ajax({
        url: 'ajax.php?action=save_project',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success: function(resp) {
          if (resp == 1) {
            alert_toast('Lưu dữ liệu thành công', "success");
            setTimeout(function() {
              location.replace('index.php?page=project_list')
            }, 1500)
          } else {
            alert_toast("Lưu dữ liệu không thành công", "error");
            console.log(resp);
            end_load()
          }
        },
      });
    }
  })
</script>