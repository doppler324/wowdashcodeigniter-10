<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
     public function index(Request $request)
    {
        $settings = Setting::where('user_id', auth()->id())->first();

        \Illuminate\Support\Facades\Log::info('SettingsController: index method called');
        \Illuminate\Support\Facades\Log::info('Request method: ' . $request->method());
        if ($request->isMethod('POST')) {
            \Illuminate\Support\Facades\Log::info('POST data received: ' . json_encode($request->all()));
            // Валидация полей — делаем все поля nullable, чтобы не требовать их из других вкладок
            $validated = $request->validate([
                'api_url' => 'nullable|url',
                'groupby' => 'nullable|integer|min:1|max:100',
                'lr' => 'nullable|integer',
                'domain' => 'nullable|string|in:ru,com,ua,com.tr,by,kz',
                'lang' => 'nullable|string|in:ru,uk,en,tr,be,kk',
                'device' => 'nullable|string|in:desktop,tablet,mobile',
                'page' => 'nullable|integer|min:0',
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

            // Фильтруем null и пустые строки, чтобы не перезаписывать существующие значения
            $validated = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            \Illuminate\Support\Facades\Log::info('Validated data: ' . json_encode($validated));
            \Illuminate\Support\Facades\Log::info('Current settings before update: ' . json_encode($settings));

            // Обновляем только переданные поля (чтобы не перезаписывать другие вкладки)
            $dataToUpdate = array_merge($validated, ['user_id' => auth()->id()]);

            // Если настроек еще нет — создаем новые
            if (!$settings) {
                $settings = Setting::create($dataToUpdate);
            } else {
                // Обновляем только те поля, которые были переданы в запросе
                foreach ($dataToUpdate as $key => $value) {
                    if ($key !== 'user_id') { // user_id не меняем
                        $settings->{$key} = $value;
                    }
                }
                $settings->save();
            }

            \Illuminate\Support\Facades\Log::info('Settings after update: ' . json_encode($settings->fresh()));

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
