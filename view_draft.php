<?php
include 'db_connect.php';
$stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
$qry = $conn->query("SELECT * FROM draft_list where id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
	$$k = $v;
}
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
								<dt><b class="border-bottom border-primary">Tên dự án</b></dt>
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
								<dt><b class="border-bottom border-primary">Lãnh đạo</b></dt>
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
</div>
