<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.edit');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $whiteLabelId = $request->user()->white_label_id;
        
        foreach ($data as $key => $value) {
            // dd(Setting::where('key', $key)->where('white_label_id', $whiteLabelId)->get());
            $setting = Setting::where('key', $key)
                ->where('white_label_id', $whiteLabelId)
                ->first();
                
            if ($setting) {
                // Update the existing setting
                $setting->value = $value;
                $setting->save();
            } else {
                
                // Create a new setting
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'white_label_id' => $whiteLabelId,
                ]);
            }
        }
    
        return redirect()->route('settings.index');
    }
    
    

}
