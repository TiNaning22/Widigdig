<?php 
  include '_header.php';
  include '_nav.php';
  include '_sidebar.php'; 
?>

<?php  
  if ( $levelLogin === "kurir") {
    echo "
      <script>
        document.location.href = 'bo';
      </script>
    ";
  }  
?>

<?php
// ambil ID dari URL
$id = abs((int)$_GET['id']);

// Query untuk data customer berdasarkan ID
$customer = query("SELECT * FROM customer WHERE customer_id = $id AND customer_cabang = $sessionCabang")[0];

// cek apakah tombol submit sudah ditekan
if( isset($_POST["submit"]) ){
  // Cek apakah data berhasil diubah atau tidak
  if( editMembership($_POST) > 0 ) {
    echo "
      <script>
        document.location.href = 'jenis-pelanggan?sukses=1';
      </script>
    ";
  } else {
    echo "
      <script>
        alert('Data gagal diubah');
        document.location.href = 'jenis-pelanggan';
      </script>
    ";
  }
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Status Membership</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="bo">Home</a></li>
              <li class="breadcrumb-item"><a href="customer">Customers</a></li>
              <li class="breadcrumb-item"><a href="jenis-pelanggan">Jenis Pelanggan</a></li>
              <li class="breadcrumb-item active">Edit Membership</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Edit Status Membership: <?= $customer['customer_nama']; ?></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="" method="post">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-lg-6">
                    <input type="hidden" name="customer_id" value="<?= $customer['customer_id']; ?>">
                    <div class="form-group">
                      <label for="customer_nama">Nama Customer</label>
                      <input type="text" name="customer_nama" class="form-control" id="customer_nama" value="<?= $customer['customer_nama']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="customer_tlpn">No. Telepon</label>
                      <input type="number" name="customer_tlpn" class="form-control" id="customer_tlpn" value="<?= $customer['customer_tlpn']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="customer_alamat">Alamat</label>
                      <textarea name="customer_alamat" id="customer_alamat" class="form-control" rows="5" readonly><?= $customer['customer_alamat']; ?></textarea>
                    </div>
                  </div>

                  <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="customer_membership">Status Membership</label>
                      <select name="customer_membership" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="1" <?php if(isset($customer['customer_membership']) && $customer['customer_membership'] === "1") echo "selected"; ?>>Member</option>
                        <option value="0" <?php if(!isset($customer['customer_membership']) || $customer['customer_membership'] === "0") echo "selected"; ?>>Non-Member</option>
                      </select>
                    </div>
                    
                    <?php if(isset($customer['customer_membership']) && $customer['customer_membership'] === "1"): ?>
                    <div class="form-group">
                      <label for="membership_date">Tanggal Bergabung</label>
                      <input type="text" class="form-control" value="<?= isset($customer['membership_date']) ? date('d-m-Y', strtotime($customer['membership_date'])) : date('d-m-Y'); ?>" readonly>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer text-right">
                <a href="jenis-pelanggan" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary ml-2">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
</div>

<?php
// Fungsi untuk mengedit status membership
function editMembership($data) {
  global $conn;
  global $sessionCabang;
  
  $customer_id = htmlspecialchars($data['customer_id']);
  $customer_membership = htmlspecialchars($data['customer_membership']);

  // Set tanggal bergabung jika statusnya diubah menjadi member
  $membership_date = '';
  $current_status = query("SELECT customer_membership FROM customer WHERE customer_id = $customer_id")[0];
  
  if($customer_membership === "1" && (!isset($current_status['customer_membership']) || $current_status['customer_membership'] !== "1")) {
    $membership_date = ", membership_date = '".date('Y-m-d')."'";
  }
  
  // Update database - fixed syntax error in the SQL query
  $query = "UPDATE customer SET 
            customer_membership = '$customer_membership'
            $membership_date
            WHERE customer_id = $customer_id AND customer_cabang = $sessionCabang
          ";
  mysqli_query($conn, $query);
  
  return mysqli_affected_rows($conn);
}
?>

<?php include '_footer.php'; ?>