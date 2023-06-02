<?php

require('../database.php');

if ($_POST && $_POST['submit']) {
  $pegawaiId = mysqli_real_escape_string($conn, $_POST['id-pegawai']);
  $gajiPegawai = mysqli_real_escape_string($conn, $_POST['gaji-pegawai']);

  $gajiQuery = $conn->query("
    SELECT * FROM gaji
    WHERE pegawai_id = $pegawaiId
  ");

  $gaji = $gajiQuery->fetch_assoc();

  if ($gaji) {
    $conn->query("
      UPDATE gaji
      SET gaji = $gajiPegawai
      WHERE pegawai_id = $pegawaiId
    ");
  } else {
    $conn->query("
      INSERT INTO gaji
      (pegawai_id, gaji)
      VALUES
      ($pegawaiId, $gajiPegawai)
    ");
  }

  header("Refresh:0");

  exit;
}

$dayInThisMonth = 30;

$dataPegawaiQuery = $conn->query("
  SELECT pegawai.*, gaji.gaji
  FROM pegawai LEFT JOIN gaji ON gaji.pegawai_id = pegawai.id;
");

$dataPegawai = [];

while ($pegawai = $dataPegawaiQuery->fetch_assoc()) {
  $dataPegawai[] = $pegawai;
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
          <h1 class="m-0">Gaji Harian Pegawai</h1>
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
                <th>Gaji Harian</th>
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
                  <td>Rp. <?= number_format($d['gaji'] ? $d['gaji'] : 0, 0, '.', ',') ?></td>
                  <td><button data-toggle="modal" data-target="#modal-edit-gaji" data-pegawai='<?= base64_encode(json_encode($d)) ?>' class="btn-edit-gaji btn btn-warning">Ubah Gaji Harian</button></td>
                </tr>
              <?php endforeach ?>
            </tbody>
            <tfoot>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Gaji Harian</th>
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

<div class="modal fade" id="modal-edit-gaji">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="post">
        <div class="modal-header">
          <h4 class="modal-title">Ubah Gaji Harian Pegawai</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id-pegawai" id="id-pegawai" />
          <div class="form-group">
            <label for="nama-pegawai">Nama Pegawai</label>
            <input class="form-control disabled" type="text" id="nama-pegawai" name="nama-pegawai" disabled />
          </div>
          <div class="form-group">
            <label for="nama-pegawai">Divisi</label>
            <input class="form-control disabled" type="text" id="divisi-pegawai" name="divisi-pegawai" disabled />
          </div>
          <div class="form-group">
            <label for="gaji-pegawai">Gaji Harian</label>
            <input class="form-control" type="number" id="gaji-pegawai" name="gaji-pegawai" />
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" value="submit" name="submit">Simpan</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
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
    const datatable = $("#daftar_pegawai").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('#daftar_pegawai').on('draw.dt', function () {
      $('.btn-edit-gaji').on('click', function(btn) {
        const pegawai = JSON.parse(atob($(this).data('pegawai')));

        $('#nama-pegawai').val(pegawai.nama);
        $('#divisi-pegawai').val(pegawai.divisi);
        $('#gaji-pegawai').val(pegawai.gaji);
        $('#id-pegawai').val(pegawai.id);
      });
  } );

    $('.btn-edit-gaji').on('click', function(btn) {
      const pegawai = JSON.parse(atob($(this).data('pegawai')));

      $('#nama-pegawai').val(pegawai.nama);
      $('#divisi-pegawai').val(pegawai.divisi);
      $('#gaji-pegawai').val(pegawai.gaji);
      $('#id-pegawai').val(pegawai.id);
    });
  });
</script>

<?php require_once('../closeTag.php') ?>