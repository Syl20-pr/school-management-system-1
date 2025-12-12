<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BulletinCalculationService;
use App\Services\StudentRankingService;
use App\Services\PdfGenerationService;
use App\Services\StudentPromotionService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BulletinCalculationService::class, function ($app) {
            return new BulletinCalculationService($app->make(StudentRankingService::class));
        });
        
        $this->app->singleton(StudentRankingService::class, function ($app) {
            return new StudentRankingService();
        });
        
        $this->app->singleton(PdfGenerationService::class, function ($app) {
            return new PdfGenerationService();
        });

        $this->app->bind(StudentPromotionService::class, function ($app) {
        return new StudentPromotionService();
    });
    }

    public function boot()
    {
        //
    }
}