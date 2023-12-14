<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function create()
    {
    
        $generalSettings = GeneralSetting::find(range(1, 7));
        return view('admin.GeneralSetting.Generalcreate', compact('generalSettings'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'StartTime' => 'required',
            'EndTime' => 'required',
        ]);

        try {

            $generalSettings = GeneralSetting::whereIn('id', range(1, 7))->get();
            foreach ($generalSettings as $generalSetting) {
                $day = $generalSetting->Days;
                $isDaySelected = $request->has("holiday.{$day}") ? 'on' : 'off';

                $generalSetting->update([
                    'holiday' => $isDaySelected,
                    'StartTime' => $request->StartTime,
                    'Endtime' => $request->EndTime,
                ]);
            }
            return redirect()->route('General.create')->with('success', 'Time Saved Successfully!');
        } catch (\Throwable $th) {
           
            return redirect()->route('General.create')->with('error', 'Internal Server Error!');
        }
    }
}
