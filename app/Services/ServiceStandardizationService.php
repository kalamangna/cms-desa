<?php

namespace App\Services;

use App\Models\Service;
use App\Support\LetterTemplatePresets;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceStandardizationService
{
    /**
     * Menyeragamkan katalog 10 layanan standar inti desa secara idempotent tanpa menghapus layanan kustom atau riwayat permohonan.
     *
     * @return array{updated: int, created: int, preserved_custom: int}
     */
    public function standardize(): array
    {
        $catalog = LetterTemplatePresets::standardCatalog();

        $summary = DB::transaction(function () use ($catalog): array {
            $updated = 0;
            $created = 0;
            $matchedIds = [];

            $existingServices = Service::withCount('requirementItems')->orderBy('id')->get();

            foreach ($catalog as $item) {
                $candidates = array_merge([$item['title']], $item['aliases']);
                $normalizedCandidates = array_map(fn (string $v): string => $this->normalizeTitle($v), $candidates);

                $matched = $existingServices->first(function (Service $service) use ($matchedIds, $normalizedCandidates): bool {
                    if (in_array($service->id, $matchedIds, true)) {
                        return false;
                    }

                    return in_array($this->normalizeTitle((string) $service->title), $normalizedCandidates, true);
                });

                if ($matched) {
                    $matchedIds[] = $matched->id;

                    $matched->fill([
                        'title' => $item['title'],
                        'slug' => Str::slug($item['title']),
                        'category' => $item['category'],
                        'sort_order' => $item['sort_order'],
                        'is_active' => true,
                        'is_tte_enabled' => true,
                        'description' => $this->preferExisting($matched->description, $item['description']),
                        'requirements' => $this->preferExisting($matched->requirements, $item['requirements']),
                        'letter_code' => $this->preferExisting($matched->letter_code, $item['letter_code']),
                        'letter_title' => $this->preferExisting($matched->letter_title, $item['letter_title']),
                        'letter_template' => $this->preferExisting($matched->letter_template, $item['letter_template']),
                        'form_fields' => is_array($matched->form_fields) && $matched->form_fields !== []
                            ? $matched->form_fields
                            : $item['form_fields'],
                    ]);
                    $matched->save();

                    if ($matched->requirement_items_count === 0) {
                        $this->seedRequirementItems($matched, $item['requirement_items']);
                    }

                    $updated++;
                } else {
                    $service = Service::create([
                        'title' => $item['title'],
                        'slug' => Str::slug($item['title']),
                        'category' => $item['category'],
                        'sort_order' => $item['sort_order'],
                        'description' => $item['description'],
                        'requirements' => $item['requirements'],
                        'letter_code' => $item['letter_code'],
                        'letter_title' => $item['letter_title'],
                        'letter_template' => $item['letter_template'],
                        'form_fields' => $item['form_fields'],
                        'is_tte_enabled' => true,
                        'is_active' => true,
                    ]);

                    $this->seedRequirementItems($service, $item['requirement_items']);
                    $matchedIds[] = $service->id;
                    $created++;
                }
            }

            $preservedCustom = 0;
            $customServices = Service::whereNotIn('id', $matchedIds)->orderBy('id')->get();
            foreach ($customServices as $index => $custom) {
                $dirty = false;
                if (empty($custom->category)) {
                    $custom->category = 'Layanan Lainnya';
                    $dirty = true;
                }
                if ((int) $custom->sort_order < 100) {
                    $custom->sort_order = 100 + $index;
                    $dirty = true;
                }
                if ($dirty) {
                    $custom->save();
                }
                $preservedCustom++;
            }

            return [
                'updated' => $updated,
                'created' => $created,
                'preserved_custom' => $preservedCustom,
            ];
        });

        Cache::forget('services_list');

        return $summary;
    }

    private function normalizeTitle(string $title): string
    {
        return (string) Str::of($title)->trim()->squish()->lower();
    }

    private function preferExisting(?string $existing, string $default): string
    {
        return trim((string) $existing) !== '' ? (string) $existing : $default;
    }

    /**
     * @param  array<int, array{name: string, description: string, is_required: bool}>  $items
     */
    private function seedRequirementItems(Service $service, array $items): void
    {
        foreach ($items as $index => $item) {
            $service->requirementItems()->create([
                'name' => $item['name'],
                'description' => $item['description'],
                'is_required' => $item['is_required'],
                'sort_order' => $index + 1,
            ]);
        }
    }
}
