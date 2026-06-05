<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataWilayahTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_district(): void
    {
        $district = District::create([
            'name' => 'Kecamatan Tompobulu',
            'code' => '73.05.01',
        ]);

        $this->assertDatabaseHas('districts', [
            'name' => 'Kecamatan Tompobulu',
            'code' => '73.05.01',
        ]);
    }

    public function test_can_create_village(): void
    {
        $district = District::create([
            'name' => 'Kecamatan Tompobulu',
            'code' => '73.05.01',
        ]);

        $village = Village::create([
            'district_id' => $district->id,
            'name' => 'Desa Tompobulu',
            'code' => '73.05.01.2001',
            'slug' => 'desa-tompobulu',
        ]);

        $this->assertDatabaseHas('villages', [
            'name' => 'Desa Tompobulu',
            'code' => '73.05.01.2001',
            'slug' => 'desa-tompobulu',
            'district_id' => $district->id,
        ]);
    }

    public function test_village_belongs_to_district(): void
    {
        $district = District::create([
            'name' => 'Kecamatan Tompobulu',
            'code' => '73.05.01',
        ]);

        $village = Village::create([
            'district_id' => $district->id,
            'name' => 'Desa Tompobulu',
            'code' => '73.05.01.2001',
            'slug' => 'desa-tompobulu',
        ]);

        $this->assertInstanceOf(District::class, $village->district);
        $this->assertEquals($district->id, $village->district->id);
    }

    public function test_district_has_many_villages(): void
    {
        $district = District::create([
            'name' => 'Kecamatan Tompobulu',
            'code' => '73.05.01',
        ]);

        Village::create([
            'district_id' => $district->id,
            'name' => 'Desa Tompobulu',
            'code' => '73.05.01.2001',
            'slug' => 'desa-tompobulu',
        ]);

        $this->assertCount(1, $district->villages);
    }
}
