<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected array $urgencyKeywords = [
        'critical' => ['weapon', 'knife', 'gun', 'fire', 'blood', 'unconscious', 'assault'],
        'high' => ['fight', 'violent', 'threat', 'repeat', 'minor', 'curfew', 'drunk'],
        'moderate' => ['loud', 'disturbance', 'argument', 'shouting'],
    ];

    public function create()
    {
        $categories = ReportCategory::orderByRaw("name = 'Other'")->orderBy('name')->get();
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:report_categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'location_text' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $category = ReportCategory::findOrFail($validated['category_id']);
        $severity = $this->classifySeverity($category, $validated['description']);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('report-photos', 'public');
        }

        $report = Report::create([
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'description' => $validated['description'],
            'location_text' => $validated['location_text'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'photo_path' => $photoPath,
            'severity' => $severity,
            'status' => 'pending',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'report_submitted',
            'auditable_type' => Report::class,
            'auditable_id' => $report->id,
            'description' => 'Incident report submitted.',
            'metadata' => [
                'category' => $category->name,
                'severity' => $severity,
            ],
        ]);

        return redirect()->route('reports.create')->with('status', 'Your report has been submitted. Severity: ' . ucfirst($severity));
    }

    public function myReports()
    {
        $reports = Report::where('user_id', Auth::id())
            ->with('category')
            ->latest()
            ->get();

        return view('reports.index', compact('reports'));
    }

    protected function classifySeverity(ReportCategory $category, string $description): string
    {
        $aiResult = $this->classifySeverityWithAI($category, $description);

        return $aiResult ?? $this->classifySeverityWithKeywords($category, $description);
    }

    protected function classifySeverityWithAI(ReportCategory $category, string $description): ?string
    {
        $apiKey = config('services.gemini.key');

        $prompt = "You are a severity classifier for a barangay incident reporting system. "
            . "Given the category and description below, respond with EXACTLY ONE WORD: "
            . "low, moderate, high, or critical. No punctuation, no explanation, just the single word.\n\n"
            . "Category: {$category->name}\n"
            . "Default severity for this category: {$category->default_severity}\n"
            . "Description: {$description}";

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(8)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]],
                    ],
                ]
            );

            $text = strtolower(trim($response->json('candidates.0.content.parts.0.text') ?? ''));

            if (in_array($text, ['low', 'moderate', 'high', 'critical'])) {
                return $text;
            }
        } catch (\Exception $e) {
            // Fall through to keyword-based fallback below
        }

        return null;
    }

    protected function classifySeverityWithKeywords(ReportCategory $category, string $description): string
    {
        $text = strtolower($description);
        $levels = ['low' => 0, 'moderate' => 1, 'high' => 2, 'critical' => 3];
        $highest = $levels[$category->default_severity] ?? 0;

        foreach ($this->urgencyKeywords as $level => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword) && $levels[$level] > $highest) {
                    $highest = $levels[$level];
                }
            }
        }

        return array_search($highest, $levels);
    }
}