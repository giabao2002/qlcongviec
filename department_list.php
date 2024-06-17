<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2) : ?>
				<div class="card-tools">
					<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_department"><i class="fa fa-plus"></i> Thêm phòng ban</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="30%">
					<col width="25%">
					<col width="25%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Phòng ban</th>
						<th>Quản lý</th>
						<th>Thành viên</th>
						<th>Hoạt động</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM department order by name asc");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr>
							<th class="text-center"><?php echo $i++ ?></th>
							<td>
								<p><b><?php echo ucwords($row['name']) ?></b></p>
							</td>
							<td>
								<?php
								$managerQuery = $conn->query("SELECT *,concat(lastname,' ',firstname) as name FROM users where id = {$row['manager_id']}");
								if ($managerQuery !== false) {
									$manager = $managerQuery->num_rows > 0 ? $managerQuery->fetch_array() : array();
								} else {
									$manager = array();
									echo "Xảy ra lỗi! ";
								}
								?>
								<?php if (isset($manager['id'])) : ?>
									<div class="d-flex align-items-center mt-1">
										<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $manager['avatar'] ?>" alt="Avatar">
										<b><?php echo ucwords($manager['name']) ?></b>
									</div>
								<?php endif; ?>
							</td>
							<td>
								<?php
								$userIds = explode(',', $row['user_ids']);
								foreach ($userIds as $userId) {
									$userQuery = $conn->query("SELECT *, concat(lastname,' ',firstname) as name FROM users WHERE id = {$userId}");
									if ($userQuery !== false) {
										$user = $userQuery->num_rows > 0 ? $userQuery->fetch_array() : array();
									} else {
										$user = array();
										echo "Xảy ra lỗi! ";
									}
									if (isset($user['id'])) {
										echo '<div class="d-flex align-items-center mt-1">';
										echo '<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/' . $user['avatar'] . '" alt="Avatar">';
										echo '<b>' . ucwords($user['name']) . '</b>';
										echo '</div>';
									}
								}
								?>
							</td>

							<td class="text-center">
								<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									Hoạt động
								</button>
								<div class="dropdown-menu">
									<?php if ($_SESSION['login_type'] != 3 && $_SESSION['login_type'] != 4) : ?>
										<a class="dropdown-item" href="./index.php?page=edit_department&id=<?php echo $row['id'] ?>">Sửa</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_department" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Xóa</a>
									<?php endif; ?>
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

		$('.delete_department').click(function() {
			_conf("Bạn muốn xóa phòng ban này?", "delete_department", [$(this).attr('data-id')])
		})
	})

	function delete_department($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_department',
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