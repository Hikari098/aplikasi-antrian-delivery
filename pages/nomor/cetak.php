<?php
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

function cetak($no_antrian){
    $hariIni = new DateTime();

    try {
        $connector = new WindowsPrintConnector("smb://192.168.1.38/pos-80");
        $printer = new Printer($connector);
        $printer->initialize();
        
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setFont(Printer::FONT_A);
        $printer->setTextSize(2, 1);
        $printer->text("PT PARKER METAL TREATMENT INDONESIA\n");
        $printer->selectPrintMode();
        
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(1, 1);
        $printer->text("Sistem Antrian Kendaraan Delivery\n");
        $printer->text("==================================================\n\n");
        $printer->selectPrintMode();

        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 1);
        $printer->text("NOMOR ANTRIAN ANDA\n\n");
        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(4, 4);
        $printer->text($no_antrian . "\n\n\n");
        $printer->selectPrintMode();

        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(1, 1);
        $printer->text("Silahkan menunggu nomor antrian dipanggil\n");
        $printer->text("Nomor ini hanya berlaku pada hari dicetak\n");
        $printer->text(hariIndo(date('l')) . " " . date('d M Y') . "\n\n");

        $printer->selectPrintMode();
        $printer->setFont(Printer::FONT_B);
        $printer->setTextSize(2, 1);
        $printer->text("TERIMA KASIH, UTAMAKAN KESELAMATAN BERKENDARA\n\n\n\n\n");
        $printer->selectPrintMode();

        $printer->cut();
        $printer->close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
    }
}

function hariIndo ($hariInggris) {
    switch ($hariInggris) {
        case 'Sunday': return 'Minggu';
        case 'Monday': return 'Senin';
        case 'Tuesday': return 'Selasa';
        case 'Wednesday': return 'Rabu';
        case 'Thursday': return 'Kamis';
        case 'Friday': return 'Jumat';
        case 'Saturday': return 'Sabtu';
        default: return 'hari tidak valid';
    }
}
?>