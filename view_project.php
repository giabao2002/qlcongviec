<?php
include 'db_connect.php';
$stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
$qry = $conn->query("SELECT * FROM project_list where id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
	$$k = $v;
}
$tprog = $conn->query("SELECT * FROM task_list where project_id = {$id}")->num_rows;
$cprog = $conn->query("SELECT * FROM task_list where project_id = {$id} and status = 3")->num_rows;
$prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
$prog = $prog > 0 ?  number_format($prog, 2) : $prog;
$prod = $conn->query("SELECT * FROM user_productivity where project_id = {$id}")->num_rows;
if ($status == 0 && strtotime(date('Y-m-d')) >= strtotime($start_date)) :
	if ($prod  > 0  || $cprog > 0)
		$status = 2;
	else
		$status = 1;
elseif ($status == 0 && strtotime(date('Y-m-d')) > strtotime($end_date)) :
	$status = 4;
endif;
$draft = $conn->query("SELECT * FROM draft_list where id = $draft_id");
$draft = $draft->num_rows > 0 ? $draft->fetch_array() : array();
$department = $conn->query("SELECT * FROM department where id = $department_id");
$department = $department->num_rows > 0 ? $department->fetch_array() : array();
// $manager = $conn->query("SELECT *,concat(lastname,' ',firstname) as name FROM users where id = $manager_id");
// $manager = $manager->num_rows > 0 ? $manager->fetch_array() : array();
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
								<dt><b class="border-bottom border-primary">Dự án</b></dt>
								<dd><?php echo ucwords($draft['name']) ?></dd>
								<dt><b class="border-bottom border-primary">Tên nhiệm vụ</b></dt>
								<dd><?php echo ucwords($name) ?></dd>
								<dt><b class="border-bottom border-primary">Tệp tin</b></dt>
								<dd>
									<?php
									$filenames = explode(',', $filename);
									$filenames = array_filter($filenames);
									foreach ($filenames as $file) {
										$file = trim($file);
										if (is_file('assets/pdf/projects/' . $file)) : ?>
											<a href="<?php echo 'assets/pdf/projects/' . $file ?>" target="_blank"><?php echo $file ?></a><br>
										<?php else : ?>
											<i>Trống</i><br>
									<?php endif;
									}
									?>
								</dd>
								<dt><b class="border-bottom border-primary">Mô tả</b></dt>
								<dd><?php echo html_entity_decode($description) ?></dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt><b class="border-bottom border-primary">Ngày bắt đầu</b></dt>
								<dd><?php echo date("d/m/Y", strtotime($start_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Ngày kết thúc</b></dt>
								<dd><?php echo date("d/m/Y", strtotime($end_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Trạng thái</b></dt>
								<dd>
									<?php
									if ($stat[$status] == 'Chờ') {
										echo "<span class='badge badge-secondary'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Bắt đầu') {
										echo "<span class='badge badge-primary'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Đang làm') {
										echo "<span class='badge badge-info'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Tạm dừng') {
										echo "<span class='badge badge-warning'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Quá hạn') {
										echo "<span class='badge badge-danger'>{$stat[$status]}</span>";
									} elseif ($stat[$status] == 'Xong') {
										echo "<span class='badge badge-success'>{$stat[$status]}</span>";
									}
									?>
								</dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Phòng ban thực hiện</b></dt>
								<dd>
									<?php if (isset($department['id'])) : ?>
										<div class="d-flex align-items-center mt-1">
											<b><?php echo ucwords($department['name']) ?></b>
										</div>
									<?php endif; ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Phòng ban:</b></span>
				</div>
				<div class="card-body">
					<div>
						<b>Quản lý</b>
						<ul class="users-list clearfix">
							<?php
							$manager_id = $department['manager_id'];
							if (!empty($manager_id)) :
								$manager = $conn->query("SELECT *,concat(lastname,' ',firstname) as name FROM users where id = $manager_id");
								while ($row = $manager->fetch_assoc()) :
							?>
									<li>
										<img src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Ảnh đại diện">
										<a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
									</li>
								<?php
								endwhile;
							else : ?>
								<p>Trống</p>
							<?php endif; ?>
						</ul>
					</div>
					<div>
						<b>Thành viên</b>
						<ul class="users-list clearfix">
							<?php
							$user_ids = $department['user_ids'];
							if (!empty($user_ids)) :
								$members = $conn->query("SELECT *,concat(lastname,' ',firstname) as name FROM users where id in ($user_ids) order by concat(firstname,' ',lastname) asc");
								while ($row = $members->fetch_assoc()) :
							?>
									<li>
										<img src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Ảnh đại diện">
										<a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
									</li>
							<?php
								endwhile;
							endif;
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Danh sách công việc:</b></span>
					<?php if ($_SESSION['login_type'] != 4) : ?>
						<div class="card-tools">
							<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_task"><i class="fa fa-plus"></i> Thêm công việc</button>
						</div>
					<?php endif; ?>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-condensed m-0 table-hover">
							<colgroup>
								<col width="5%">
								<col width="25%">
								<col width="30%">
								<col width="15%">
								<col width="15%">
							</colgroup>
							<thead>
								<th>STT</th>
								<th>Công việc</th>
								<th>Mô tả</th>
								<th>Trạng thái</th>
								<th>Hành động</th>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$tasks = $conn->query("SELECT * FROM task_list where project_id = {$id} order by task asc");
								while ($row = $tasks->fetch_assoc()) :
									$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
									unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
									$desc = strtr(html_entity_decode($row['description']), $trans);
									$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
								?>
									<tr>
										<td class="text-center"><?php echo $i++ ?></td>
										<td class=""><b><?php echo ucwords($row['task']) ?></b></td>
										<td class="">
											<p class="truncate"><?php echo strip_tags($desc) ?></p>
										</td>
										<td>
											<?php
											if ($row['status'] == 1) {
												echo "<span class='badge badge-secondary'>Chờ</span>";
											} elseif ($row['status'] == 2) {
												echo "<span class='badge badge-primary'>Đang làm</span>";
											} elseif ($row['status'] == 3) {
												echo "<span class='badge badge-success'>Xong</span>";
											}
											?>
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
												Hành động
											</button>
											<div class="dropdown-menu" style="">
												<a class="dropdown-item view_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-task="<?php echo $row['task'] ?>">Xem</a>
												<?php if ($_SESSION['login_type'] != 4) : ?>
													<div class="dropdown-divider"></div>
													<a class="dropdown-item edit_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-task="<?php echo $row['task'] ?>">Chỉnh sửa</a>
													<div class="dropdown-divider"></div>
													<a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Xóa</a>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php
								endwhile;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<b>Tiến độ công việc</b>
					<div class="card-tools">
						<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_productivity"><i class="fa fa-plus"></i> Thêm tiến độ</button>
					</div>
				</div>
				<div class="card-body">
					<?php
					$progress = $conn->query("SELECT p.*,concat(u.firstname,' ',u.lastname) as uname,u.avatar,t.task FROM user_productivity p inner join users u on u.id = p.user_id inner join task_list t on t.id = p.task_id where p.project_id = $id order by unix_timestamp(p.date_created) desc ");
					while ($row = $progress->fetch_assoc()) :
					?>
						<div class="post">
							<div class="user-block">
								<?php if ($_SESSION['login_id'] == $row['user_id']) : ?>
									<span class="btn-group dropleft float-right">
										<span class="btndropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
											<i class="fa fa-ellipsis-v"></i>
										</span>
										<div class="dropdown-menu">
											<a class="dropdown-item manage_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-task="<?php echo $row['task'] ?>">Chỉnh sửa</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item delete_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Xóa</a>
										</div>
									</span>
								<?php endif; ?>
								<img class="img-circle img-bordered-sm" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="user image">
								<span class="username">
									<a href="#"><?php echo ucwords($row['uname']) ?>[ <?php echo ucwords($row['task']) ?> ]</a>
								</span>

							</div>
							<div>
								<?php if (!empty($row['comment'])) : ?>
									<p><?php echo html_entity_decode($row['comment']) ?></p>
								<?php else : ?>
									<p class="cmt_empty"></p>
								<?php endif; ?>
							</div>
							<div>
								<?php
								$filenames = explode(',', $row['filename']);
								foreach ($filenames as $file) {
									$file = trim($file);
									if (is_file('assets/pdf/reports/' . $file)) : ?>
										<a href="<?php echo 'assets/pdf/reports/' . $file ?>" target="_blank"><?php echo $file ?></a><br>
								<?php endif;
								}
								?>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.users-list>li img {
		border-radius: 50%;
		height: 67px;
		width: 67px;
		object-fit: cover;
	}

	.users-list>li {
		width: 33.33% !important
	}

	.truncate {
		-webkit-line-clamp: 1 !important;
	}

	.post>div>.cmt_empty {
		height: 4vh;
	}

	@media screen and (max-width: 600px) {
		.post>div>.cmt_empty {
			height: 10vh;
		}
	}
</style>
<script>
	$('#new_task').click(function() {
		uni_modal("Thêm công việc mới cho <?php echo ucwords($name) ?>", "manage_task.php?pid=<?php echo $id ?>", "mid-large")
	})
	$('.edit_task').click(function() {
		uni_modal("Chỉnh sửa công việc", "manage_task.php?pid=<?php echo $id ?>&id=" + $(this).attr('data-id'), "mid-large")
	})
	$('.view_task').click(function() {
		uni_modal("Chi tiết công việc", "view_task.php?id=" + $(this).attr('data-id'), "mid-large")
	})
	$('.delete_task').click(function() {
		_conf("Bạn có muốn xóa công việc này không?", "delete_task", [$(this).attr('data-id')])
	})
	$('#new_productivity').click(function() {
		uni_modal("<i class='fa fa-plus'></i> Tiến độ mới", "manage_progress.php?pid=<?php echo $id ?>", 'large')
	})
	$('.manage_progress').click(function() {
		uni_modal("<i class='fa fa-edit'></i> Sửa tiến độ", "manage_progress.php?pid=<?php echo $id ?>&id=" + $(this).attr('data-id'), 'large')
	})
	$('.delete_progress').click(function() {
		_conf("Bạn có muốn xóa tiến độ này không?", "delete_progress", [$(this).attr('data-id')])
	})

	function delete_task($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_task',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Dữ liệu xóa thành công!", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}

	function delete_progress($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_progress',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Dữ liệu xóa thành công!", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>