<?php include('db_connect.php') ?>
<?php
$twhere = "";
if ($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>
<!-- Info boxes -->
<div class="col-12">
  <div class="card">
    <div class="card-body">
      Xin chào <?php echo $_SESSION['login_name'] ?>!
    </div>
  </div>
</div>
<hr>
<?php

$where = "";
if ($_SESSION['login_type'] == 3) {
  $where = " where manager_id = '{$_SESSION['login_id']}' ";
} elseif ($_SESSION['login_type'] == 4) {
  $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
}
?>

<div class="row">
  <div class="col-md-8">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b>Tiến trình dự án</b>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table m-0 table-hover">
            <colgroup>
              <col width="5%">
              <col width="30%">
              <col width="35%">
              <col width="15%">
              <col width="15%">
            </colgroup>
            <thead>
              <th>STT</th>
              <th>Dự án</th>
              <th>Tiến trình</th>
              <th>Trạng thái</th>
              <th></th>
            </thead>
            <tbody>
              <?php
              $i = 1;
              $stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
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
              $where2 = "";
              if (!empty($department_ids_string)) {
                $where2 = "where department_id IN ($department_ids_string)";

                $qry = $conn->query("SELECT * FROM project_list $where2 order by name asc");
                while ($row = $qry->fetch_assoc()) :
                  $prog = 0;
                  $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
                  $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
                  $prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
                  $prog = $prog > 0 ?  number_format($prog, 2) : $prog;
                  $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
                  if ($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])) :
                    if ($prod  > 0  || $cprog > 0)
                      $row['status'] = 2;
                    else
                      $row['status'] = 1;
                  elseif ($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])) :
                    $row['status'] = 4;
                  endif;
              ?>
                  <tr>
                    <td>
                      <?php echo $i++ ?>
                    </td>
                    <td>
                      <a>
                        <?php echo ucwords($row['name']) ?>
                      </a>
                      <br>
                      <small>
                        Ngày hết hạn: <?php echo date("d/m/Y", strtotime($row['end_date'])) ?>
                      </small>
                    </td>
                    <td class="project_progress">
                      <div class="progress progress-sm">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
                        </div>
                      </div>
                      <small>
                        <?php echo $prog ?>% Công việc
                      </small>
                    </td>
                    <td class="project-state">
                      <?php
                      if ($stat[$row['status']] == 'Chờ') {
                        echo "<span class='badge badge-secondary'>{$stat[$row['status']]}</span>";
                      } elseif ($stat[$row['status']] == 'Bắt đầu') {
                        echo "<span class='badge badge-primary'>{$stat[$row['status']]}</span>";
                      } elseif ($stat[$row['status']] == 'Đang làm') {
                        echo "<span class='badge badge-info'>{$stat[$row['status']]}</span>";
                      } elseif ($stat[$row['status']] == 'Tạm dừng') {
                        echo "<span class='badge badge-warning'>{$stat[$row['status']]}</span>";
                      } elseif ($stat[$row['status']] == 'Quá hạn') {
                        echo "<span class='badge badge-danger'>{$stat[$row['status']]}</span>";
                      } elseif ($stat[$row['status']] == 'Xong') {
                        echo "<span class='badge badge-success'>{$stat[$row['status']]}</span>";
                      }
                      ?>
                    </td>
                    <td>
                      <a class="btn btn-primary btn-sm" href="./index.php?page=view_project&id=<?php echo $row['id'] ?>">
                        <i class="fas fa-folder">
                        </i>
                        Xem
                      </a>
                    </td>
                  </tr>
              <?php endwhile;
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="row">
      <div class="col-12 col-sm-6 col-md-12">
        <div class="small-box bg-light shadow-sm border">
          <div class="inner">
            <h3>
              <?php
              $qryToltal1 = $conn->query("SELECT * FROM project_list $where2")->num_rows;
              echo  $qryToltal1 ?? 0; ?>
            </h3>
            <p><b>Tổng số dự án</b></p>
          </div>
          <div class="icon">
            <i class="fa fa-layer-group"></i>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-12">
        <div class="small-box bg-light shadow-sm border">
          <div class="inner">
            <div>
              <h3><?php
                  $qryToltal2 = $conn->query("SELECT t.*,p.name as pname,p.start_date,p.status as pstatus, p.end_date,p.id as pid FROM task_list t inner join project_list p on p.id = t.project_id $where2")->num_rows;
                  echo $qryToltal2 ?? 0; ?></h3>
              <p><b>Tổng số công việc</b></p>
            </div>
            <?php if (isset($qryToltal2)) : ?>
              <div>
                <p>Công việc đã hoàn thành: <?php echo $conn->query("SELECT 1 FROM task_list t INNER JOIN project_list p ON p.id = t.project_id $where2 AND t.status = 3")->num_rows; ?></p>
                <p>Công việc sắp đến hạn: <?php echo $conn->query("SELECT 1 FROM task_list t INNER JOIN project_list p ON p.id = t.project_id $where2 AND t.status != 3 AND DATEDIFF(t.end_date, CURDATE()) BETWEEN 0 AND 1")->num_rows; ?></p>
                <p>Công việc quá hạn: <?php echo $conn->query("SELECT 1 FROM task_list t INNER JOIN project_list p ON p.id = t.project_id $where2 AND t.status != 3 AND DATEDIFF(CURDATE(), t.end_date) > 0")->num_rows; ?></p>
              </div>
            <?php endif; ?>
            <div>
              <a href="./index.php?page=task_list" class="btn btn-primary btn-sm">Chi tiết</a>
            </div>
          </div>
          <div class="icon">
            <i class="fa fa-tasks"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>