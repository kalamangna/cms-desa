<?php

namespace Tests\Feature;

use App\Filament\Resources\Citizens\Pages\ListCitizens;
use App\Filament\Resources\Families\Pages\ListFamilies;
use App\Models\Citizen;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamilyCitizenNullHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_family_model_cleans_dash_and_empty_values_to_null_on_save(): void
    {
        $family = Family::create([
            'kk_number' => '7306010101010001',
            'head_name' => 'Budi Santoso',
            'building_type' => '-',
            'ownership_status' => '--',
            'ownership_proof' => '---',
            'floor_material' => 'none',
            'wall_material' => '/',
            'roof_material' => 'tidak ada',
            'floor_condition' => '-',
            'wall_condition' => '-',
            'roof_condition' => '-',
            'toilet_facility' => '-',
            'closet_type' => '-',
            'feces_disposal' => '-',
            'water_source' => '-',
            'lighting_source' => '-',
            'electricity_power' => '-',
            'electricity_power_meter_1' => '-',
            'electricity_power_meter_2' => '-',
            'electricity_power_meter_3' => '-',
            'assistance_type' => 'Tidak Ada',
            'floor_area' => null,
            'rental_estimate' => null,
        ]);

        $family->refresh();

        $this->assertNull($family->building_type);
        $this->assertNull($family->ownership_status);
        $this->assertNull($family->ownership_proof);
        $this->assertNull($family->floor_material);
        $this->assertNull($family->wall_material);
        $this->assertNull($family->roof_material);
        $this->assertNull($family->floor_condition);
        $this->assertNull($family->wall_condition);
        $this->assertNull($family->roof_condition);
        $this->assertNull($family->toilet_facility);
        $this->assertNull($family->closet_type);
        $this->assertNull($family->feces_disposal);
        $this->assertNull($family->water_source);
        $this->assertNull($family->lighting_source);
        $this->assertNull($family->electricity_power);
        $this->assertNull($family->electricity_power_meter_1);
        $this->assertNull($family->assistance_type);
    }

    public function test_citizen_model_cleans_dash_and_empty_values_to_null_on_save(): void
    {
        $citizen = Citizen::create([
            'nik' => '7306010101010002',
            'name' => 'Siti Aminah',
            'gender' => '-',
            'religion' => '-',
            'blood_type' => '-',
            'marital_status' => '-',
            'family_relation' => '-',
            'domicile_address_type' => '-',
            'school_participation' => '-',
            'education_level' => '-',
            'education' => '-',
            'bpjs_status' => '-',
            'job' => '-',
            'job_status' => '-',
            'citizenship_status' => '-',
            'has_digital_wallet' => '-',
        ]);

        $citizen->refresh();

        // Check raw DB values and accessors
        $this->assertNull($citizen->getAttributes()['gender']);
        $this->assertNull($citizen->religion);
        $this->assertNull($citizen->blood_type);
        $this->assertNull($citizen->marital_status);
        $this->assertNull($citizen->family_relation);
        $this->assertNull($citizen->domicile_address_type);
        $this->assertNull($citizen->school_participation);
        $this->assertNull($citizen->education_level);
        $this->assertNull($citizen->education);
        $this->assertNull($citizen->bpjs_status);
        $this->assertNull($citizen->job);
        $this->assertNull($citizen->job_status);
        $this->assertNull($citizen->citizenship_status);
        $this->assertNull($citizen->has_digital_wallet);
    }

    public function test_citizen_gender_infers_from_nik_if_not_provided(): void
    {
        // Day > 40 means female (day 52 -> 12th day of month, female)
        $femaleCitizen = Citizen::create([
            'nik' => '7306015203900001',
            'name' => 'Perempuan Test',
            'gender' => null,
        ]);

        $this->assertEquals('Perempuan', $femaleCitizen->gender);

        // Day <= 31 means male (day 15)
        $maleCitizen = Citizen::create([
            'nik' => '7306011503900001',
            'name' => 'Laki Test',
            'gender' => null,
        ]);

        $this->assertEquals('Laki-laki', $maleCitizen->gender);
    }

    public function test_family_import_parser_returns_null_for_empty_and_dash(): void
    {
        $page = new ListFamilies;
        $ref = new \ReflectionClass($page);

        $methods = [
            'parseBuildingType',
            'parseOwnershipStatus',
            'parseOwnershipProof',
            'parseFloorMaterial',
            'parseWallMaterial',
            'parseRoofMaterial',
            'parseCondition',
            'parseToiletFacility',
            'parseClosetType',
            'parseFecesDisposal',
            'parseWaterSource',
            'parseLightingSource',
            'parseElectricityPower',
            'parseAssistanceType',
            'parseFloorArea',
            'cleanNullableNumeric',
        ];

        foreach ($methods as $methodName) {
            $method = $ref->getMethod($methodName);
            $method->setAccessible(true);

            $this->assertNull($method->invoke($page, null), "Failed asserting null for {$methodName}(null)");
            $this->assertNull($method->invoke($page, ''), "Failed asserting null for {$methodName}('')");
            $this->assertNull($method->invoke($page, '-'), "Failed asserting null for {$methodName}('-')");
            $this->assertNull($method->invoke($page, '--'), "Failed asserting null for {$methodName}('--')");
        }
    }

    public function test_citizen_import_parser_returns_null_for_empty_and_dash(): void
    {
        $page = new ListCitizens;
        $ref = new \ReflectionClass($page);

        $methods = [
            'parseGender',
            'parseMaritalStatus',
            'parseFamilyRelation',
            'parseEducationLevel',
            'parseJob',
            'parseSchoolParticipation',
            'parseDomicileAddressType',
            'parseHasDigitalWallet',
            'parseCitizenshipStatus',
            'parseBpjsStatus',
            'parseJobStatus',
        ];

        foreach ($methods as $methodName) {
            $method = $ref->getMethod($methodName);
            $method->setAccessible(true);

            $this->assertNull($method->invoke($page, null), "Failed asserting null for {$methodName}(null)");
            $this->assertNull($method->invoke($page, ''), "Failed asserting null for {$methodName}('')");
            $this->assertNull($method->invoke($page, '-'), "Failed asserting null for {$methodName}('-')");
            $this->assertNull($method->invoke($page, '--'), "Failed asserting null for {$methodName}('--')");
        }
    }
}
