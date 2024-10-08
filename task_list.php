<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<?php if ($_SESSION['login_type'] != 4 && $_SESSION['login_type'] != 3) : ?>
				<div class="card-tools">
					<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_project"><i class="fa fa-plus"></i> Thêm nhiệm vụ</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">STT</th>
						<th>Nhiệm vụ</th>
						<th>Công việc</th>
						<th>Ngày bắt đầu</th>
						<th>Ngày kết thúc</th>
						<th>Trạng thái</th>
						<th>Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$where = "";
					if ($_SESSION['login_type'] == 3) {
						$where = " where manager_id = '{$_SESSION['login_id']}' ";
					} elseif ($_SESSION['login_type'] == 4) {
						$where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
					}

					$department_ids = array();
					$departments = $conn->query("SELECT * FROM department $where order by name asc");
					if ($departments->num_rows > 0) {
						while ($row = $departments->fetch_assoc()) {
							$department_ids[] = $row['id'];
						}
					}
					$department_ids_string = implode(',', $department_ids);
					if (!empty($department_ids_string)) {
						$where2 = "where department_id IN ($department_ids_string)";

						$stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
						$qry = $conn->query("SELECT t.*, t.start_date, t.end_date, p.name as pname, p.status as pstatus, p.id as pid FROM task_list t inner join project_list p on p.id = t.project_id $where2 order by p.name asc");
						while ($row = $qry->fetch_assoc()) :
							$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
							unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
							$desc = strtr(html_entity_decode($row['description']), $trans);
							$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
					?>
							<tr>
								<td class="text-center"><?php echo $i++ ?></td>
								<td>
									<p><b><?php echo ucwords($row['pname']) ?></b></p>
								</td>
								<td>
									<p><b><?php echo ucwords($row['task']) ?></b></p>
									<p class="truncate"><?php echo strip_tags($desc) ?></p>
								</td>
								<td><b><?php echo date("d/m/Y", strtotime($row['start_date'])) ?></b></td>
								<td><b><?php echo date("d/m/Y", strtotime($row['end_date'])) ?></b></td>
								<td>
									<?php
									if ($row['status'] != 3) {
										$endDate = strtotime($row['end_date']);
										$currentDate = strtotime(date("Y-m-d"));
										$remainingDays = ($endDate - $currentDate) / (60 * 60 * 24); // Chuyển đổi giây thành ngày

										if ($remainingDays < 0) {
											echo "Quá hạn " . abs($remainingDays) . " ngày";
										} else {
											echo "Còn " . $remainingDays . " ngày";
										}
									} else {
										echo "Đã hoàn thành";
									}
									?>
								</td>
								<td class="text-center">
									<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
										Hành động
									</button>
									<div class="dropdown-menu" style="">
										<a class="dropdown-item view_project" href="./index.php?page=view_task&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Xem</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item new_productivity" data-pid='<?php echo $row['pid'] ?>' data-tid='<?php echo $row['id'] ?>' data-task='<?php echo ucwords($row['task']) ?>' href="javascript:void(0)">Thêm tiến độ</a>
									</div>
								</td>
							</tr>
					<?php endwhile;
					} ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table p {
		margin: unset !important;
	}

	table td {
		vertical-align: middle !important
	}
</style>
<script>
	$(document).ready(function() {
		$('#list').dataTable()
		$('.new_productivity').click(function() {
			uni_modal("<i class='fa fa-plus'></i> New Progress for: " + $(this).attr('data-task'), "manage_progress.php?pid=" + $(this).attr('data-pid') + "&tid=" + $(this).attr('data-tid'), 'large')
		})
	})

	function delete_project($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_project',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Xóa dữ liệu thành công!", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>