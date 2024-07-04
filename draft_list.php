<?php include 'db_connect.php' ?>
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <?php if ($_SESSION['login_type'] == 1) : ?>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_draft"><i class="fa fa-plus"></i> Thêm nhiệm vụ</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <table class="table tabe-hover table-condensed" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="35%">
                    <col width="15%">
                    <col width="15%">
                    <col width="20%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Dự án</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày hết hạn</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $stat = array("Chờ", "Bắt đầu", "Đang làm", "Tạm dừng", "Quá hạn", "Xong");
                    $where = "";
                    if ($_SESSION['login_type'] == 2) {
                        $where = " where director_id = '{$_SESSION['login_id']}' order by name asc";
                    }
                    $qry = $conn->query("SELECT * FROM draft_list $where");
                    while ($row = $qry->fetch_assoc()) :
                        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
                        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                        // Decode và clean description
                        $desc = strtr(html_entity_decode($row['description']), $trans);
                        $desc = str_replace(["<li>", "</li>"], ["", ", "], $desc);
                    ?>
                        <tr>
                            <th class="text-center"><?php echo $i++ ?></th>
                            <td>
                                <p><b><?php echo ucwords($row['name']) ?></b></p>
                                <p class="truncate"><?php echo strip_tags($desc) ?></p>
                            </td>
                            <td><b><?php echo date("d/m/Y", strtotime($row['start_date'])) ?></b></td>
                            <td><b><?php echo date("d/m/Y", strtotime($row['end_date'])) ?></b></td>
                            <td class="">
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
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Hành động
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item view_draft" href="./index.php?page=view_draft&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Xem</a>
                                    <?php if ($_SESSION['login_type'] != 3 && $_SESSION['login_type'] != 4) : ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="./index.php?page=edit_draft&id=<?php echo $row['id'] ?>">Sửa</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_draft" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Xóa</a>
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

        $('.delete_draft').click(function() {
            _conf("Bạn chắc chắn muốn xóa dự án này?", "delete_draft", [$(this).attr('data-id')])
        })
    })

    function delete_draft($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_draft',
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