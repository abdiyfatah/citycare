<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::withCount('doctors')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        Department::create($data);

        return redirect()->route('departments.index')
                         ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load('doctors.user');
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $department->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('departments.show', $department)
                         ->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        if ($department->doctors()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete a department that has doctors assigned.']);
        }
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }
}
