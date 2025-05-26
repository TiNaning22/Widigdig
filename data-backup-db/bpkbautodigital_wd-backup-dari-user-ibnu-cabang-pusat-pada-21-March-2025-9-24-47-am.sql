

CREATE TABLE `akun` (
  `akun_id` int(11) NOT NULL AUTO_INCREMENT,
  `akun_kode` varchar(20) NOT NULL,
  `akun_nama` varchar(255) NOT NULL,
  `akun_kategori` int(11) NOT NULL COMMENT '1=Aset, 2=Kewajiban, 3=Ekuitas, 4=Pendapatan, 5=Beban, 6=Lainnya',
  `akun_laporan_keuangan` int(11) NOT NULL COMMENT '1=Neraca, 2=Laba Rugi, 3=Arus Kas',
  `akun_saldo_normal` char(1) NOT NULL COMMENT 'D=Debit, K=Kredit',
  `akun_cabang` int(11) NOT NULL,
  PRIMARY KEY (`akun_id`),
  UNIQUE KEY `akun_kode_cabang` (`akun_kode`,`akun_cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO akun VALUES("1","1793713","adit","4","1","D","0");



CREATE TABLE `arus_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `nama_kas` varchar(255) NOT NULL,
  `jenis_kas` enum('masuk','keluar') NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `cabang` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cabang` (`cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO arus_stock VALUES("2","2025-03-17","kasbon","masuk","80000000.00","eded","0","2025-03-17 22:36:27","2025-03-20 13:29:34");
INSERT INTO arus_stock VALUES("3","2025-03-17","kass","masuk","123432.00","ssssssss","0","2025-03-17 22:39:54","");



CREATE TABLE `bank` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_nama` varchar(100) NOT NULL,
  `bank_status` tinyint(1) DEFAULT 1,
  `bank_cabang` int(11) NOT NULL,
  `bank_created` date DEFAULT NULL,
  PRIMARY KEY (`bank_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO bank VALUES("2","Mandiri","1","0","2025-03-17");



CREATE TABLE `barang` (
  `barang_id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_kode` varchar(500) NOT NULL,
  `barang_kode_slug` varchar(500) NOT NULL,
  `barang_kode_count` int(11) NOT NULL,
  `barang_nama` varchar(250) NOT NULL,
  `barang_harga_beli` varchar(250) NOT NULL,
  `barang_harga` varchar(250) NOT NULL,
  `barang_harga_grosir_1` int(11) NOT NULL,
  `barang_harga_grosir_2` int(11) NOT NULL,
  `barang_harga_s2` int(11) NOT NULL,
  `barang_harga_grosir_1_s2` int(11) NOT NULL,
  `barang_harga_grosir_2_s2` int(11) NOT NULL,
  `barang_harga_s3` int(11) NOT NULL,
  `barang_harga_grosir_1_s3` int(11) NOT NULL,
  `barang_harga_grosir_2_s3` int(11) NOT NULL,
  `barang_stock` text NOT NULL,
  `barang_tanggal` varchar(250) NOT NULL,
  `barang_kategori_id` int(11) NOT NULL,
  `kategori_id` varchar(250) NOT NULL,
  `barang_satuan_id` varchar(250) NOT NULL,
  `satuan_id` varchar(250) NOT NULL,
  `satuan_id_2` int(11) NOT NULL,
  `satuan_id_3` int(11) NOT NULL,
  `satuan_isi_1` int(11) NOT NULL,
  `satuan_isi_2` int(11) NOT NULL,
  `satuan_isi_3` int(11) NOT NULL,
  `barang_deskripsi` text NOT NULL,
  `barang_option_sn` int(11) NOT NULL,
  `barang_penyimpanan` varchar(500) NOT NULL,
  `barang_kadaluarsa` varchar(250) NOT NULL,
  `barang_no_batch` varchar(500) NOT NULL,
  `barang_terjual` int(11) NOT NULL,
  `barang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`barang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO barang VALUES("75","754875856","754875856","18","Lameson - 16","7000","50000","0","0","0","0","0","0","0","0","20","14 March 2022 11:14:02 am","6","6","3","2","0","0","1","0","0","Obat Gatal","0","Rak No 1","2026-11-04","6346347466","5","0");
INSERT INTO barang VALUES("76","63634634","63634634","19","Paramek","5000","12000","10000","9000","0","0","0","0","0","0","18","03 November 2022 3:04:22 pm","6","6","2","2","0","0","1","0","0","Obat Pusing","0","Rak No 2","2028-04-03","6346347","2","0");
INSERT INTO barang VALUES("77","64747474757","64747474757","20","Paracetamol","50000","25000","0","0","0","0","0","0","0","0","26","16 December 2022 8:40:50 am","11","11","3","3","0","0","1","0","0","tes","0","Rak No 1","2027-10-07","43643643","13","0");
INSERT INTO barang VALUES("87","1","1","1","komidin","","7000","5000","5000","0","0","0","0","0","0","15","19 March 2025 8:59:36 am","11","11","4","4","0","0","1","0","0","1","0","1","2025-03-20","1","0","0");



CREATE TABLE `barang_internal` (
  `barang_id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_kode` varchar(50) NOT NULL,
  `barang_kode_slug` varchar(50) NOT NULL,
  `barang_kode_count` int(11) NOT NULL DEFAULT 0,
  `barang_nama` varchar(255) NOT NULL,
  `barang_harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_grosir_1` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_grosir_2` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_s2` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_grosir_1_s2` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_grosir_2_s2` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_s3` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_grosir_1_s3` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_harga_grosir_2_s3` decimal(15,2) NOT NULL DEFAULT 0.00,
  `barang_stock` int(11) NOT NULL DEFAULT 0,
  `barang_tanggal` date NOT NULL,
  `barang_kategori_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `barang_satuan_id` int(11) NOT NULL,
  `satuan_id` int(11) NOT NULL,
  `satuan_id_2` int(11) NOT NULL,
  `satuan_id_3` int(11) NOT NULL,
  `satuan_isi_1` int(11) NOT NULL DEFAULT 1,
  `satuan_isi_2` int(11) NOT NULL DEFAULT 1,
  `satuan_isi_3` int(11) NOT NULL DEFAULT 1,
  `barang_deskripsi` text DEFAULT NULL,
  `barang_option_sn` tinyint(1) NOT NULL DEFAULT 0,
  `barang_penyimpanan` varchar(255) DEFAULT NULL,
  `barang_kadaluarsa` date DEFAULT NULL,
  `barang_no_batch` varchar(100) DEFAULT NULL,
  `barang_terjual` int(11) NOT NULL DEFAULT 0,
  `barang_cabang` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`barang_id`),
  KEY `barang_kategori_id` (`barang_kategori_id`),
  KEY `barang_satuan_id` (`barang_satuan_id`),
  KEY `satuan_id_2` (`satuan_id_2`),
  KEY `satuan_id_3` (`satuan_id_3`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO barang_internal VALUES("1","1","","1","tes","0.00","1.00","1.00","1.00","0.00","0.00","0.00","0.00","0.00","0.00","1","2025-03-19","11","11","4","4","0","0","1","0","0","1","0","1","2025-04-01","1","0","0");
INSERT INTO barang_internal VALUES("2","2","","2","tes","0.00","2.00","2.00","2.00","0.00","0.00","0.00","0.00","0.00","0.00","2","2025-03-19","11","11","4","4","0","0","1","0","0","2","0","2","2025-04-02","2","0","0");
INSERT INTO barang_internal VALUES("3","3","","3","3","0.00","3.00","3.00","3.00","0.00","0.00","0.00","0.00","0.00","0.00","3","2025-03-19","10","10","4","4","0","0","1","0","0","3","0","3","2025-04-03","3","0","0");
INSERT INTO barang_internal VALUES("4","86668868","","4","konidin","0.00","2500.00","2000.00","2000.00","0.00","0.00","0.00","0.00","0.00","0.00","30","2025-03-19","11","11","4","4","0","0","1","0","0","obat batuk","0","rak nomor 3","2025-05-31","86876579","0","0");



CREATE TABLE `barang_sn` (
  `barang_sn_id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_sn_desc` text NOT NULL,
  `barang_kode_slug` varchar(500) NOT NULL,
  `barang_sn_status` int(11) NOT NULL,
  `barang_sn_cabang` int(11) NOT NULL,
  PRIMARY KEY (`barang_sn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_nama` varchar(500) NOT NULL,
  `customer_tlpn` varchar(250) NOT NULL,
  `customer_email` varchar(250) NOT NULL,
  `customer_alamat` text NOT NULL,
  `customer_create` varchar(250) NOT NULL,
  `customer_status` varchar(250) NOT NULL,
  `customer_category` int(11) NOT NULL,
  `customer_membership` varchar(1) DEFAULT '0',
  `membership_date` date DEFAULT NULL,
  `customer_cabang` int(11) NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO customer VALUES("19","coba1","098765432112","coba1@gmail.com","sleman","05 February 2025 6:15:12 pm","1","0","1","2025-03-20","0");



CREATE TABLE `ekspedisi` (
  `ekspedisi_id` int(11) NOT NULL AUTO_INCREMENT,
  `ekspedisi_nama` varchar(500) NOT NULL,
  `ekspedisi_status` varchar(250) NOT NULL,
  `ekspedisi_cabang` int(11) NOT NULL,
  PRIMARY KEY (`ekspedisi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO ekspedisi VALUES("5","JNE Cabang","1","1");
INSERT INTO ekspedisi VALUES("8","TokoPedia","0","0");



CREATE TABLE `golongan_produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_golongan` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO golongan_produk VALUES("1","golongan tulus");



CREATE TABLE `hutang` (
  `hutang_id` int(11) NOT NULL AUTO_INCREMENT,
  `hutang_invoice` text NOT NULL,
  `hutang_invoice_parent` text NOT NULL,
  `hutang_date` varchar(500) NOT NULL,
  `hutang_date_time` varchar(500) NOT NULL,
  `hutang_kasir` int(11) NOT NULL,
  `hutang_nominal` varchar(500) NOT NULL,
  `hutang_tipe_pembayaran` int(11) NOT NULL,
  `hutang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`hutang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO hutang VALUES("2","22222","222","10/03/2025","10/03/2025","1","1","1","1");
INSERT INTO hutang VALUES("3","55576","20250315230","2025-03-15","15 March 2025 9:54:56 am","3","27000","0","0");



CREATE TABLE `hutang_awal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_ref` varchar(100) NOT NULL,
  `supplier` varchar(200) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `nominal` decimal(10,2) NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `cabang` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO hutang_awal VALUES("1","264918230","rudi","2025-03-18","1000000.00","2025-03-17","oke","0","2025-03-18 21:44:25","2025-03-18 21:48:43");



CREATE TABLE `hutang_kembalian` (
  `hl_id` int(11) NOT NULL AUTO_INCREMENT,
  `hl_invoice` text NOT NULL,
  `hl_invoice_parent` text NOT NULL,
  `hl_date` varchar(500) NOT NULL,
  `hl_date_time` varchar(500) NOT NULL,
  `hl_nominal` varchar(500) NOT NULL,
  `hl_cabang` int(11) NOT NULL,
  PRIMARY KEY (`hl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO hutang_kembalian VALUES("3","55576","20250315230","2025-03-15","15 March 2025 9:54:56 am","0","0");



CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `penjualan_invoice` text NOT NULL,
  `penjualan_invoice_count` varchar(250) NOT NULL,
  `invoice_tgl` varchar(250) NOT NULL,
  `invoice_customer` varchar(500) NOT NULL,
  `invoice_customer_category` int(11) NOT NULL,
  `invoice_kurir` varchar(500) NOT NULL,
  `invoice_status_kurir` int(11) NOT NULL,
  `invoice_tipe_transaksi` int(11) NOT NULL,
  `invoice_total_beli` int(11) NOT NULL,
  `invoice_total` int(11) NOT NULL,
  `invoice_ongkir` int(11) NOT NULL,
  `invoice_diskon` int(11) NOT NULL,
  `invoice_sub_total` int(11) NOT NULL,
  `invoice_bayar` int(11) NOT NULL,
  `invoice_kembali` int(11) NOT NULL,
  `invoice_kasir` varchar(500) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_date_year_month` varchar(250) NOT NULL,
  `invoice_date_edit` varchar(500) NOT NULL,
  `invoice_kasir_edit` varchar(250) NOT NULL,
  `invoice_total_beli_lama` int(11) NOT NULL,
  `invoice_total_lama` varchar(500) NOT NULL,
  `invoice_ongkir_lama` int(11) NOT NULL,
  `invoice_sub_total_lama` int(11) NOT NULL,
  `invoice_bayar_lama` varchar(500) NOT NULL,
  `invoice_kembali_lama` varchar(500) NOT NULL,
  `invoice_marketplace` varchar(500) NOT NULL,
  `invoice_ekspedisi` int(11) NOT NULL,
  `invoice_no_resi` varchar(500) NOT NULL,
  `invoice_date_selesai_kurir` varchar(500) NOT NULL,
  `invoice_piutang` int(11) NOT NULL,
  `invoice_piutang_dp` varchar(500) NOT NULL,
  `invoice_piutang_jatuh_tempo` varchar(500) NOT NULL,
  `invoice_piutang_lunas` int(11) NOT NULL,
  `invoice_draft` int(11) NOT NULL,
  `invoice_cabang` int(11) NOT NULL,
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO invoice VALUES("4","202502051","1","05 February 2025 6:14:03 pm","0","0","0","1","0","5000","25000","0","0","25000","25000","0","3","2025-02-05","2025-02"," "," ","5000","25000","0","25000","25000","0","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("5","202502052","2","05 February 2025 6:18:44 pm","0","0","0","1","0","5000","12000","0","0","12000","12000","0","3","2025-02-05","2025-02"," "," ","5000","12000","0","12000","12000","0","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("6","202502073","3","07 February 2025 11:26:39 am","19","0","14","1","0","5000","25000","0","0","25000","25000","0","15","2025-02-07","2025-02"," "," ","5000","25000","0","25000","25000","0","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("7","202502214","4","21 February 2025 10:43:11 am","0","0","0","1","0","5000","25000","0","0","25000","25000","0","3","2025-02-21","2025-02"," "," ","5000","25000","0","25000","25000","0","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("8","202502215","5","21 February 2025 11:38:04 am","0","0","0","1","0","5000","25000","0","0","25000","25000","0","3","2025-02-21","2025-02"," "," ","5000","25000","0","25000","25000","0","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("9","202503156","6","15 March 2025 9:10:13 am","0","0","0","1","0","5000","25000","0","0","25000","50000","25000","3","2025-03-15","2025-03"," "," ","5000","25000","0","25000","50000","25000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("10","202503157","7","15 March 2025 9:11:38 am","0","0","0","1","0","5000","25000","0","0","25000","50000","25000","3","2025-03-15","2025-03"," "," ","5000","25000","0","25000","50000","25000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("12","202503159","9","15 March 2025 9:17:35 am","19","0","0","1","0","15000","75000","0","0","75000","75000","0","3","2025-03-15","2025-03"," "," ","15000","75000","0","75000","15000","-60000","","0","-","-","0","15000","2025-04-15","1","0","0");
INSERT INTO invoice VALUES("13","2025031510","10","15 March 2025 9:22:36 am","0","0","0","1","1","20000","100000","0","0","100000","50000","-50000","3","2025-03-15","2025-03"," "," ","20000","100000","0","100000","50000","-50000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("14","2025031511","11","15 March 2025 9:58:11 am","20","0","0","1","0","21000","150000","0","0","150000","10000","-140000","3","2025-03-15","2025-03","2025-03-15","3","21000","150000","0","150000","10000","-140000","","0","-","-","1","10000","2025-04-15","0","0","0");
INSERT INTO invoice VALUES("15","2025031512","12","15 March 2025 9:59:26 am","20","0","0","1","1","12000","62000","0","0","62000","10000","-52000","3","2025-03-15","2025-03"," "," ","12000","62000","0","62000","10000","-52000","","0","-","-","1","10000","2025-03-18","0","0","0");
INSERT INTO invoice VALUES("16","2025031813","13","18 March 2025 10:30:57 am","0","0","0","1","0","50000","25000","0","0","25000","50000","25000","3","2025-03-18","2025-03"," "," ","50000","25000","0","25000","50000","25000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("17","2025031814","14","18 March 2025 10:31:14 am","0","0","14","1","0","50000","25000","0","0","25000","50000","25000","3","2025-03-18","2025-03"," "," ","50000","25000","0","25000","50000","25000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("18","2025031815","15","18 March 2025 10:31:30 am","0","0","0","1","0","50000","25000","0","0","25000","50000","25000","3","2025-03-18","2025-03"," "," ","50000","25000","0","25000","50000","25000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("19","2025031916","16","19 March 2025 2:35:14 pm","0","0","0","1","0","150000","75000","0","5000","70000","100000","30000","3","2025-03-19","2025-03"," "," ","150000","75000","0","70000","100000","30000","","0","-","-","0","0","0","0","0","0");
INSERT INTO invoice VALUES("20","2025031917","17","19 March 2025 2:57:21 pm","0","0","0","1","0","157000","125000","0","0","125000","130000","5000","3","2025-03-19","2025-03"," "," ","157000","125000","0","125000","130000","5000","","0","-","-","0","0","0","0","0","0");



CREATE TABLE `invoice_pembelian` (
  `invoice_pembelian_id` int(11) NOT NULL AUTO_INCREMENT,
  `pembelian_invoice` text NOT NULL,
  `pembelian_invoice_parent` text NOT NULL,
  `invoice_tgl` varchar(250) NOT NULL,
  `invoice_supplier` varchar(500) NOT NULL,
  `invoice_total` int(11) NOT NULL,
  `invoice_bayar` int(11) NOT NULL,
  `invoice_kembali` int(11) NOT NULL,
  `invoice_kasir` varchar(500) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_date_edit` varchar(500) NOT NULL,
  `invoice_kasir_edit` varchar(250) NOT NULL,
  `invoice_total_lama` varchar(500) NOT NULL,
  `invoice_bayar_lama` varchar(500) NOT NULL,
  `invoice_kembali_lama` varchar(500) NOT NULL,
  `invoice_hutang` int(11) NOT NULL,
  `invoice_hutang_dp` varchar(500) NOT NULL,
  `invoice_hutang_jatuh_tempo` varchar(500) NOT NULL,
  `invoice_hutang_lunas` int(11) NOT NULL,
  `invoice_pembelian_cabang` int(11) NOT NULL,
  PRIMARY KEY (`invoice_pembelian_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO invoice_pembelian VALUES("5","28282828","20250315130","15 March 2025 9:45:02 am","2","250000","0","-250000","3                                  ","2025-03-15","2025-03-17","3","250000","10000","-240000","1","10000","2025-03-17","0","0");
INSERT INTO invoice_pembelian VALUES("6","55576","20250315230","15 March 2025 9:54:01 am","4","77000","77000","0","3                                  ","2025-03-15"," "," ","77000","50000","-27000","0","50000","2025-03-16","1","0");



CREATE TABLE `invoice_pembelian_number` (
  `invoice_pembelian_number_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_pembelian_number_input` varchar(250) NOT NULL,
  `invoice_pembelian_number_parent` text NOT NULL,
  `invoice_pembelian_number_user` varchar(250) NOT NULL,
  `invoice_pembelian_number_delete` varchar(250) NOT NULL,
  `invoice_pembelian_cabang` int(11) NOT NULL,
  PRIMARY KEY (`invoice_pembelian_number_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `jasa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_jasa` varchar(255) NOT NULL,
  `satuan` varchar(100) NOT NULL,
  `hna_ppn` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `margin` decimal(5,2) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT 0.00,
  `golongan_produk` int(11) NOT NULL,
  `rak` varchar(100) DEFAULT NULL,
  `cabang` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `jurnal` (
  `jurnal_id` int(11) NOT NULL AUTO_INCREMENT,
  `jurnal_nomor` varchar(50) NOT NULL,
  `jurnal_tanggal` date NOT NULL,
  `jurnal_tipe` varchar(50) NOT NULL COMMENT 'Umum, Penjualan, Pembelian, dll',
  `jurnal_referensi` varchar(100) DEFAULT NULL,
  `jurnal_keterangan` text DEFAULT NULL,
  `jurnal_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Draft, 1=Posted',
  `jurnal_created` datetime NOT NULL,
  `jurnal_created_by` int(11) NOT NULL,
  `jurnal_cabang` int(11) NOT NULL,
  PRIMARY KEY (`jurnal_id`),
  UNIQUE KEY `jurnal_nomor_cabang` (`jurnal_nomor`,`jurnal_cabang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `jurnal_detail` (
  `jurnal_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `jurnal_id` int(11) NOT NULL,
  `jurnal_akun` int(11) NOT NULL,
  `jurnal_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jurnal_kredit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jurnal_keterangan` text DEFAULT NULL,
  `jurnal_cabang` int(11) NOT NULL,
  PRIMARY KEY (`jurnal_detail_id`),
  KEY `jurnal_id` (`jurnal_id`),
  KEY `jurnal_akun` (`jurnal_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `kartu_stok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_id` int(11) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `stok_awal` int(11) NOT NULL DEFAULT 0,
  `jumlah_masuk` int(11) NOT NULL DEFAULT 0,
  `jumlah_keluar` int(11) NOT NULL DEFAULT 0,
  `sisa_stok` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `barang_id` (`barang_id`),
  KEY `tanggal_transaksi` (`tanggal_transaksi`),
  CONSTRAINT `fk_kartu_stok_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`barang_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `kategori` (
  `kategori_id` int(11) NOT NULL AUTO_INCREMENT,
  `kategori_nama` varchar(500) NOT NULL,
  `kategori_status` varchar(250) NOT NULL,
  `kategori_cabang` int(11) NOT NULL,
  PRIMARY KEY (`kategori_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO kategori VALUES("2","Obat Wajib Apotek (OWA)","1","0");
INSERT INTO kategori VALUES("4","Obat Keras","1","0");
INSERT INTO kategori VALUES("6","Obat Bebas","1","0");
INSERT INTO kategori VALUES("8","Obat Bebas Terbatas","1","0");
INSERT INTO kategori VALUES("9","Obat Golongan Narkotika","1","0");
INSERT INTO kategori VALUES("10","Obat Psikotropika","1","0");
INSERT INTO kategori VALUES("11","Obat Herbal","1","0");
INSERT INTO kategori VALUES("12","SATUAN","1","4");



CREATE TABLE `keranjang` (
  `keranjang_id` int(11) NOT NULL AUTO_INCREMENT,
  `keranjang_nama` varchar(500) NOT NULL,
  `keranjang_harga_beli` varchar(250) NOT NULL,
  `keranjang_harga` varchar(250) NOT NULL,
  `keranjang_harga_parent` int(11) NOT NULL,
  `keranjang_harga_edit` int(11) NOT NULL,
  `keranjang_satuan` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `barang_kode_slug` varchar(500) NOT NULL,
  `keranjang_qty` int(11) NOT NULL,
  `keranjang_qty_view` int(11) NOT NULL,
  `keranjang_konversi_isi` int(11) NOT NULL,
  `keranjang_barang_sn_id` int(11) NOT NULL,
  `keranjang_barang_option_sn` int(11) NOT NULL,
  `keranjang_sn` text NOT NULL,
  `keranjang_id_kasir` int(11) NOT NULL,
  `keranjang_id_cek` varchar(500) NOT NULL,
  `keranjang_tipe_customer` int(11) NOT NULL,
  `keranjang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`keranjang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO keranjang VALUES("27","komidin","","7000","7000","0","4","87","1","1","1","1","0","0","0","3","8730","0","0");



CREATE TABLE `keranjang_draft` (
  `keranjang_draf_id` int(11) NOT NULL AUTO_INCREMENT,
  `keranjang_nama` varchar(250) NOT NULL,
  `keranjang_harga_beli` varchar(250) NOT NULL,
  `keranjang_harga` varchar(250) NOT NULL,
  `keranjang_harga_parent` int(11) NOT NULL,
  `keranjang_harga_edit` int(11) NOT NULL,
  `keranjang_satuan` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `barang_kode_slug` varchar(250) NOT NULL,
  `keranjang_qty` int(11) NOT NULL,
  `keranjang_qty_view` int(11) NOT NULL,
  `keranjang_konversi_isi` int(11) NOT NULL,
  `keranjang_barang_sn_id` int(11) NOT NULL,
  `keranjang_barang_option_sn` int(11) NOT NULL,
  `keranjang_sn` text NOT NULL,
  `keranjang_id_kasir` int(11) NOT NULL,
  `keranjang_id_cek` varchar(500) NOT NULL,
  `keranjang_tipe_customer` int(11) NOT NULL,
  `keranjang_draft_status` int(11) NOT NULL,
  `keranjang_invoice` text NOT NULL,
  `keranjang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`keranjang_draf_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `keranjang_pembelian` (
  `keranjang_id` int(11) NOT NULL AUTO_INCREMENT,
  `keranjang_nama` varchar(500) NOT NULL,
  `keranjang_harga` varchar(250) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `keranjang_qty` int(11) NOT NULL,
  `keranjang_id_kasir` int(11) NOT NULL,
  `keranjang_id_cek` varchar(500) NOT NULL,
  `keranjang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`keranjang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `keranjang_transfer` (
  `keranjang_transfer_id` int(11) NOT NULL AUTO_INCREMENT,
  `keranjang_transfer_nama` text NOT NULL,
  `barang_id` int(11) NOT NULL,
  `barang_kode_slug` text NOT NULL,
  `keranjang_transfer_qty` int(11) NOT NULL,
  `keranjang_barang_sn_id` int(11) NOT NULL,
  `keranjang_barang_option_sn` int(11) NOT NULL,
  `keranjang_sn` text NOT NULL,
  `keranjang_transfer_id_kasir` int(11) NOT NULL,
  `keranjang_id_cek` varchar(500) NOT NULL,
  `keranjang_pengirim_cabang` int(11) NOT NULL,
  `keranjang_penerima_cabang` int(11) NOT NULL,
  `keranjang_transfer_cabang` int(11) NOT NULL,
  PRIMARY KEY (`keranjang_transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `laba_bersih` (
  `lb_id` int(11) NOT NULL AUTO_INCREMENT,
  `lb_pendapatan_lain` int(11) NOT NULL,
  `lb_pengeluaran_gaji` int(11) NOT NULL,
  `lb_pengeluaran_listrik` int(11) NOT NULL,
  `lb_pengeluaran_tlpn_internet` int(11) NOT NULL,
  `lb_pengeluaran_perlengkapan_toko` int(11) NOT NULL,
  `lb_pengeluaran_biaya_penyusutan` int(11) NOT NULL,
  `lb_pengeluaran_bensin` int(11) NOT NULL,
  `lb_pengeluaran_tak_terduga` int(11) NOT NULL,
  `lb_pengeluaran_lain` int(11) NOT NULL,
  `lb_cabang` int(11) NOT NULL,
  PRIMARY KEY (`lb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO laba_bersih VALUES("1","4000000","2000000","200000","140000","100000","0","0","10000","50000","0");
INSERT INTO laba_bersih VALUES("2","0","0","0","0","0","0","0","0","0","1");



CREATE TABLE `laporan_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_id` int(11) NOT NULL,
  `sales_nama` varchar(100) NOT NULL,
  `sales_hp` varchar(20) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `jumlah_transaksi` int(11) DEFAULT 0,
  `total_penjualan` decimal(15,2) DEFAULT 0.00,
  `target_penjualan` decimal(15,2) DEFAULT 0.00,
  `persentase_pencapaian` decimal(10,2) DEFAULT 0.00,
  `komisi` decimal(15,2) DEFAULT 0.00,
  `tanggal_laporan` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_id` (`sales_id`),
  KEY `toko_id` (`toko_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO laporan_sales VALUES("1","1","tes","tes","1","0","0.00","0.00","0.00","0.00","2025-03-18");



CREATE TABLE `log_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `keterangan` text NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_barang_id` (`barang_id`),
  KEY `idx_jenis` (`jenis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `mutasi_stock` (
  `mutasi_id` int(11) NOT NULL AUTO_INCREMENT,
  `mutasi_barang_id` int(11) NOT NULL,
  `mutasi_barang_kode` varchar(100) NOT NULL,
  `mutasi_barang_nama` varchar(255) NOT NULL,
  `mutasi_kategori` varchar(100) NOT NULL,
  `mutasi_qty_awal` int(11) NOT NULL,
  `mutasi_qty_adjust` int(11) NOT NULL,
  `mutasi_qty_akhir` int(11) NOT NULL,
  `mutasi_keterangan` text DEFAULT NULL,
  `mutasi_tanggal` datetime NOT NULL,
  `mutasi_user` varchar(100) NOT NULL,
  `mutasi_cabang` int(11) NOT NULL,
  PRIMARY KEY (`mutasi_id`),
  KEY `mutasi_barang_id` (`mutasi_barang_id`),
  KEY `mutasi_cabang` (`mutasi_cabang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `nota_setting` (
  `nota_id` int(11) NOT NULL AUTO_INCREMENT,
  `nota_toko_id` int(11) NOT NULL,
  `nota_lebar` float NOT NULL DEFAULT 8,
  `nota_font_size` int(11) NOT NULL DEFAULT 9,
  `nota_header_height` int(11) NOT NULL DEFAULT 20,
  `nota_margin` int(11) NOT NULL DEFAULT 5,
  `nota_show_logo` tinyint(1) NOT NULL DEFAULT 1,
  `nota_show_alamat` tinyint(1) NOT NULL DEFAULT 1,
  `nota_show_telp` tinyint(1) NOT NULL DEFAULT 1,
  `nota_show_email` tinyint(1) NOT NULL DEFAULT 1,
  `nota_footer_text` text DEFAULT 'Terima kasih atas kunjungan Anda',
  `nota_created` datetime DEFAULT NULL,
  `nota_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`nota_id`),
  KEY `nota_toko_id` (`nota_toko_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO nota_setting VALUES("1","2","8","9","20","5","1","1","1","1","Terima kasih atas kunjungan Anda","2025-03-18 13:02:00","2025-03-20 13:44:19");



CREATE TABLE `pasien` (
  `pasien_id` int(11) NOT NULL AUTO_INCREMENT,
  `pasien_kode` varchar(20) NOT NULL,
  `pasien_nama` varchar(100) NOT NULL,
  `pasien_alamat` text NOT NULL,
  `pasien_hp` varchar(20) NOT NULL,
  `pasien_email` varchar(100) DEFAULT NULL,
  `pasien_kota` varchar(100) DEFAULT NULL,
  `pasien_kodepos` varchar(10) DEFAULT NULL,
  `pasien_status` enum('0','1') NOT NULL DEFAULT '1',
  `pasien_cabang` int(11) NOT NULL,
  `pasien_created` date NOT NULL,
  PRIMARY KEY (`pasien_id`),
  UNIQUE KEY `pasien_kode` (`pasien_kode`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pasien VALUES("1","PSN-000001","rizky","pekalongan","086242242","rizky@gmail.com","pekalongan","23232","1","0","2025-03-17");
INSERT INTO pasien VALUES("2","PSN-000002","ahmad","lamongan","08122646742","ahmad@gmail.com","lamongan","2022","1","0","2025-03-20");



CREATE TABLE `pembelian` (
  `pembelian_id` int(11) NOT NULL AUTO_INCREMENT,
  `pembelian_barang_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `barang_qty` int(11) NOT NULL,
  `keranjang_id_kasir` int(11) NOT NULL,
  `pembelian_invoice` text NOT NULL,
  `pembelian_invoice_parent` text NOT NULL,
  `pembelian_date` date NOT NULL,
  `barang_qty_lama` varchar(500) NOT NULL,
  `barang_qty_lama_parent` varchar(500) NOT NULL,
  `barang_harga_beli` int(11) NOT NULL,
  `pembelian_cabang` int(11) NOT NULL,
  PRIMARY KEY (`pembelian_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO pembelian VALUES("5","77","77","5","3","28282828","20250315130","2025-03-15","5","5","50000","0");
INSERT INTO pembelian VALUES("6","75","75","11","3","55576","20250315230","2025-03-15","11","11","7000","0");



CREATE TABLE `penerimaan_konsinyasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_nama` varchar(255) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `tanggal_penitipan` date NOT NULL,
  `tanggal_penarikan` date DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_id` (`barang_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO penerimaan_konsinyasi VALUES("1","tes","3","2025-03-20","2025-03-21","1");



CREATE TABLE `penjualan` (
  `penjualan_id` int(11) NOT NULL AUTO_INCREMENT,
  `penjualan_barang_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `barang_qty` int(11) NOT NULL,
  `barang_qty_keranjang` int(11) NOT NULL,
  `barang_qty_konversi_isi` int(11) NOT NULL,
  `keranjang_satuan` int(11) NOT NULL,
  `keranjang_harga_beli` varchar(500) NOT NULL,
  `keranjang_harga` varchar(500) NOT NULL,
  `keranjang_harga_parent` int(11) NOT NULL,
  `keranjang_harga_edit` int(11) NOT NULL,
  `keranjang_id_kasir` int(11) NOT NULL,
  `penjualan_invoice` text NOT NULL,
  `penjualan_date` date NOT NULL,
  `penjualan_date_year_month` varchar(250) NOT NULL,
  `barang_qty_lama` varchar(500) NOT NULL,
  `barang_qty_lama_parent` varchar(500) NOT NULL,
  `barang_option_sn` int(11) NOT NULL,
  `barang_sn_id` int(11) NOT NULL,
  `barang_sn_desc` text NOT NULL,
  `invoice_customer_category` int(11) NOT NULL,
  `penjualan_cabang` int(11) NOT NULL,
  `penjualan_sales_id` int(11) DEFAULT 0,
  PRIMARY KEY (`penjualan_id`),
  KEY `idx_penjualan_sales_id` (`penjualan_sales_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO penjualan VALUES("6","77","77","1","1","1","3","5000","25000","25000","0","3","202502051","2025-02-05","2025-02","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("7","76","76","1","1","1","2","5000","12000","12000","0","3","202502052","2025-02-05","2025-02","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("8","77","77","1","1","1","3","5000","25000","25000","0","15","202502073","2025-02-07","2025-02","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("9","77","77","1","1","1","3","5000","25000","25000","0","3","202502214","2025-02-21","2025-02","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("10","77","77","1","1","1","3","5000","25000","25000","0","3","202502215","2025-02-21","2025-02","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("11","77","77","1","1","1","3","5000","25000","25000","0","3","202503156","2025-03-15","2025-03","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("12","77","77","1","1","1","3","5000","25000","25000","0","3","202503157","2025-03-15","2025-03","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("15","77","77","3","3","1","3","5000","25000","25000","0","3","202503159","2025-03-15","2025-03","3","3","0","0","0","0","0","");
INSERT INTO penjualan VALUES("16","77","77","4","4","1","3","5000","25000","25000","0","3","2025031510","2025-03-15","2025-03","4","4","0","0","0","0","0","");
INSERT INTO penjualan VALUES("17","75","75","3","3","1","2","7000","50000","50000","0","3","2025031511","2025-03-15","2025-03","3","3","0","0","0","0","0","");
INSERT INTO penjualan VALUES("18","76","76","1","1","1","2","5000","12000","12000","0","3","2025031512","2025-03-15","2025-03","1","1","0","0","0","0","0","");
INSERT INTO penjualan VALUES("19","75","75","1","1","1","2","7000","50000","50000","0","3","2025031512","2025-03-15","2025-03","1","1","0","0","0","0","0","");



CREATE TABLE `penjualan_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penjualan_invoice` varchar(50) NOT NULL,
  `penjualan_date` datetime NOT NULL,
  `penjualan_pelanggan` int(11) NOT NULL,
  `penjualan_sales_id` int(11) DEFAULT NULL,
  `penjualan_tipe_pembayaran` varchar(20) NOT NULL,
  `penjualan_keterangan` text DEFAULT NULL,
  `penjualan_total` decimal(15,2) NOT NULL,
  `penjualan_bayar` decimal(15,2) NOT NULL,
  `penjualan_kembali` decimal(15,2) NOT NULL,
  `penjualan_status` tinyint(1) NOT NULL DEFAULT 1,
  `penjualan_status_lunas` tinyint(1) NOT NULL DEFAULT 1,
  `penjualan_cabang` int(11) NOT NULL,
  `penjualan_user` int(11) NOT NULL,
  `penjualan_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO penjualan_sales VALUES("1","202503180001","2025-03-18 00:00:00","20","1","cash","","50000.00","50000.00","0.00","1","1","0","0","2025-03-18 20:45:49");
INSERT INTO penjualan_sales VALUES("2","202503180001","2025-03-18 00:00:00","19","1","cash","","50000.00","200000.00","150000.00","1","1","0","0","2025-03-18 20:48:27");
INSERT INTO penjualan_sales VALUES("3","202503190001","2025-03-19 00:00:00","19","2","cash","testing","12000.00","50000.00","38000.00","1","1","0","0","2025-03-19 13:22:36");
INSERT INTO penjualan_sales VALUES("4","202503190001","2025-03-19 00:00:00","19","1","cash","hao","7000.00","8000.00","1000.00","1","1","0","0","2025-03-19 16:11:24");
INSERT INTO penjualan_sales VALUES("5","202503200001","2025-03-20 00:00:00","19","1","cash","","500000.00","500000.00","0.00","1","1","0","0","2025-03-20 14:58:19");
INSERT INTO penjualan_sales VALUES("6","202503210001","2025-03-21 00:00:00","19","1","cash","","7000.00","7000.00","0.00","1","1","0","0","2025-03-21 07:49:00");



CREATE TABLE `penutupan_shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(100) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `tanggal` date NOT NULL DEFAULT curdate(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO penutupan_shift VALUES("2","ibnu","07:00:00","16:00:00","2025-03-19");
INSERT INTO penutupan_shift VALUES("3","roni","08:30:00","19:00:00","2025-03-19");



CREATE TABLE `perpindahan_barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_referensi` varchar(20) NOT NULL,
  `tanggal` datetime NOT NULL,
  `barang_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `cabang_asal` int(11) NOT NULL,
  `cabang_tujuan` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_no_referensi` (`no_referensi`),
  KEY `idx_barang_id` (`barang_id`),
  KEY `idx_cabang_asal` (`cabang_asal`),
  KEY `idx_cabang_tujuan` (`cabang_tujuan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `piutang` (
  `piutang_id` int(11) NOT NULL AUTO_INCREMENT,
  `piutang_invoice` text NOT NULL,
  `piutang_date` varchar(500) NOT NULL,
  `piutang_date_time` varchar(500) NOT NULL,
  `piutang_kasir` int(11) NOT NULL,
  `piutang_nominal` varchar(500) NOT NULL,
  `piutang_tipe_pembayaran` int(11) NOT NULL,
  `piutang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`piutang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO piutang VALUES("1","202503159","2025-03-15","15 March 2025 10:01:55 am","3","60000","0","0");



CREATE TABLE `piutang_awal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_ref` varchar(50) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `nominal_piutang` decimal(15,2) NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas',
  `cabang` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_no_ref` (`no_ref`),
  KEY `idx_customer` (`nama_customer`),
  KEY `idx_status` (`status`),
  KEY `idx_cabang` (`cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO piutang_awal VALUES("1","121","dapin","2025-03-18","2000000.00","2025-03-31","bayar","lunas","0","2025-03-18 21:20:33","2025-03-18 21:30:59");



CREATE TABLE `piutang_kembalian` (
  `pl_id` int(11) NOT NULL AUTO_INCREMENT,
  `pl_invoice` text NOT NULL,
  `pl_date` varchar(500) NOT NULL,
  `pl_date_time` varchar(500) NOT NULL,
  `pl_nominal` varchar(250) NOT NULL,
  `pl_cabang` int(11) NOT NULL,
  PRIMARY KEY (`pl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO piutang_kembalian VALUES("5","202503159","2025-03-15","15 March 2025 10:01:55 am","0","0");



CREATE TABLE `rak` (
  `rak_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_rak` varchar(100) NOT NULL,
  `cabang` int(11) NOT NULL,
  `toko_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`rak_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO rak VALUES("2","rak 5","0","","2025-03-17 09:16:30","");
INSERT INTO rak VALUES("5","rak 2","0","1","2025-03-19 15:27:35","");
INSERT INTO rak VALUES("6","rak 3","5","6","2025-03-20 15:01:15","");



CREATE TABLE `retur` (
  `retur_id` int(11) NOT NULL AUTO_INCREMENT,
  `retur_barang_id` varchar(500) NOT NULL,
  `retur_invoice` varchar(500) NOT NULL,
  `retur_admin_id` varchar(500) NOT NULL,
  `retur_date` date NOT NULL,
  `retur_alasan` text NOT NULL,
  `barang_stock` int(11) NOT NULL,
  PRIMARY KEY (`retur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO retur VALUES("1","1","a","1","2025-03-17","aaaa","1");



CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_nama` varchar(255) NOT NULL,
  `sales_hp` varchar(50) DEFAULT NULL,
  `sales_email` varchar(255) DEFAULT NULL,
  `sales_alamat` text DEFAULT NULL,
  `sales_status` tinyint(1) DEFAULT 1 COMMENT '1=Aktif, 0=Non-Aktif',
  `sales_cabang` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sales_id`),
  KEY `idx_cabang_status` (`sales_cabang`,`sales_status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO sales VALUES("1","adit","085703247167","hai@gmail.com","test","1","0","2025-03-18 12:57:08","2025-03-19 13:20:38");
INSERT INTO sales VALUES("2","doni","08456743456","doni@gmail.com","temanggung","1","0","2025-03-19 13:21:10","");



CREATE TABLE `satuan` (
  `satuan_id` int(11) NOT NULL AUTO_INCREMENT,
  `satuan_nama` varchar(500) NOT NULL,
  `satuan_status` varchar(250) NOT NULL,
  `satuan_cabang` int(11) NOT NULL,
  PRIMARY KEY (`satuan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO satuan VALUES("2","PCS","1","0");
INSERT INTO satuan VALUES("3","STRIP","1","0");
INSERT INTO satuan VALUES("4","BOTOL","1","0");
INSERT INTO satuan VALUES("5","BOX","0","0");
INSERT INTO satuan VALUES("6","paratusin","1","0");



CREATE TABLE `settings_akuntansi` (
  `sa_id` int(11) NOT NULL AUTO_INCREMENT,
  `sa_periode_bulan` varchar(2) NOT NULL,
  `sa_periode_tahun` varchar(4) NOT NULL,
  `sa_tarif_pajak` decimal(5,2) NOT NULL,
  `sa_cabang` int(11) NOT NULL,
  PRIMARY KEY (`sa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO settings_akuntansi VALUES("1","03","2025","10.00","0");



CREATE TABLE `stock_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_id` int(11) NOT NULL,
  `barang_cabang` int(11) NOT NULL,
  `stock_sebelum` int(11) NOT NULL,
  `stock_sesudah` int(11) NOT NULL,
  `keterangan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cabang_tujuan` int(11) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `stock_log_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`barang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO stock_log VALUES("19","87","0","1","1","ss","2025-03-19 09:59:01","");



CREATE TABLE `stock_opname` (
  `stock_opname_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_opname_date_create` varchar(250) NOT NULL,
  `stock_opname_datetime_create` varchar(250) NOT NULL,
  `stock_opname_date_proses` varchar(250) NOT NULL,
  `stock_opname_user_create` int(11) NOT NULL,
  `stock_opname_user_eksekusi` int(11) NOT NULL,
  `stock_opname_status` int(11) NOT NULL,
  `stock_opname_user_upload` int(11) NOT NULL,
  `stock_opname_date_upload` varchar(250) NOT NULL,
  `stock_opname_datetime_upload` varchar(250) NOT NULL,
  `stock_opname_tipe` int(11) NOT NULL,
  `stock_opname_cabang` int(11) NOT NULL,
  PRIMARY KEY (`stock_opname_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO stock_opname VALUES("17","2025-02-05","05 February 2025 5:59:54 pm","2025-02-05","3","3","0","0","","","0","0");
INSERT INTO stock_opname VALUES("18","2025-02-07","07 February 2025 1:55:31 pm","2025-02-07","3","3","0","0","","","0","0");
INSERT INTO stock_opname VALUES("19","2025-02-07","07 February 2025 2:36:07 pm","2025-02-07","3","3","0","0","","","0","0");
INSERT INTO stock_opname VALUES("21","2025-03-11","11 March 2025 10:22:33 am","2025-03-11","3","3","1","3","2025-03-13","13 March 2025 11:47:01 am","0","0");
INSERT INTO stock_opname VALUES("22","2025-03-14","14 March 2025 4:55:40 pm","2025-03-14","3","3","0","0","","","0","0");
INSERT INTO stock_opname VALUES("24","2025-03-20","20 March 2025 9:33:20 am","2025-03-20","3","3","0","0","","","0","0");
INSERT INTO stock_opname VALUES("25","2025-03-20","20 March 2025 10:03:08 am","2025-03-20","3","3","0","0","","","0","0");



CREATE TABLE `stock_opname_hasil` (
  `soh_id` int(11) NOT NULL AUTO_INCREMENT,
  `soh_stock_opname_id` int(11) NOT NULL,
  `soh_barang_id` int(11) NOT NULL,
  `soh_barang_kode` varchar(500) NOT NULL,
  `soh_barang_stock_system` int(11) NOT NULL,
  `soh_stock_fisik` int(11) NOT NULL,
  `soh_selisih` int(11) NOT NULL,
  `soh_note` text NOT NULL,
  `soh_date` varchar(250) NOT NULL,
  `soh_datetime` varchar(250) NOT NULL,
  `soh_tipe` int(11) NOT NULL,
  `soh_user` int(11) NOT NULL,
  `soh_barang_cabang` int(11) NOT NULL,
  PRIMARY KEY (`soh_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `stok_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `history_barang_id` int(11) NOT NULL,
  `history_date` date NOT NULL,
  `history_jumlah` int(11) NOT NULL,
  `stok_tipe` enum('masuk','keluar','transfer','penyesuaian') NOT NULL,
  `history_cabang` int(11) NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `stok_kartu` (
  `stok_kartu_id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_nama` varchar(255) NOT NULL,
  `stok_kartu_tanggal` date NOT NULL,
  `stok_kartu_stok_awal` int(11) NOT NULL,
  `stok_kartu_masuk` int(11) NOT NULL,
  `stok_kartu_keluar` int(11) NOT NULL,
  `stok_kartu_sisa` int(11) NOT NULL,
  `stok_kartu_keterangan` text DEFAULT NULL,
  `stok_kartu_cabang` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`stok_kartu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_kode` varchar(50) NOT NULL,
  `supplier_nama` varchar(100) NOT NULL,
  `supplier_wa` varchar(15) DEFAULT NULL,
  `supplier_email` varchar(100) DEFAULT NULL,
  `supplier_alamat` text DEFAULT NULL,
  `supplier_company` varchar(100) DEFAULT NULL,
  `supplier_kota` varchar(100) DEFAULT NULL,
  `supplier_kodepos` varchar(10) DEFAULT NULL,
  `supplier_status` enum('0','1') DEFAULT '1',
  `supplier_cabang` int(11) NOT NULL,
  PRIMARY KEY (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO supplier VALUES("1","1","sww","4554","swsw@gmail.com","daddada","dadasadda","dadadad","23232","1","0");



CREATE TABLE `terlaris` (
  `terlaris_id` int(11) NOT NULL AUTO_INCREMENT,
  `barang_id` int(11) NOT NULL,
  `barang_terjual` int(11) NOT NULL,
  PRIMARY KEY (`terlaris_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO terlaris VALUES("1","77","1");
INSERT INTO terlaris VALUES("2","76","1");
INSERT INTO terlaris VALUES("3","77","1");
INSERT INTO terlaris VALUES("4","77","1");
INSERT INTO terlaris VALUES("5","77","1");
INSERT INTO terlaris VALUES("6","77","1");
INSERT INTO terlaris VALUES("7","77","1");
INSERT INTO terlaris VALUES("8","77","10");
INSERT INTO terlaris VALUES("9","76","7");
INSERT INTO terlaris VALUES("10","77","3");
INSERT INTO terlaris VALUES("11","77","4");
INSERT INTO terlaris VALUES("12","75","3");
INSERT INTO terlaris VALUES("13","76","1");
INSERT INTO terlaris VALUES("14","75","1");



CREATE TABLE `toko` (
  `toko_id` int(11) NOT NULL AUTO_INCREMENT,
  `toko_nama` varchar(500) NOT NULL,
  `toko_kota` varchar(250) NOT NULL,
  `toko_alamat` text NOT NULL,
  `toko_tlpn` varchar(250) NOT NULL,
  `toko_wa` varchar(250) NOT NULL,
  `toko_email` varchar(500) NOT NULL,
  `toko_print` int(11) NOT NULL,
  `toko_status` int(11) NOT NULL,
  `toko_ongkir` int(11) NOT NULL,
  `toko_cabang` int(11) NOT NULL,
  PRIMARY KEY (`toko_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO toko VALUES("1","Apotek Widigdo 1","Klaten","085842199807","031890876","085842199807","apt@gmail.com","8","1","0","0");
INSERT INTO toko VALUES("2","Apotek Widigdo 2","Sleman","Sleman","085842199807","085842199807","apt2@gmail.com","10","1","0","1");
INSERT INTO toko VALUES("3","Apotek Widigdo 3","Sleman","Jangkang, Widodomartani, Kec. Ngemplak, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55584","085842199807","085842199807","apt3@gmail.com","10","1","0","2");
INSERT INTO toko VALUES("4","Apotek Widigdo 4","Sleman","Mriyan, Margomulyo, Seyegan, Sleman Regency, Special Region of Yogyakarta 55561","085842199807","085842199807","apt4@gmail.com","10","1","0","3");
INSERT INTO toko VALUES("5","Apotek Widigdo 5","Sleman","Blembem Kidul, Harjobinangun, Pakem, Sleman Regency, Special Region of Yogyakarta 55582","085842199807","085842199807","apt4@gmail.com","10","1","0","4");
INSERT INTO toko VALUES("6","Kantor Widigdo Grup","Sleman","Kenteng, Wono Kerto, Kec. Turi, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55551","085842199807","085842199807","apt6@gmail.com","10","1","0","5");



CREATE TABLE `transfer` (
  `transfer_id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_ref` text NOT NULL,
  `transfer_count` int(11) NOT NULL,
  `transfer_date` varchar(250) NOT NULL,
  `transfer_date_time` varchar(250) NOT NULL,
  `transfer_terima_date` varchar(250) NOT NULL,
  `transfer_terima_date_time` varchar(250) NOT NULL,
  `transfer_note` text NOT NULL,
  `transfer_pengirim_cabang` int(11) NOT NULL,
  `transfer_penerima_cabang` int(11) NOT NULL,
  `transfer_id_tipe_keluar` int(11) NOT NULL,
  `transfer_id_tipe_masuk` int(11) NOT NULL,
  `transfer_status` int(11) NOT NULL,
  `transfer_user` int(11) NOT NULL,
  `transfer_user_penerima` int(11) NOT NULL,
  `transfer_cabang` int(11) NOT NULL,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO transfer VALUES("2","2025020711","1","2025-02-07","07 February 2025 2:11:54 pm","","","","1","4","0","4","1","3","0","0");



CREATE TABLE `transfer_produk_keluar` (
  `tpk_id` int(11) NOT NULL AUTO_INCREMENT,
  `tpk_transfer_barang_id` int(11) NOT NULL,
  `tpk_barang_id` int(11) NOT NULL,
  `tpk_kode_slug` varchar(500) NOT NULL,
  `tpk_qty` int(11) NOT NULL,
  `tpk_ref` text NOT NULL,
  `tpk_date` varchar(11) NOT NULL,
  `tpk_date_time` varchar(500) NOT NULL,
  `tpk_barang_option_sn` int(11) NOT NULL,
  `tpk_barang_sn_id` int(11) NOT NULL,
  `tpk_barang_sn_desc` varchar(500) NOT NULL,
  `tpk_user` int(11) NOT NULL,
  `tpk_pengirim_cabang` int(11) NOT NULL,
  `tpk_penerima_cabang` int(11) NOT NULL,
  `tpk_cabang` int(11) NOT NULL,
  PRIMARY KEY (`tpk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;




CREATE TABLE `transfer_produk_masuk` (
  `tpm_id` int(11) NOT NULL AUTO_INCREMENT,
  `tpm_kode_slug` text NOT NULL,
  `tpm_qty` int(11) NOT NULL,
  `tpm_ref` text NOT NULL,
  `tpm_date` varchar(250) NOT NULL,
  `tpm_date_time` varchar(250) NOT NULL,
  `tpm_barang_option_sn` int(11) NOT NULL,
  `tpm_barang_sn_id` int(11) NOT NULL,
  `tpm_barang_sn_desc` varchar(500) NOT NULL,
  `tpm_user` int(11) NOT NULL,
  `tpm_pengirim_cabang` int(11) NOT NULL,
  `tpm_penerima_cabang` int(11) NOT NULL,
  `tpm_cabang` int(11) NOT NULL,
  PRIMARY KEY (`tpm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO transfer_produk_masuk VALUES("1","1","1","1","18/03/2025","20","1","1","1","1","1","1","1");



CREATE TABLE `transfer_select_cabang` (
  `tsc_id` int(11) NOT NULL AUTO_INCREMENT,
  `tsc_cabang_pusat` int(11) NOT NULL,
  `tsc_cabang_penerima` int(11) NOT NULL,
  `tsc_user_id` int(11) NOT NULL,
  `tsc_cabang` int(11) NOT NULL,
  PRIMARY KEY (`tsc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO transfer_select_cabang VALUES("6","0","1","3","0");



CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_nama` varchar(500) NOT NULL,
  `user_no_hp` varchar(250) NOT NULL,
  `user_alamat` text NOT NULL,
  `user_email` varchar(250) NOT NULL,
  `user_password` varchar(500) NOT NULL,
  `user_create` varchar(250) NOT NULL,
  `user_level` varchar(250) NOT NULL,
  `user_status` varchar(250) NOT NULL,
  `user_cabang` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO user VALUES("3","ibnu","08584219807","Surabaya","itsolutionjogja@gmail.com","fb2cf4bbf0295eff4122e63be313f5f9","30 March 2020 9:17:00 pm","super admin","1","0");
INSERT INTO user VALUES("5","Doni Asrul Afandi","08584219807","Surabaya","doniasrulafandi@gmail.com","455741f419c80249f1a9015d678ef1d1","08 April 2020 3:40:08 pm","admin","1","0");
INSERT INTO user VALUES("8","Doni Afandi","08584219807","Surabaya","admin@widigdo.com","5c032178dc4710456f16d99488a6d1eb","16 April 2020 9:32:06 pm","admin","1","0");
INSERT INTO user VALUES("14","Pak Sucripto","08584219807","Surabaya Jawa Timur Indonesia","kurir1@widigdo.com","0a78eecfc1e1f9dee975b5b494c71901","21 August 2021 10:38:10 am","kurir","1","0");
INSERT INTO user VALUES("15","Kasir","08584219807","Surabaya","kasir@widigdo.com","9d0572aaba10191fdcc70c600bd62e3e","04 September 2021 1:31:34 pm","kasir","1","0");



CREATE ALGORITHM=UNDEFINED DEFINER=`bpkbautodigital`@`localhost` SQL SECURITY DEFINER VIEW `view_laporan_sales` AS select `l`.`id` AS `laporan_id`,`l`.`sales_id` AS `sales_id`,`l`.`sales_nama` AS `sales_nama`,`l`.`sales_hp` AS `sales_hp`,`l`.`toko_id` AS `toko_id`,month(`l`.`tanggal_laporan`) AS `periode_bulan`,year(`l`.`tanggal_laporan`) AS `periode_tahun`,`l`.`jumlah_transaksi` AS `jumlah_transaksi`,`l`.`total_penjualan` AS `total_penjualan`,`l`.`target_penjualan` AS `target_penjualan`,`l`.`persentase_pencapaian` AS `persentase_pencapaian`,`l`.`komisi` AS `komisi` from `laporan_sales` `l`;

INSERT INTO view_laporan_sales VALUES("1","1","tes","tes","1","3","2025","0","0.00","0.00","0.00","0.00");

