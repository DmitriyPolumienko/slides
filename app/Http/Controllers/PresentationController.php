<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\MasterTemplate;
use App\Models\Presentation;
use App\Models\Project;
use App\Models\Theme;
use App\Services\Export\FigmaExportService;
use App\Services\Export\PdfExportService;
use App\Services\Export\PptxExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PresentationController extends Controller
{
    public function index()
    {
        $presentations = Presentation::with(['project', 'theme', 'masterTemplate'])
            ->withCount('slides')
            ->latest()
            ->paginate(20);
        return view('presentations.index', compact('presentations'));
    }

    public function create()
    {
        $projects = Project::all();
        $themes = Theme::all();
        $languages = Language::all();
        $templates = MasterTemplate::where('is_active', true)->get();
        return view('presentations.create', compact('projects', 'themes', 'languages', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'theme_id' => 'nullable|exists:themes,id',
            'language_id' => 'nullable|exists:languages,id',
            'master_template_id' => 'nullable|exists:master_templates,id',
        ]);

        $presentation = Presentation::create($validated);
        return redirect()->route('presentations.builder', $presentation);
    }

    public function builder(Presentation $presentation)
    {
        $presentation->load(['slides.slots', 'dataSources', 'project', 'theme', 'language', 'masterTemplate.versions']);
        return view('presentations.builder', compact('presentation'));
    }

    public function exportPdf(Presentation $presentation, PdfExportService $service): BinaryFileResponse
    {
        $path = $service->export($presentation);
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportPptx(Presentation $presentation, PptxExportService $service): BinaryFileResponse
    {
        $path = $service->export($presentation);
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportFigma(Presentation $presentation, FigmaExportService $service): JsonResponse
    {
        $figmaJson = $service->export($presentation);
        return response()->json($figmaJson, 200, [
            'Content-Disposition' => 'attachment; filename="figma_export_' . $presentation->id . '.json"',
        ]);
    }
}
