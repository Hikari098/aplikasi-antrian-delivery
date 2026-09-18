-- Hapus data antrian lama yang tidak memiliki jam input atau ganda di history
DELETE FROM queue_antrian_history WHERE jam_input IS NULL OR jam_input = '' OR jam_input = '00:00:00' OR jam_input = '-';