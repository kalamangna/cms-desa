<?php

$families = \App\Models\Family::all();
$updated = 0;

foreach($families as $f) {
    $changed = false;
    
    // Bukti Kepemilikan
    if ($f->ownership_proof === 'SHM') { $f->ownership_proof = 'SHM (Sertifikat Hak Milik)'; $changed = true; }
    
    // Jenis Kloset
    if ($f->closet_type === 'Tidak Ada') { $f->closet_type = 'Tidak Pakai'; $changed = true; }
    if ($f->closet_type === 'Plengsengan dengan Tutup') { $f->closet_type = 'Plengsengan'; $changed = true; }

    // Sumber Penerangan Utama
    if ($f->lighting_source === 'Listrik PLN Dengan Meteran') { $f->lighting_source = 'Listrik PLN dengan meteran'; $changed = true; }
    if ($f->lighting_source === 'Listrik PLN Tanpa Meteran') { $f->lighting_source = 'Listrik PLN tanpa meteran'; $changed = true; }
    if ($f->lighting_source === 'Listrik Non-PLN') { $f->lighting_source = 'Listrik non-PLN'; $changed = true; }
    if ($f->lighting_source === 'Bukan Listrik') { $f->lighting_source = 'Bukan listrik'; $changed = true; }

    // Daya Listrik
    if ($f->electricity_power_meter_1 && str_contains($f->electricity_power_meter_1, 'Watt')) {
        $f->electricity_power_meter_1 = str_replace('Watt', 'VA', $f->electricity_power_meter_1);
        $changed = true;
    }
    if ($f->electricity_power_meter_2 && str_contains($f->electricity_power_meter_2, 'Watt')) {
        $f->electricity_power_meter_2 = str_replace('Watt', 'VA', $f->electricity_power_meter_2);
        $changed = true;
    }
    if ($f->electricity_power_meter_3 && str_contains($f->electricity_power_meter_3, 'Watt')) {
        $f->electricity_power_meter_3 = str_replace('Watt', 'VA', $f->electricity_power_meter_3);
        $changed = true;
    }
    
    if ($changed) {
        $f->save();
        $updated++;
    }
}

echo "Updated $updated families.\n";
