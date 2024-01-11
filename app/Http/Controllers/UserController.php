<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WhiteLabel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $users = User::forUser($user)->latest()->paginate(config('settings.pagination'));
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $whiteLabels = WhiteLabel::all();

        return view('users.create', ['whiteLabels' => $whiteLabels]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'white_label_id' => 'nullable|exists:white_labels,id',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        $whiteLabelId = $request->input('white_label_id', $request->user()->white_label_id);
        $validatedData['white_label_id'] = $whiteLabelId;

        $user = User::create($validatedData);
        if (!$user) {
            return redirect()->back()->with('error', __('User creation failed.'));
        }

        return redirect()->route('users.show', $user->id)->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $whiteLabels = WhiteLabel::all();
        return view('users.edit', ['user' => $user, 'whiteLabels' => $whiteLabels]);
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|unique:users,email,$user->id",
            'password' => 'nullable|min:6',
            'role' => 'required',
            'white_label_id' => 'nullable|exists:white_labels,id',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);
        $whiteLabelId = $request->input('white_label_id', $request->user()->white_label_id);
        $validatedData['white_label_id'] = $whiteLabelId;
       
        $user->update($validatedData);
        if (!$user) {
            return redirect()->route('users.edit', $user->id)->with('error', 'User update failed');
        }

        return redirect()->route('users.edit', $user->id)->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
