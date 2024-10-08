<?php
session_start();
ini_set('display_errors', 1);
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}
	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	function login()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT *,concat(lastname,' ',firstname) as name FROM users where email = '" . $email . "' and password = '" . md5($password) . "'  ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			return 1;
		} else {
			return 2;
		}
	}
	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '" . $student_code . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['rs_' . $key] = $value;
			}
			return 1;
		} else {
			return 3;
		}
	}

	function save_user()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'password')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (!empty($password)) {
			$data .= ", password=md5('$password') ";
		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set $data");
		} else {
			$old_user_type = $this->db->query("SELECT type FROM users WHERE id = $id")->fetch_object()->type;
			if ($old_user_type == 3 && $type != 3) {
				$this->db->query("UPDATE department SET manager_id = NULL WHERE manager_id = $id");
			} elseif ($old_user_type == 4 && $type != 4) {
				$qry = $this->db->query("SELECT * FROM department WHERE FIND_IN_SET($id, user_ids)")->fetch_object();
				if ($qry) {
					$user_ids = explode(',', $qry->user_ids);
					$key = array_search($id, $user_ids);
					unset($user_ids[$key]);
					$this->db->query("UPDATE department SET user_ids = '" . implode(',', $user_ids) . "' WHERE id = $qry->id");
				}
			}
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if ($save) {
			return 1;
		}
	}
	function signup()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass')) && !is_numeric($k)) {
				if ($k == 'password') {
					if (empty($v))
						continue;
					$v = md5($v);
				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set $data");
		} else {
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if ($save) {
			if (empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if (!in_array($key, array('id', 'cpass', 'password')) && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			$_SESSION['login_id'] = $id;
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'cpass', 'table', 'password')) && !is_numeric($k)) {

				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' " . (!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if ($check > 0) {
			return 2;
			exit;
		}
		if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", avatar = '$fname' ";
		}
		if (!empty($password))
			$data .= " ,password=md5('$password') ";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set $data");
		} else {
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if ($save) {
			foreach ($_POST as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = " . $id);
		if ($delete)
			return 1;
	}
	function save_system_settings()
	{
		extract($_POST);
		$data = '';
		foreach ($_POST as $k => $v) {
			if (!is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if ($_FILES['cover']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'], '../assets/uploads/' . $fname);
			$data .= ", cover_img = '$fname' ";
		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if ($chk->num_rows > 0) {
			$save = $this->db->query("UPDATE system_settings set $data where id =" . $chk->fetch_array()['id']);
		} else {
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if ($save) {
			foreach ($_POST as $k => $v) {
				if (!is_numeric($k)) {
					$_SESSION['system'][$k] = $v;
				}
			}
			if ($_FILES['cover']['tmp_name'] != '') {
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image()
	{
		extract($_FILES['file']);
		if (!empty($tmp_name)) {
			$fname = strtotime(date("Y-m-d H:i")) . "_" . (str_replace(" ", "-", $name));
			$move = move_uploaded_file($tmp_name, 'assets/uploads/' . $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path = explode('/', $_SERVER['PHP_SELF']);
			$currentPath = '/' . $path[1];
			if ($move) {
				return $protocol . '://' . $hostName . $currentPath . '/assets/uploads/' . $fname;
			}
		}
	}
	function save_pdf($file, $path, $i = 0)
	{
		if (isset($file) && $file['tmp_name'] != '') {
			$fname = $file['name'][$i];
			$move = move_uploaded_file($file['tmp_name'][$i], $path . $fname);
			return $move;
		}
	}
	function save_draft()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'pdf_file')) && !is_numeric($k)) {
				if ($k == 'description')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$filesName = '';
		if (isset($_FILES['pdf_file'])) {
			$pdf_files = $_FILES['pdf_file'];
			for ($i = 0; $i < count($pdf_files['name']); $i++) {
				$result = $this->save_pdf($pdf_files, 'assets/pdf/drafts/', $i);
				if ($result) {
					$filesName .= $pdf_files['name'][$i] . ',';
				}
			}
		}
		$data .= ", filename = '" . $filesName . "'";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO draft_list set $data");
		} else {
			$save = $this->db->query("UPDATE draft_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_draft()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM draft_list where id = $id");
		if ($delete) {
			return 1;
		}
	}

	function save_project()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'pdf_file')) && !is_numeric($k)) {
				if ($k == 'description')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$filesName = '';
		if (isset($_FILES['pdf_file'])) {
			$pdf_files = $_FILES['pdf_file'];
			for ($i = 0; $i < count($pdf_files['name']); $i++) {
				$result = $this->save_pdf($pdf_files, 'assets/pdf/projects/', $i);
				if ($result) {
					$filesName .= $pdf_files['name'][$i] . ',';
				}
			}
		}
		$data .= ", filename = '" . $filesName . "'";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO project_list set $data");
		} else {
			$save = $this->db->query("UPDATE project_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}

	function delete_project()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM project_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_task()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'pdf_file', 'view')) && !is_numeric($k)) {
				if ($k == 'description')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$filesName = '';
		if (isset($_FILES['pdf_file'])) {
			$pdf_files = $_FILES['pdf_file'];
			for ($i = 0; $i < count($pdf_files['name']); $i++) {
				$result = $this->save_pdf($pdf_files, 'assets/pdf/tasks/', $i);
				if ($result) {
					$filesName .= $pdf_files['name'][$i] . ',';
				}
			}
		}
		$data .= ", filename = '" . $filesName . "'";
		if (empty($id)) {
			$department_id = $this->db->query("SELECT department_id FROM project_list where id = $project_id")->fetch_assoc()['department_id'];
			$user_ids_query = $this->db->query("SELECT user_ids FROM department WHERE id = $department_id");
			$user_ids_row = $user_ids_query->fetch_assoc();
			$user_ids = $user_ids_row['user_ids'];
			$data .= ", view = '$user_ids'";
			$save = $this->db->query("INSERT INTO task_list set $data");
		} else {
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_task()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function save_progress()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'pdf_file')) && !is_numeric($k)) {
				if ($k == 'comment')
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		$filesName = '';
		if (isset($_FILES['pdf_file'])) {
			$pdf_files = $_FILES['pdf_file'];

			for ($i = 0; $i < count($pdf_files['name']); $i++) {
				$result = $this->save_pdf($pdf_files, 'assets/pdf/reports/', $i);
				if ($result) {
					$filesName .= $pdf_files['name'][$i] . ',';
				}
			}
		}
		$data .= ", filename = '" . $filesName . "'";
		if (empty($id)) {
			$data .= ", user_id={$_SESSION['login_id']} ";

			$save = $this->db->query("INSERT INTO user_productivity set $data");
		} else {
			$save = $this->db->query("UPDATE user_productivity set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}
	function delete_progress()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM user_productivity where id = $id");
		if ($delete) {
			return 1;
		}
	}
	function get_report()
	{
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between '$date_from' and '$date_to' order by unix_timestamp(t.date_created) desc ");
		while ($row = $get->fetch_assoc()) {
			$row['date_created'] = date("M d, Y", strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'], 2);
			$row['child_price'] = number_format($row['child_price'], 2);
			$row['amount'] = number_format($row['amount'], 2);
			$data[] = $row;
		}
		return json_encode($data);
	}
	function save_department()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id', 'user_ids')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		if (isset($user_ids)) {
			$data .= ", user_ids='" . implode(',', $user_ids) . "' ";
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO department set $data");
		} else {
			$save = $this->db->query("UPDATE department set $data where id = $id");
		}
		if ($save) {
			return 1;
		}
	}

	function delete_department()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM department where id = $id");
		if ($delete) {
			return 1;
		}
	}

	function view()
	{
		extract($_GET);
		$user_id = $_SESSION['login_id'];
		$save = $this->db->query("UPDATE task_list SET view = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', view, ','), ',$user_id,', ',')) WHERE id = $id");
		if ($save)
			header("location:index.php?page=view_task&id=$id");
	}
}
