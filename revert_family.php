<?php

$families = \App\Models\Family::all();
$updated = 0;

foreach($families as $f) {
    $changed = false;
    
    // Bukti Kepemilikan
    if ($f->ownership_proof === 'SHM (Sertifikat Hak Milik)') { $f->ownership_proof = 'SHM'; $changed = true; }
    
    // Jenis Kloset
    if ($f->closet_type === 'Tidak Pakai') { $f->closet_type = 'Tidak Ada'; $changed = true; }
    if ($f->closet_type === 'Plengsengan') { $f->closet_type = 'Plengsengan dengan Tutup'; $changed = true; }

    // Sumber Penerangan Utama
    if ($f->lighting_source === 'Listrik PLN dengan meteran') { $f->lighting_source = 'Listrik PLN Dengan Meteran'; $changed = true; }
    if ($f->lighting_source === 'Listrik PLN tanpa meteran') { $f->lighting_source = 'Listrik PLN Tanpa Meteran'; $changed = true; }
    if ($f->lighting_source === 'Listrik non-PLN') { $f->lighting_source = 'Listrik Non-PLN'; $changed = true; }
    if ($f->lighting_source === 'Bukan listrik') { $f->lighting_source = 'Bukan Listrik'; $changed = true; }

    // Daya Listrik
    if ($f->electricity_power_meter_1 && str_contains($f->electricity_power_meter_1, 'VA')) {
        $f->electricity_power_meter_1 = str_replace('VA', 'Watt', $f->electricity_power_meter_1);
        $changed = true;
    }
    if ($f->electricity_power_meter_2 && str_contains($f->electricity_power_meter_2, 'VA')) {
        $f->electricity_power_meter_2 = str_replace('VA', 'Watt', $f->electricity_power_meter_2);
        $changed = true;
    }
    if ($f->electricity_power_meter_3 && str_contains($f->electricity_power_meter_3, 'VA')) {
        $f->electricity_power_meter_3 = str_replace('VA', 'Watt', $f->electricity_power_meter_3);
        $changed = true;
    }
    
    if ($changed) {
        $f->save();
        $updated++;
    }
}

echo "Reverted $updated families.\n";
