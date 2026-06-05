<?php

namespace Tests\Feature;

use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class APBDesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_budget_category(): void
    {
        BudgetCategory::create(['name' => 'Pendapatan', 'slug' => 'pendapatan']);
        $this->assertDatabaseHas('budget_categories', ['name' => 'Pendapatan']);
    }

    public function test_can_create_budget_realization(): void
    {
        $category = BudgetCategory::create(['name' => 'Pendapatan', 'slug' => 'pendapatan']);
        
        $realization = BudgetRealization::create([
            'budget_category_id' => $category->id,
            'title' => 'Dana Desa',
            'year' => 2024,
            'budget_amount' => 1000000000,
            'realization_amount' => 500000000,
        ]);

        $this->assertDatabaseHas('budget_realizations', [
            'title' => 'Dana Desa',
            'budget_amount' => 1000000000,
        ]);
        
        $this->assertEquals(50, $realization->percentage);
    }
}
