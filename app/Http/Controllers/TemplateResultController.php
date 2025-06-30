<?php

namespace App\Http\Controllers;

use App\Models\TemplateResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateResultController extends Controller
{
    public function index(Request $request)
    {
        $query = TemplateResult::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
        }

        if ($request->has('type') && in_array($request->type, ['mc', 'skb'])) {
            $query->where('type', $request->type);
        }

        $templates = $query->latest()->paginate(10);
        $totalTemplates = TemplateResult::count();

        return view('superadmin.templates.index', compact('templates', 'totalTemplates'));
    }

    public function create()
    {
        return view('superadmin.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:template_results',
            'type' => 'required|in:mc,skb',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        TemplateResult::create($validated + [
            'created_by' => Auth::id(),
            'default' => $request->has('default'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('template-results.index')->with('success', 'Template berhasil ditambahkan.');
    }

    public function edit(TemplateResult $templateResult)
    {
        return view('superadmin.templates.edit', compact('templateResult'));
    }

    public function update(Request $request, TemplateResult $templateResult)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:template_results,code,' . $templateResult->id,
            'type' => 'required|in:mc,skb',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $templateResult->update($validated + [
            'default' => $request->has('default'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('template-results.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(TemplateResult $templateResult)
    {
        $templateResult->delete();
        return redirect()->route('template-results.index')->with('success', 'Template berhasil dihapus.');
    }
}
