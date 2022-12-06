<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return Inertia::render('Location/Index', [
            'locations' => request()->user()->locations()->paginate(9)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'name' => ['required'],
            'name' => ['required', 'unique:locations'],
        ]);

        $request->user()->locations()->create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
        ]);

        return redirect()->back();
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('location.index');
    }
}
