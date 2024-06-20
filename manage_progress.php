<?php
include 'db_connect.php';
include 'common.php';
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM user_productivity where id = " . $_GET['id'])->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
}
$file_info_json = getFileInfo($filename, "assets/pdf/reports/");
?>
<div class="container-fluid">
	<form action="" id="manage-progress">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<div class="col-lg-12">
			<div class="row">
				<div class="col-md-5">
					<?php if (!isset($_GET['tid'])) : ?>
						<div class="form-group">
							<label for="" class="control-label">Danh sách công việc</label>
							<select class="form-control form-control-sm select2" name="task_id" required>
								<option></option>
								<?php
								$tasks = $conn->query("SELECT * FROM task_list where project_id = {$_GET['pid']} order by task asc ");
								while ($row = $tasks->fetch_assoc()) :
								?>
									<option value="<?php echo $row['id'] ?>" <?php echo isset($task_id) && $task_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['task']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					<?php else : ?>
						<input type="hidden" name="task_id" value="<?php echo isset($_GET['tid']) ? $_GET['tid'] : '' ?>">
					<?php endif; ?>
					<div class="form-group">
						<label for="">Chủ đề</label>
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo isset($subject) ? $subject : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Ngày</label>
						<input type="date" class="form-control form-control-sm" name="date" value="<?php echo isset($date) ? date("Y-m-d", strtotime($date)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Thời gian bắt đầu</label>
						<input type="time" class="form-control form-control-sm" name="start_time" value="<?php echo isset($start_time) ? date("H:i", strtotime("2020-01-01 " . $start_time)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Thời gian kết thúc</label>
						<input type="time" class="form-control form-control-sm" name="end_time" value="<?php echo isset($end_time) ? date("H:i", strtotime("2020-01-01 " . $end_time)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="" class="control-label">Báo cáo (*.pdf)</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" name="pdf_file[]" accept=".pdf" multiple>
							<label class="custom-file-label" style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" for="custom-file-input">Thêm tệp tin</label>
						</div>
						<div id="file-names" style="margin-top: 10px;"></div>
					</div>
				</div>
				<div class="col-md-7">
					<div class="form-group">
						<label for="">Mô tả tiến độ</label>
						<textarea name="comment" id="" cols="30" rows="10" class="summernote form-control" required=""><?php echo isset($comment) ? $comment : '' ?></textarea>
					</div>
				</div>
			</div>
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
		$('.select2').select2({
			placeholder: "Chọn...",
			width: "100%"
		});
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
	$('#manage-progress').submit(function(e) {
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

		if (isValid) {
			start_load()
			let formData = new FormData(form[0]);
			formData.delete('pdf_file[]');
			selectedFiles.forEach((file, index) => {
				formData.append(`pdf_file[]`, file);
			});
			$.ajax({
				url: 'ajax.php?action=save_progress',
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
							location.reload()
						}, 1500)
					} else {
						alert_toast("Lưu dữ liệu không thành công", "error");
						console.log(resp);
						end_load()
					}
				}
			})
		} else {
			alert_toast('Vui lòng không để trống các trường bắt buộc.', "error");
		}
	})
</script>