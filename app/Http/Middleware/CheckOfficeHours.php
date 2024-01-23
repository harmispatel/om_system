<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\GeneralSetting;
use Carbon\Carbon;
class CheckOfficeHours
{
    public function handle($request, Closure $next)
    {
        $setting  = GeneralSetting::where('Days', strtolower(Carbon::now()->format('l')))->first();

        if (!$setting || $setting->holiday == 'off') {
            return redirect()->back()->with('error', "It's a holiday or not office hours.");
        }

        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime < $setting->StartTime || $currentTime > $setting->EndTime) {
            return redirect()->back()->with('error', "It's not office hours.");
        }

        return $next($request);
    }
}

