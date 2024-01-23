

<?php
use App\Models\GeneralSetting;
use Carbon\Carbon;

function isOfficeHours(){

    $setting  = GeneralSetting::where('Days', strtolower(Carbon::now()->format('l')))->first();

    if (!$setting || $setting->holiday == 'off') {

        return false; // It's a holiday or off day
    }

    $currentTime = now()->format('H:i:s');

    return $currentTime >= $setting->StartTime && $currentTime <= $setting->EndTime;
}

?>
