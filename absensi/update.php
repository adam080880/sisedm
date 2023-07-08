<?php
require('../database.php');

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Fetch the record from the database
  $sql = "SELECT * FROM absensi WHERE id = $id";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  if (!$row) {
    header("Refresh:0;url=index.php");
  }
}

if (isset($_POST['submit'])) {
  $id = $_POST['id'];
  $tgl_absen = mysqli_real_escape_string($conn, $_POST['date']);
  $newDate = date("Y-m-d", strtotime($tgl_absen));

  $sql = "UPDATE absensi SET tgl_absen = '$newDate' WHERE id = $id";

  $conn->query($sql);
  if ($conn->affected_rows) {
    header("Refresh:0;url=index.php");
    exit;
  }
}

?>

<?php require_once('../header.php') ?>

<!-- Main navbar container -->
<?php require_once('../navbar.php') ?>

<!-- Main Sidebar Container -->
<?php require_once('../sidebar.php') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Ubah Data Absensi Pegawai</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active">Absensi Pegawai</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- main content -->
  <section class="content">
    <div class="container-fluid">
      <form method="post" action="">
        <div class="card-body">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <div class="form-group">
            <label>Date:</label>
            <div class="input-group date" id="reservationdate" data-target-input="nearest">
              <input value="<?= date("m/d/Y", strtotime($row['tgl_absen'])); ?>" name="date" type="text" class="form-control datetimepicker-input" data-target="#reservationdate" />
              <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
          <button name="submit" type="submit" class="btn btn-primary">Ubah Data</button>
        </div>
      </form>
    </div>
  </section>
</div>

<?php require_once('../footer.php') ?>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>

<script>
  $(function() {
    $('.select2').select2()

    //Date picker
    $('#reservationdate').datetimepicker({
      format: 'L'
    });

    $("#daftar_pegawai").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

<?php require_once('../closeTag.php') ?>