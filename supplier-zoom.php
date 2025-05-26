<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>
<?php  
  if ( $levelLogin === "kasir" && $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }
    
?>


<?php  
// ambil data di URL
$id = abs((int)$_GET['id']);


// query data mahasiswa berdasarkan id
$supplier = query("SELECT * FROM supplier WHERE supplier_id = $id ")[0];
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Detail Data Supplier</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item active">Data Supplier</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Data Supplier</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                          <input type="hidden" name="supplier_id" value="<?= $supplier['supplier_id']; ?>">
                          <label for="supplier_kode">Kode Supplier</label>
                          <input type="text" name="supplier_kode" class="form-control" id="supplier_kode" value="<?= $supplier['supplier_kode']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="supplier_nama">Nama</label>
                          <input type="text" name="supplier_nama" class="form-control" id="supplier_nama" placeholder="Nama Marketing Supplier" value="<?= $supplier['supplier_nama']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="supplier_wa">No. WhatsApp</label>
                            <input type="number" name="supplier_wa" class="form-control" id="barang_harga" placeholder="Contoh: 081234567890" value="<?= $supplier['supplier_wa']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="supplier_bank">Bank</label>
                          <input type="text" name="supplier_bank" class="form-control" id="supplier_bank" placeholder="Contoh: BCA, Mandiri, BRI" value="<?= $supplier['supplier_bank']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="supplier_norekening">No. Rekening</label>
                          <input type="text" name="supplier_norekening" class="form-control" id="supplier_norekening" placeholder="Masukkan No. Rekening" value="<?= $supplier['supplier_norekening']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="supplier_email">Email</label>
                            <input type="email" name="supplier_email" class="form-control" id="supplier_email" value="<?= $supplier['supplier_email']; ?>" readonly>
                        </div>
                        
                    </div>

                    <div class="col-md-6 col-lg-6">
                      <div class="form-group">
                          <label for="supplier_company">Nama Perusahaan Supplier</label>
                          <input type="text" name="supplier_company" class="form-control" id="supplier_company" placeholder="Contoh: PT Semua Produk" value="<?= $supplier['supplier_company']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="supplier_kota">Kota</label>
                          <input type="text" name="supplier_kota" class="form-control" id="supplier_kota" value="<?= $supplier['supplier_kota']; ?>" readonly>
                        </div>
                        <div class="form-group">
                          <label for="supplier_kodepos">Kode Pos</label>
                          <input type="text" name="supplier_kodepos" class="form-control" id="supplier_kodepos" value="<?= $supplier['supplier_kodepos']; ?>" readonly>
                        </div>
                        <div class="form-group ">
                              <label for="supplier_status">Status</label>
                              <div class="">
                                <?php  
                                  if ( $supplier['supplier_status'] === "1" ) {
                                    $status = "Active";
                                  } else {
                                    $status = "Not Active";
                                  }
                                ?>
                                <input type="text" name="supplier_status" class="form-control" id="supplier_status" value="<?= $status; ?>" readonly>
                              </div>
                          </div>

                          <div class="form-group">
                            <label for="supplier_create">Waktu Create</label>
                            <input type="text" name="supplier_create" class="form-control" id="supplier_create" value="<?= $supplier['supplier_create']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="supplier_alamat">Alamat</label>
                            <textarea name="supplier_alamat" id="supplier_alamat" class="form-control" rows="5" readonly="readonly" placeholder="Alamat Lengkap"><?= $supplier['supplier_alamat']; ?></textarea>
                        </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-right">
                  <a href="supplier" class="btn btn-success">Kembali</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>


<?php include '_footer.php'; ?>
<script>
    function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
 
        return false;
      return true;
    }
</script>