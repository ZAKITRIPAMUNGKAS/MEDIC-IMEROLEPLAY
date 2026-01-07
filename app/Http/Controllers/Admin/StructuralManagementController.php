<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StructuralManagementController extends Controller
{
    /**
     * Display a listing of the resource with tree structure
     */
    public function index()
    {
        // Get all positions with their relationships, ordered hierarchically
        $positions = OrganizationalPosition::with(['user.role', 'parent', 'children'])
            ->orderBy('level')
            ->orderBy('display_order')
            ->get();

        // Build tree structure for display
        $tree = $this->buildTree($positions);

        // Get all active users for assignment dropdown
        $users = User::where('is_active', true)
            ->with('role')
            ->orderBy('name')
            ->get();

        return view('admin.structural.index', compact('positions', 'tree', 'users'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        // Get all active users for assignment
        $users = User::where('is_active', true)
            ->with('role')
            ->orderBy('name')
            ->get();

        // Get all positions for parent selection
        $positions = OrganizationalPosition::orderBy('level')
            ->orderBy('display_order')
            ->get();

        // Level options
        $levels = $this->getLevelOptions();

        return view('admin.structural.create', compact('users', 'positions', 'levels'));
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|integer|min:0|max:10',
            'level_key' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:organizational_positions,id',
            'title' => 'required|string|max:255',
            'position_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        // Auto-generate level_key if not provided
        if (empty($validated['level_key'])) {
            $validated['level_key'] = 'level_' . $validated['level'];
        }

        // Auto-set display_order if not provided
        if (!isset($validated['display_order'])) {
            $maxOrder = OrganizationalPosition::where('level', $validated['level'])->max('display_order');
            $validated['display_order'] = ($maxOrder ?? 0) + 1;
        }

        OrganizationalPosition::create($validated);

        return redirect()->route('admin.structural.index')
            ->with('success', 'Position created successfully!');
    }

    /**
     * Display the specified resource
     */
    public function show(OrganizationalPosition $structural)
    {
        $structural->load(['user.role', 'parent', 'children.user']);

        if (request()->wantsJson()) {
            return response()->json($structural);
        }

        return view('admin.structural.show', compact('structural'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(OrganizationalPosition $structural)
    {
        // Get all active users for assignment
        $users = User::where('is_active', true)
            ->with('role')
            ->orderBy('name')
            ->get();

        // Get all positions for parent selection (exclude self and descendants)
        $positions = OrganizationalPosition::where('id', '!=', $structural->id)
            ->orderBy('level')
            ->orderBy('display_order')
            ->get();

        // Level options
        $levels = $this->getLevelOptions();

        return view('admin.structural.edit', compact('structural', 'users', 'positions', 'levels'));
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, OrganizationalPosition $structural)
    {
        $validated = $request->validate([
            'level' => 'required|integer|min:0|max:10',
            'level_key' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:organizational_positions,id',
            'title' => 'required|string|max:255',
            'position_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        // Prevent making a position its own parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $structural->id) {
            return back()->withErrors(['parent_id' => 'A position cannot be its own parent.']);
        }

        // Auto-generate level_key if not provided
        if (empty($validated['level_key'])) {
            $validated['level_key'] = 'level_' . $validated['level'];
        }

        $structural->update($validated);

        return redirect()->route('admin.structural.index')
            ->with('success', 'Position updated successfully!');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(OrganizationalPosition $structural)
    {
        // Check if has children
        if ($structural->hasChildren()) {
            return back()->withErrors(['delete' => 'Cannot delete position with child positions. Delete or reassign children first.']);
        }

        $structural->delete();

        return redirect()->route('admin.structural.index')
            ->with('success', 'Position deleted successfully!');
    }

    /**
     * Reorder positions via AJAX
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:organizational_positions,id',
            'positions.*.display_order' => 'required|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['positions'] as $positionData) {
                OrganizationalPosition::where('id', $positionData['id'])
                    ->update(['display_order' => $positionData['display_order']]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Positions reordered successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to reorder positions.'], 500);
        }
    }

    /**
     * Build tree structure from flat collection
     */
    private function buildTree($positions, $parentId = null)
    {
        $tree = [];

        foreach ($positions as $position) {
            if ($position->parent_id == $parentId) {
                $children = $this->buildTree($positions, $position->id);
                $node = [
                    'position' => $position,
                    'children' => $children
                ];
                $tree[] = $node;
            }
        }

        return $tree;
    }

    /**
     * Get level options for dropdown
     */
    private function getLevelOptions()
    {
        return [
            0 => 'Level 0 - High Command (CEO, Director, etc)',
            1 => 'Level 1 - Deputy / Vice',
            2 => 'Level 2 - Department Heads',
            3 => 'Level 3 - Unit Heads / Managers',
            4 => 'Level 4 - Deputy Managers / Staff Managers',
            5 => 'Level 5 - Staff / Team Members',
            6 => 'Level 6 - Sub-teams / Specialized Units',
            7 => 'Level 7 - Support Staff',
        ];
    }
}
