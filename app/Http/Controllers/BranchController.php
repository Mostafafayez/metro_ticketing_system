<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    // List all branches
    public function index()
    {
        return Branch::all();
    }

    // Add a new branch
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $branch = Branch::create($request->only(['name', 'location']));
        return response()->json($branch, 201);
    }
}
