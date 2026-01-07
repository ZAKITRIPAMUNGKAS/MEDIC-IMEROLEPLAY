<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalStructure;
use Illuminate\Http\Request;

class OrganizationalStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $structures = OrganizationalStructure::orderBy('created_at', 'desc')->get();

        // Get all active users for dropdown
        $users = \App\Models\User::where('is_active', true)
            ->whereNotNull('role_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.organizational-structure.index', compact('structures', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.organizational-structure.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'hospital_type' => 'required|in:ems,roxwood',
            'structure_data' => 'required|json',
            'required_names' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse structure_data from JSON string
        $validated['structure_data'] = json_decode($validated['structure_data'], true);

        // Parse required_names from textarea (one name per line)
        if (!empty($validated['required_names'])) {
            $names = array_filter(array_map('trim', explode("\n", $validated['required_names'])));
            $validated['required_names'] = $names;
        } else {
            $validated['required_names'] = [];
        }

        $structure = OrganizationalStructure::create($validated);

        // If marked as active, activate it (will deactivate others)
        if ($request->boolean('is_active')) {
            $structure->activate();
        }

        return redirect()->route('admin.organizational-structure.index')
            ->with('success', 'Struktur organisasi berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $structure = OrganizationalStructure::findOrFail($id);
        return view('admin.organizational-structure.edit', compact('structure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $structure = OrganizationalStructure::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'hospital_type' => 'required|in:ems,roxwood',
            'structure_data' => 'required|json',
            'required_names' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse structure_data from JSON string
        $validated['structure_data'] = json_decode($validated['structure_data'], true);

        // Parse required_names from textarea (one name per line)
        if (!empty($validated['required_names'])) {
            $names = array_filter(array_map('trim', explode("\n", $validated['required_names'])));
            $validated['required_names'] = $names;
        } else {
            $validated['required_names'] = [];
        }

        $structure->update($validated);

        // If marked as active, activate it (will deactivate others)
        if ($request->boolean('is_active')) {
            $structure->activate();
        }

        return redirect()->route('admin.organizational-structure.index')
            ->with('success', 'Struktur organisasi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $structure = OrganizationalStructure::findOrFail($id);

        // Prevent deleting active structure
        if ($structure->is_active) {
            return redirect()->route('admin.organizational-structure.index')
                ->with('error', 'Tidak dapat menghapus struktur yang sedang aktif!');
        }

        $structure->delete();

        return redirect()->route('admin.organizational-structure.index')
            ->with('success', 'Struktur organisasi berhasil dihapus!');
    }

    /**
     * Activate a specific structure
     */
    public function activate($id)
    {
        $structure = OrganizationalStructure::findOrFail($id);
        $structure->activate();

        return redirect()->route('admin.organizational-structure.index')
            ->with('success', 'Struktur berhasil diaktifkan!');
    }

    /**
     * Show form to edit names only (simplified interface)
     */
    public function editNames($id)
    {
        $structure = OrganizationalStructure::findOrFail($id);
        return view('admin.organizational-structure.edit-names', compact('structure'));
    }

    /**
     * Update names in the structure
     */
    public function updateNames(Request $request, $id)
    {
        $structure = OrganizationalStructure::findOrFail($id);

        // Validate basic fields
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'names' => 'required|array',
            'names.*.name' => 'nullable|string|max:255',
            'names.*.position' => 'required|string',
        ]);

        // Update structure name if provided
        if (isset($validated['name'])) {
            $structure->name = $validated['name'];
        }

        // Get current structure data
        $structureData = $structure->structure_data;

        // Update names based on position keys
        foreach ($validated['names'] as $item) {
            $position = $item['position'];
            $name = $item['name'] ?? '';

            if ($position === 'high_command') {
                $structureData['high_command']['name'] = $name;
            } else if (str_starts_with($position, 'department_')) {
                // Parse department_X or department_X_member_Y
                $parts = explode('_', $position);
                $deptIndex = (int) $parts[1];

                if (count($parts) === 2) {
                    // Department head
                    $structureData['departments'][$deptIndex]['name'] = $name;
                } else if (count($parts) === 4 && $parts[2] === 'member') {
                    // Department member
                    $memberIndex = (int) $parts[3];
                    $structureData['departments'][$deptIndex]['members'][$memberIndex]['name'] = $name;
                }
            }
        }

        // Save updated structure
        $structure->structure_data = $structureData;
        $structure->save();

        return redirect()->route('admin.organizational-structure.index')
            ->with('success', 'Nama staff berhasil diperbarui!');
    }
}
