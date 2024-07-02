<?php include 'db_connect.php' ?>
<div class="col-md-12">
  <div class="card card-outline card-success">
    <div class="card-header">
      <b>Tiến độ nhiệm vụ</b>
      <div class="card-tools">
        <button class="btn btn-flat btn-sm bg-gradient-success btn-success" id="print"><i class="fa fa-print"></i> In</button>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive" id="printable">
        <table class="table m-0 table-bordered">
          <colgroup>
            <col width="5%">
            <col width="30%">
            <col width="15%">
            <col width="15%">
            <col width="20%">
            <col width="20%">
          </colgroup>
          <thead>
            <th>STT</th>
            <th>Nhiệm vụ</th>
            <th>Công việc</th>
            <th>Công việc đã xong</th>
            <th>Tiến độ</th>
            <th>Trạng thái</th>
          </thead>
          <tbody>
            <?php
            $i = 1;
            $stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
            $where = "";
            $department = $conn->query("SELECT * FROM department where manager_id = {$_SESSION['login_id']}");
            $department = $department->num_rows > 0 ? $department->fetch_array() : array();
            if ($_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 4) {
              $where = " where department_id = {$department['id']} ";
            }
            $qry = $conn->query("SELECT * FROM project_list $where order by name asc");
            while ($row = $qry->fetch_assoc()) :
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
                <td class="text-center">
                  <?php echo number_format($tprog) ?>
                </td>
                <td class="text-center">
                  <?php echo number_format($cprog) ?>
                </td>
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
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
  $('#print').click(function() {
    start_load()
    var _h = $('head').clone()
    var _p = $('#printable').clone()
    var _d = "<p class='text-center'><b>Project Progress Report as of (<?php echo date("F d, Y") ?>)</b></p>"
    _p.prepend(_d)
    _p.prepend(_h)
    var nw = window.open("", "", "width=900,height=600")
    nw.document.write(_p.html())
    nw.document.close()
    nw.print()
    setTimeout(function() {
      nw.close()
      end_load()
    }, 750)
  })
</script>