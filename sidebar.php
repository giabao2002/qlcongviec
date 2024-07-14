<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <div class="sidebar pb-4 mb-4">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" data-accordion="false">
        <li class="nav-item dropdown">
          <a href="./" class="nav-link nav-home">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Trang chủ
            </p>
          </a>
        </li>
        <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2) : ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-file"></i>
              <p>
                Dự án
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if ($_SESSION['login_type'] == 1) : ?>
                <li class="nav-item">
                  <a href="./index.php?page=new_draft" class="nav-link nav-draft_project tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>Thêm</p>
                  </a>
                </li>
              <?php endif ?>
              <li class="nav-item">
                <a href="./index.php?page=draft_list" class="nav-link nav-draft_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Danh sách</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a href="#" class="nav-link nav-edit_project nav-view_project">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>
              Nhiệm vụ
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <?php if ($_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 1) : ?>
              <li class="nav-item">
                <a href="./index.php?page=new_project" class="nav-link nav-new_project tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Thêm</p>
                </a>
              </li>
            <?php endif; ?>
            <li class="nav-item">
              <a href="./index.php?page=project_list" class="nav-link nav-project_list tree-item">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>Danh sách</p>
              </a>
            </li>
          </ul>
        </li>
        <?php if ($_SESSION['login_type'] == 1) : ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Phòng ban
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=new_department" class="nav-link nav-new_department tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Thêm</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=department_list" class="nav-link nav-department_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Danh sách</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a href="./index.php?page=task_list" class="nav-link nav-task_list">
            <i class="fas fa-tasks nav-icon"></i>
            <p>Công việc</p>
          </a>
        </li>
        <?php if ($_SESSION['login_type'] == 1) : ?>
          <li class="nav-item">
            <a href="./index.php?page=reports" class="nav-link nav-reports">
              <i class="fas fa-th-list nav-icon"></i>
              <p>Báo cáo</p>
            </a>
          </li>
        <?php endif; ?>
        <?php if ($_SESSION['login_type'] == 1) : ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_user">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Người dùng
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Thêm</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Danh sách</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</aside>
<script>
  $(document).ready(function() {
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
    if (s != '')
      page = page + '_' + s;
    if ($('.nav-link.nav-' + page).length > 0) {
      $('.nav-link.nav-' + page).addClass('active')
      if ($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
        $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active')
        $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open')
      }
      if ($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
        $('.nav-link.nav-' + page).parent().addClass('menu-open')
      }

    }
  })
</script>