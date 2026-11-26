<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $categories = Document::CATEGORY_OPTIONS;
        $classifications = Document::INFORMATION_CLASSIFICATION_OPTIONS;
        $maxYear = (int) date('Y') + 1;

        $selectedCategory = $request->input('category');
        if (! is_string($selectedCategory) || ! array_key_exists($selectedCategory, $categories)) {
            $selectedCategory = null;
        }

        $selectedClassification = $request->input('classification');
        if (! is_string($selectedClassification) || ! array_key_exists($selectedClassification, $classifications)) {
            $selectedClassification = null;
        }

        $rawYear = $request->input('year');
        $selectedYear = null;
        if (is_numeric($rawYear)) {
            $parsedYear = (int) $rawYear;
            if ($parsedYear >= 1990 && $parsedYear <= $maxYear) {
                $selectedYear = $parsedYear;
            }
        }

        $search = trim((string) $request->input('search', ''));

        $query = Document::query()
            ->when($selectedCategory, fn ($q) => $q->where('category', $selectedCategory))
            ->when($selectedClassification, fn ($q) => $q->where('information_classification', $selectedClassification))
            ->when($selectedYear, fn ($q) => $q->where('year', $selectedYear))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('year')
            ->orderByDesc('published_date')
            ->latest();

        $documents = $query->paginate(10)->withQueryString();

        $availableYears = Document::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('documents.index', compact(
            'documents',
            'categories',
            'classifications',
            'availableYears',
            'selectedCategory',
            'selectedClassification',
            'selectedYear',
            'search'
        ));
    }
}
