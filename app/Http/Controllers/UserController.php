<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WhiteLabel;
use Illuminate\Http\Request;
use League\Csv\Writer;
use Dompdf\Dompdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $users = User::forUser($user);
        
        if ($request->search) {
            $users = $users->where(function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->search}%");
            });
        }
    
        $paginationConfig = config('settings.pagination');
        $users = $users->latest()->paginate($paginationConfig)->appends(request()->except('page'));
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

        return redirect()->route('users.index', $user->id)->with('success', 'User created successfully');
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

    public function exportCSV()
    {
        $users = $this->getFilteredUsers();

        $csv = Writer::createFromString('');
        $csv->insertOne(['First Name', 'Last Name', 'Email', 'Phone', 'Address']);

        foreach ($users as $user) {
            $csv->insertOne([$user->first_name, $user->last_name, $user->email, $user->phone, $user->address]);
        }

        $fileName = 'users_' . date('Y-m-d') . '.csv';

        return response((string) $csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function exportPDF()
    {
        $users = $this->getFilteredUsers();

        $html = view('users.export', compact('users'))->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $fileName = 'users_' . date('Y-m-d') . '.pdf';

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }

    private function getFilteredUsers()
    {
        $user = auth()->user();
        $users = User::forUser($user);

        if (request('search')) {
            $users = $users->where('first_name', 'LIKE', '%' . request('search') . '%');
        }

        return $users->latest()->get();
    }
}
