<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
     public function index(Request $request)
    {
        $settings = Setting::where('user_id', auth()->id())->first();

        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'api_url' => 'nullable|url',
                'groupby' => 'required|integer|min:1|max:100',
                'lr' => 'nullable|integer',
                'domain' => 'required|string|in:ru,com,ua,com.tr,by,kz',
                'lang' => 'required|string|in:ru,uk,en,tr,be,kk',
                'device' => 'required|string|in:desktop,tablet,mobile',
                'page' => 'required|integer|min:0',
                'yandex_client_id' => 'nullable|string',
                'yandex_client_secret' => 'nullable|string',
                'yandex_redirect_uri' => 'nullable|url',
                'yandex_metrika_token' => 'nullable|string',
                'yandex_metrika_counter' => 'nullable|string',
                'yandex_metrika_period' => 'nullable|string',
                'yandex_metrika_metrics' => 'nullable|string',
                'yandex_metrika_filters' => 'nullable|string',
                'yandex_metrika_dimensions' => 'nullable|string',
                'yandex_metrika_sort' => 'nullable|string',
            ]);

            if ($settings) {
                $settings->update(array_merge($validated, ['user_id' => auth()->id()]));
            } else {
                $settings = Setting::create(array_merge($validated, ['user_id' => auth()->id()]));
            }

            return redirect()->route('settings')->with('success', 'Настройки сохранены');
        }

        return view('settings/index', compact('settings'));
    }

    public function company()
    {
        return view('settings/company');
    }

    public function currencies()
    {
        return view('settings/currencies');
    }

    public function language()
    {
        return view('settings/language');
    }

    public function notification()
    {
        return view('settings/notification');
    }

    public function notificationAlert()
    {
        return view('settings/notificationAlert');
    }

    public function paymentGateway()
    {
        return view('settings/paymentGateway');
    }

    public function theme()
    {
        return view('settings/theme');
    }

}
