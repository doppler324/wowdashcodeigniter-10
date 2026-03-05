<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Генерируем хлебные крошки для главной страницы
        $breadcrumbService = app(\App\Services\BreadcrumbService::class);
        $breadcrumbService->generateFromRoute();
        $breadcrumbs = $breadcrumbService->get();

        return view('dashboard/index', compact('breadcrumbs'));
    }

    public function index2()
    {
        return view('dashboard/index2');
    }

    public function index3()
    {
        return view('dashboard/index3');
    }

    public function index4()
    {
        return view('dashboard/index4');
    }

    public function index5()
    {
        return view('dashboard/index5');
    }

    public function wallet()
    {
        return view('dashboard/wallet');
    }

}
