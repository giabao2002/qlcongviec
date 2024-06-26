<!-- Navbar -->
<?php
$query = "SELECT * FROM task_list WHERE view IS NULL OR view LIKE '%" . $_SESSION['login_id'] . "%' ORDER BY id DESC LIMIT 5";
$result = $conn->query($query);
$item_count = mysqli_num_rows($result);
?>
<nav class="main-header navbar navbar-expand navbar-primary navbar-dark ">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <?php if (isset($_SESSION['login_id'])) : ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
      </li>
    <?php endif; ?>
    <li>
      <a class="nav-link text-white" href="./" role="button">
        <large><b><?php echo 'Quản lý công việc' ?></b></large>
      </a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <?php if($_SESSION['login_type'] == 4): ?>
      <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
        <i class="fa fa-bell" aria-hidden="true"></i>
        <?php if ($item_count > 0) : ?>
          <span class="badge badge-warning"><?php echo $item_count; ?></span>
        <?php endif; ?>
      </a>
      <div class="dropdown-menu" aria-labelledby="notifications" style="left: -2.5em;">
        <?php
        $output = '';
        if ($item_count > 0) {
          $current_item = 0;
          while ($row = mysqli_fetch_array($result)) {
            $current_item++;
            $output .= '<a href="ajax.php?action=view&id=' . $row['id'] . '" class="dropdown-item"><strong>' . $row["task"] . '</strong><br /><small><em>' . $row["description"] . '</em></small></a>';
            if ($current_item < $item_count) {
              $output .= '<div class="dropdown-divider"></div>';
            }
          }
        } else {
          $output .= '<a href="#" class="text-italic dropdown-item">Không có công việc mới</a>';
        }
        echo $output;
        ?>
      </div>
    </li>
     <?php endif; ?> 
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
        <span>
          <div class="d-felx badge-pill">
            <span class="fa fa-user mr-2"></span>
            <span><b><?php echo ucwords($_SESSION['login_firstname']) ?></b></span>
            <span class="fa fa-angle-down ml-2"></span>
          </div>
        </span>
      </a>
      <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
        <a class="dropdown-item" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i>Tài khoản...</a>
        <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i>Đăng xuất</a>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
<script>
  $('#manage_account').click(function() {
    uni_modal('Manage Account', 'manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
  })
</script>