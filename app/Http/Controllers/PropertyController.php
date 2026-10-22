<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with('images');

        $selectedLocations = array_filter((array) $request->input('location', []));
        if (!empty($selectedLocations)) {
            $query->whereIn('location', $selectedLocations);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->input('bedrooms'));
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->input('bathrooms'));
        }

        if ($request->filled('area_min')) {
            $query->where('area', '>=', $request->input('area_min'));
        }

        if ($request->filled('area_max')) {
            $query->where('area', '<=', $request->input('area_max'));
        }

        if ($request->boolean('parking')) {
            $query->where('has_parking', true);
        }

        $properties = $query->latest()->paginate(12)->withQueryString();

        $locations = Property::select('location')->distinct()->orderBy('location')->pluck('location');

        return view('properties.index', compact('properties', 'locations'));
    }

    public function show(Property $property)
    {
        $property->load('images', 'videos');
        return view('properties.show', compact('property'));
    }
}
