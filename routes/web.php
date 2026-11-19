<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $agent = new Agent();
    $agent->setUserAgent($request->header('User-Agent'));

    $device = [
        'browser' => $agent->browser(),
        'os' => $agent->platform(),
        'device' => $agent->device(),
    ];

    $ip = $request->ip();
    $location = null;

    if (in_array($ip, ['127.0.0.1', '::1'])) {
        $location = [
            'ip' => $ip,
            'country' => 'Localhost',
            'region' => 'Development',
            'city' => 'Local Machine',
        ];
    } else {
        try {
            $res = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,query");
            if ($res->successful() && $res['status'] === 'success') {
                $location = [
                    'ip' => $res['query'],
                    'country' => $res['country'],
                    'region' => $res['regionName'],
                    'city' => $res['city'],
                ];
            }
        } catch (\Exception $e) {
        }
    }

    return view('welcome', compact('device', 'location'));
});

Route::get("/storage", function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return "storage linked";
});
