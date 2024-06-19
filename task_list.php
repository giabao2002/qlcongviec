<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<?php if ($_SESSION['login_type'] != 4 && $_SESSION['login_type'] != 3) : ?>
				<div class="card-tools">
					<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_project"><i class="fa fa-plus"></i> Thêm dự án</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Dự án</th>
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
					$where2 = "WHERE department_id IN ($department_ids_string)";

					$stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
					$qry = $conn->query("SELECT t.*,p.name as pname,p.start_date,p.status as pstatus, p.end_date,p.id as pid FROM task_list t inner join project_list p on p.id = t.project_id $where2 order by p.name asc");
					while ($row = $qry->fetch_assoc()) :
						$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
						unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
						$desc = strtr(html_entity_decode($row['description']), $trans);
						$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
						$tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['pid']}")->num_rows;
						$cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['pid']} and status = 3")->num_rows;
						$prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
						$prog = $prog > 0 ?  number_format($prog, 2) : $prog;
						$prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['pid']}")->num_rows;
						if ($row['pstatus'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])) :
							if ($prod  > 0  || $cprog > 0)
								$row['pstatus'] = 2;
							else
								$row['pstatus'] = 1;
						elseif ($row['pstatus'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])) :
							$row['pstatus'] = 4;
						endif;


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
								$startDate = strtotime($row['start_date']);
								$endDate = strtotime($row['end_date']);
								$remainingDays = ($endDate - $startDate) / (60 * 60 * 24); // Chuyển đổi giây thành ngày
								echo "Còn " . $remainingDays . " ngày";
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
					<?php endwhile; ?>
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