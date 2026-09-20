<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected array $urgencyKeywords = [
        'critical' => ['weapon', 'knife', 'gun', 'fire', 'blood', 'unconscious', 'assault'],
        'high' => ['fight', 'violent', 'threat', 'repeat', 'minor', 'curfew', 'drunk'],
        'moderate' => ['loud', 'disturbance', 'argument', 'shouting'],
    ];

    public function create()
    {
        $categories = ReportCategory::orderBy('name')->get();
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:report_categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'location_text' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $category = ReportCategory::findOrFail($validated['category_id']);
        $severity = $this->classifySeverity($category, $validated['description']);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('report-photos', 'public');
        }

        Report::create([
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'description' => $validated['description'],
            'location_text' => $validated['location_text'],
            'photo_path' => $photoPath,
            'severity' => $severity,
            'status' => 'pending',
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