CREATE TABLE `queue_antrian_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tanggal` DATE NOT NULL,
  `no_antrian` VARCHAR(10) NOT NULL,
  `nama_customer` VARCHAR(100) DEFAULT NULL,
  `nama_driver` VARCHAR(100) DEFAULT NULL,
  `plat_nomor` VARCHAR(20) DEFAULT NULL,
  `id_loket` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;