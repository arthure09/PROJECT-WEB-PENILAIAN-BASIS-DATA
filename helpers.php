<?php
function hitungNilaiAkhir(int $total): int { return 90 - $total; }
function tentukanPredikat(int $n): string {
    if ($n >= 86) return "Istimewa";
    if ($n >= 78) return "Sangat Baik";
    if ($n >= 65) return "Baik";
    if ($n >= 50) return "Cukup";
    return "Kurang";
}
function tentukanStatus(int $n): string { return $n >= 65 ? "LANJUT" : "ULANG"; }
?>
