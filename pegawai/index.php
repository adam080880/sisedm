<?php

require('../database.php');

$sql = "SELECT * FROM pegawai";
$result = $conn->query($sql);

$dataPegawai = [];

while ($pegawai = $result->fetch_assoc()) {
  $dataPegawai[] = $pegawai;
}


function delete($id) {
  var_dump($id);
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
          <h1 class="m-0">Pegawai</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active">Pegawai</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- main content -->
  <section class="content">
    <div class="container-fluid">
      <a href="./add.php" class="btn btn-primary mb-3">Tambah Data</a>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Data</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="daftar_pegawai" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $index = 0 ?>
              <?php foreach ($dataPegawai as $d) : ?>
                <tr>
                  <td><?= ++$index ?></td>
                  <td><?= $d['nama'] ?></td>
                  <td><?= $d['divisi'] ?></td>
                  <td>
                    <a href="./delete.php?id=<?= $d['id']; ?>" class="btn btn-danger" onclick="confirm('Yakin?')">Hapus</a>
                    <a href="./update.php?id=<?= $d['id']; ?>" class="btn btn-success">Ubah</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
            <tfoot>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Aksi</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
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

<script>
  $(function() {
    $("#daftar_pegawai").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

<?php require_once('../closeTag.php') ?>