<!-- Navbar -->
<?php
$query = "SELECT * FROM task_list WHERE view IS NULL OR FIND_IN_SET('" . $_SESSION['login_id'] . "', view) > 0 ORDER BY id DESC LIMIT 5";
$result = $conn->query($query);
$item_count = mysqli_num_rows($result);
?>
<nav class="navbar navbar-expand navbar-primary navbar-dark ">
  <!-- Left navbar links -->
  <ul class="navbar-nav fix-navbar">
    <li>
      <img class="img-topbar" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSEPMP5DJWg6DnGNjCvCaFiZneDNtHgJBlVnQ&s" alt="">
    </li>
    <li>
      <a class="nav-link text-white" href="./">
        <large><b><?php echo 'Ủy ban nhân dân' ?></b></large>
      </a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <?php if ($_SESSION['login_type'] == 4) : ?>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
          <i class="fa fa-bell" aria-hidden="true"></i>
          <?php if ($item_count > 0) : ?>
            <span class="badge badge-warning"><?php echo $item_count; ?></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="notifications" style="left: -2.5em;">
          <h4>Thông báo</h4>
          <?php
          $output = '';
          if ($item_count > 0) {
            $current_item = 0;
            while ($row = mysqli_fetch_array($result)) {
              $current_item++;
              $description = strip_tags(html_entity_decode($row["description"]));
              $maxLength = 50;
              if (mb_strlen($description) > $maxLength) {
                $description = mb_substr($description, 0, $maxLength) . "...";
              }
              $output .= '<a href="ajax.php?action=view&id=' . $row['id'] . '" class="dropdown-item"><strong>Công việc: ' . $row["task"] . '</strong><br /><small><div> Mô tả: ' . $description . '</div></small></a>';
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
<style>
  .navbar{
    height: 6.5vh;
    padding-left: 16px;
  }
</style>
<!-- /.navbar -->
<script>
  $('#manage_account').click(function() {
    uni_modal('Manage Account', 'manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
  })
</script>