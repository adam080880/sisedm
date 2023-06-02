<?php

require('database.php');

$month = date('m');
$formattedMonth = date('M');
$year = date('Y');
$dayInThisMonth = cal_days_in_month(CAL_GREGORIAN, +$month, +$year);

$totalPegawaiQuery = $conn->query('SELECT COUNT(id) as countPegawai FROM pegawai;');
$totalPegawai = $totalPegawaiQuery->fetch_assoc()['countPegawai'];

$totalDivisiQuery = $conn->query("
SELECT COUNT(countDivisiSubQuery.countDivisi) as countDivisi FROM (
	SELECT COUNT(divisi) as countDivisi FROM pegawai GROUP BY divisi
) as countDivisiSubQuery;
");
$totalDivisi = $totalDivisiQuery->fetch_assoc()['countDivisi'];

$totalGajiBulanIniQuery = $conn->query("
  SELECT SUM(absence * gaji) as sumTotalGaji FROM (
    SELECT pegawai.id, COUNT(absensi.id) as absence, gaji.gaji as gaji
    FROM pegawai
    LEFT JOIN absensi ON pegawai.id = absensi.pegawai_id
    LEFT JOIN gaji ON pegawai.id = gaji.pegawai_id
    WHERE MONTH(absensi.tgl_absen) = $month AND YEAR(absensi.tgl_absen) = $year
    GROUP BY pegawai.id
  ) as totalGajiSubQuery;
");
$sumTotalGaji = $totalGajiBulanIniQuery->fetch_assoc();
$totalGajiBulanIni = $sumTotalGaji['sumTotalGaji'] ? $sumTotalGaji['sumTotalGaji'] : 0;

$absenceMetadataQuery = $conn->query("
  SELECT (
    SELECT SUM(absence.countAbsence) as sumCountAbsence
    FROM (
      SELECT pegawai.id, COUNT(absensi.id) as countAbsence
      FROM pegawai
      LEFT JOIN absensi ON absensi.pegawai_id = pegawai.id
      WHERE MONTH(absensi.tgl_absen) = $month AND YEAR(absensi.tgl_absen) = $year
      GROUP BY pegawai.id
    ) AS absence
  ) as sumCountAbsence,
  (SELECT COUNT(id) FROM pegawai) as countPegawai;
");
$absenceMetadata = $absenceMetadataQuery->fetch_assoc();
$avgAbsence = $absenceMetadata['sumCountAbsence'] / $absenceMetadata['countPegawai'];
$persentaseAbsensiRataRata = number_format(($avgAbsence / $dayInThisMonth) * 100, 2, '.', '');

$dataAbsenQuery = $conn->query("
  SELECT pegawai.*, COUNT(absensi.id) absence
  FROM pegawai LEFT JOIN absensi ON pegawai.id = absensi.pegawai_id
  WHERE MONTH(absensi.tgl_absen) = $month AND YEAR(absensi.tgl_absen) = $year
  GROUP BY pegawai.id
  ORDER BY COUNT(absensi.id) DESC
  LIMIT 5;
");
$dataAbsen = [];

while ($absen = $dataAbsenQuery->fetch_assoc()) {
  $dataAbsen[] = $absen;
}

$dataGajiQuery = $conn->query("
  SELECT pegawai.*, COUNT(absensi.id) as absence, gaji.gaji as gaji, gaji.gaji * COUNT(absensi.id) as totalGaji
  FROM pegawai
  LEFT JOIN absensi ON pegawai.id = absensi.pegawai_id
  LEFT JOIN gaji ON pegawai.id = gaji.pegawai_id
  WHERE MONTH(absensi.tgl_absen) = $month AND YEAR(absensi.tgl_absen) = $year
  GROUP BY pegawai.id
  ORDER BY gaji.gaji * COUNT(absensi.id) DESC
  LIMIT 5;
");
$dataGaji = [];

while ($gaji = $dataGajiQuery->fetch_assoc()) {
  $dataGaji[] = $gaji;
}

?>

<?php require_once('header.php') ?>

<!-- Main navbar container -->
<?php require_once('navbar.php') ?>

<!-- Main Sidebar Container -->
<?php require_once('sidebar.php') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?= $totalDivisi ?></h3>

              <p>Total Divisi</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= $totalPegawai ?></h3>

              <p>Total Pegawai</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= $persentaseAbsensiRataRata ?><sup style="font-size: 20px">%</sup></h3>

              <p>Persentase Kehadiran (Avg)</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= number_format($totalGajiBulanIni, 0, ',', '.') ?></h3>

              <p>Total Gaji Bulan Ini (<?= $formattedMonth ?> <?= $year ?>)</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Top 5 Absensi Bulan Ini (<?= $formattedMonth ?> <?= $year ?>)</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="absensi_pegawai" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Kehadiran</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $index = 0 ?>
                  <?php foreach ($dataAbsen as $d) : ?>
                    <tr>
                      <td><?= ++$index ?></td>
                      <td><?= $d['nama'] ?></td>
                      <td><?= $d['divisi'] ?></td>
                      <td><?= $d['absence'] ?> / <?= $dayInThisMonth ?></td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Kehadiran</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
        <div class="col-sm-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Top 5 Gaji Karyawan Prorate kehadiran (<?= $formattedMonth ?> <?= $year ?>)</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="gaji_pegawai" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Nominal (<?= $formattedMonth ?> <?= $year ?>)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $index = 0 ?>
                  <?php foreach ($dataGaji as $d) : ?>
                    <tr>
                      <td><?= ++$index ?></td>
                      <td><?= $d['nama'] ?></td>
                      <td><?= $d['divisi'] ?></td>
                      <td><?= number_format($d['totalGaji'] ? $d['totalGaji'] : 0, 0, ',', '.') ?></td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Nominal (<?= $formattedMonth ?> <?= $year ?>)</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once('footer.php') ?>
<script src="./plugins/datatables/jquery.dataTables.min.js"></script>
<script src="./plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="./plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="./plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="./plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="./plugins/jszip/jszip.min.js"></script>
<script src="./plugins/pdfmake/pdfmake.min.js"></script>
<script src="./plugins/pdfmake/vfs_fonts.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="./plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
  $(function() {
    $("#absensi_pegawai").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $("#gaji_pegawai").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

<?php require_once('closeTag.php') ?>