<?php
include 'db_connect.php';
include 'common.php';
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM task_list where id = " . $_GET['id'])->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
	if (isset($filename)) {
		$file_info_json = getFileInfo($filename, "assets/pdf/tasks/");
	}
} else {
	$pid = $_GET['pid'];
	$project_query = $conn->query("SELECT * FROM project_list WHERE id = $pid");
	$project = $project_query->fetch_assoc();
	$project_start_date = $project['start_date'];
	$project_end_date = $project['end_date'];
	$department_query = $conn->query("SELECT user_ids FROM department WHERE id =" . $project['department_id'] . "");
	if ($department_query->num_rows > 0) {
		$department = $department_query->fetch_assoc();
		$user_ids = $department['user_ids'];
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-task">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<input type="hidden" name="view" value="<?php echo isset($user_ids) ? $user_ids : '' ?>">
		<div class="form-group">
			<label for="">Công việc</label>
			<input type="text" class="form-control form-control-sm" name="task" value="<?php echo isset($task) ? $task : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="">Ngày bắt đầu</label>
			<input type="date" class="form-control form-control-sm" name="start_date" min="<?php echo $project_start_date; ?>" max="<?php echo $project_end_date; ?>" value="<?php echo isset($start_date) ? date("Y-m-d", strtotime($start_date)) : date("Y-m-d", strtotime($project_start_date)) ?>" required>
		</div>
		<div class="form-group">
			<label for="">Ngày kết thúc</label>
			<input type="date" class="form-control form-control-sm" name="end_date" min="<?php echo $project_start_date; ?>" max="<?php echo $project_end_date; ?>" value="<?php echo isset($end_date) ? date("Y-m-d", strtotime($end_date)) : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="">Mô tả</label>
			<textarea name="description" id="" cols="30" rows="10" class="summernote form-control"><?php echo isset($description) ? $description : '' ?></textarea>
		</div>
		<div class="form-group">
			<label for="">Trạng thái</label>
			<select name="status" id="status" class="custom-select custom-select-sm">
				<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Chờ</option>
				<option value="2" <?php echo isset($status) && $status == 2 ? 'selected' : '' ?>>Đang làm</option>
				<option value="3" <?php echo isset($status) && $status == 3 ? 'selected' : '' ?>>Xong</option>
			</select>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Tệp đính kèm</label>
			<div class="custom-file">
				<input type="file" class="custom-file-input" name="pdf_file[]" multiple>
				<label class="custom-file-label" style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" for="custom-file-input">Thêm tệp tin</label>
			</div>
			<div id="file-names" style="margin-top: 10px;"></div>
		</div>
	</form>
</div>

<script src="common.js"></script>
<script>
	$(document).ready(function() {


		$('.summernote').summernote({
			height: 200,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ol', 'ul', 'paragraph', 'height']],
				['table', ['table']],
				['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
			]
		})
	})

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

	$('#manage-task').submit(function(e) {
		e.preventDefault()
		// Kiểm tra xem có trường nào để trống hay không
		var form = $(this);
		var isValid = true;
		var msg = ''
		form.find('input, textarea, select').each(function() {
			if ($(this).prop('required') && $(this).val() == '') {
				isValid = false;
				msg = 'Vui lòng nhập đủ thông tin!'
			}
		});
		var startDate = new Date(form.find('input[name="start_date"]').val());
		var endDate = new Date(form.find('input[name="end_date"]').val());
		if (startDate > endDate) {
			isValid = false;
			msg = 'Ngày bắt đầu không thể sau ngày kết thúc!'
		}
		if (isValid) {
			start_load()
			let formData = new FormData(form[0]);
			formData.delete('pdf_file[]');
			selectedFiles.forEach((file, index) => {
				formData.append(`pdf_file[]`, file);
			});
			$.ajax({
				url: 'ajax.php?action=save_task',
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
							location.reload();
						}, 1500)
					} else {
						alert_toast("Lưu dữ liệu không thành công", "error");
						end_load()
					}
				}
			})
		} else {
			alert_toast(msg, "error");
		}
	})
</script>