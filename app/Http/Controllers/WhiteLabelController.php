<?php

namespace App\Http\Controllers;

use App\Models\WhiteLabel;
use Illuminate\Http\Request;

class WhiteLabelController extends Controller
{
    public function index()
    {
        $whiteLabels = WhiteLabel::latest()->paginate(20);
        return view('whitelabels.index', compact('whiteLabels'));
    }

    public function create()
    {
        return view('whitelabels.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'domain' => 'required',
            'description' => 'required',
            'url' => 'required'
        ], [
            'domain.required' => 'The domain field is required.',
            'description.required' => 'The description field is required.',
            'url.required' => 'The URL field is required.'
        ]);
    
        WhiteLabel::create($validatedData);
        return redirect()->route('white_labels.index')->with('success', 'White Label created successfully');
    }
    

    public function edit(WhiteLabel $whiteLabel)
    {
        return view('whitelabels.edit', compact('whiteLabel'));
    }

    public function update(Request $request, WhiteLabel $whiteLabel)
    {
        $validatedData = $request->validate([
            'domain' => 'required',
            'description' => 'required',
            'url' => 'required'
        ]);

        $whiteLabel->update($validatedData);
        return redirect()->route('white-labels.index')->with('success', 'White Label updated successfully');
    }

    public function show(WhiteLabel $whiteLabel)
    {
        return view('whitelabels.show', compact('whiteLabel'));
    }

    public function destroy(WhiteLabel $whiteLabel)
    {
        $whiteLabel->delete();
        return redirect()->route('white-labels.index')->with('success', 'White Label deleted successfully');
    }
}
