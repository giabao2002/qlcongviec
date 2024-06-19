<?php
include 'db_connect.php';
if (isset($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM task_list where id = " . $_GET['id'])->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<dl>
		<dt><b class="border-bottom border-primary">Công việc</b></dt>
		<dd><?php echo ucwords($task) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Trạng thái</b></dt>
		<dd>
			<?php
			if ($status == 1) {
				echo "<span class='badge badge-secondary'>Chờ</span>";
			} elseif ($status == 2) {
				echo "<span class='badge badge-primary'>Đang làm</span>";
			} elseif ($status == 3) {
				echo "<span class='badge badge-success'>Xong</span>";
			}
			?>
		</dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Tệp tin</b></dt>
		<dd>
			<?php
			$filenames = explode(',', $filename);
			foreach ($filenames as $file) {
				$file = trim($file);
				if (is_file('assets/pdf/tasks/' . $file)) : ?>
					<a href="<?php echo 'assets/pdf/tasks/' . $file ?>" target="_blank"><?php echo $file ?></a><br>
				<?php else : ?>
					<i>Trống</i><br>
			<?php endif;
			}
			?>
		</dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Mô tả</b></dt>
		<dd><?php echo html_entity_decode($description) ?></dd>
	</dl>
</div>