<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SchemaHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class SystemHealthController extends Controller
{
    public function index(): View
    {
        $databaseOperational = $this->databaseOperational();
        $storageOperational = $this->storageOperational();

        $queuePending = SchemaHelper::tableExists('jobs')
            ? DB::table('jobs')->count()
            : 0;

        $queueFailed = SchemaHelper::tableExists('failed_jobs')
            ? DB::table('failed_jobs')->count()
            : 0;

        $notificationsCount = SchemaHelper::tableExists('notifications')
            ? DB::table('notifications')->count()
            : 0;

        $unreadNotifications = SchemaHelper::tableExists('notifications')
            ? DB::table('notifications')
                ->whereNull('read_at')
                ->count()
            : 0;

        $sessionsCount = SchemaHelper::tableExists('sessions')
            ? DB::table('sessions')->count()
            : 0;

        $health = [
            'database' => [
                'label' => 'Base PostgreSQL',
                'operational' => $databaseOperational,
                'value' => $databaseOperational
                    ? 'Opérationnelle'
                    : 'Indisponible',
            ],

            'storage' => [
                'label' => 'Stockage',
                'operational' => $storageOperational,
                'value' => $storageOperational
                    ? 'Accessible en écriture'
                    : 'Erreur d’écriture',
            ],

            'cache' => [
                'label' => 'Cache',
                'operational' => SchemaHelper::tableExists('cache'),
                'value' => config('cache.default'),
            ],

            'session' => [
                'label' => 'Sessions',
                'operational' => SchemaHelper::tableExists('sessions'),
                'value' => $sessionsCount . ' session(s)',
            ],

            'queue' => [
                'label' => 'File d’attente',
                'operational' => SchemaHelper::tableExists('jobs'),
                'value' => $queuePending . ' tâche(s) en attente',
            ],

            'failed_jobs' => [
                'label' => 'Tâches échouées',
                'operational' => $queueFailed === 0,
                'value' => $queueFailed . ' échec(s)',
            ],

            'notifications' => [
                'label' => 'Notifications',
                'operational' => SchemaHelper::tableExists('notifications'),
                'value' => sprintf(
                    '%d total, %d non lue(s)',
                    $notificationsCount,
                    $unreadNotifications
                ),
            ],

            'environment' => [
                'label' => 'Environnement',
                'operational' => ! app()->environment('production')
                    || ! config('app.debug'),
                'value' => app()->environment(),
            ],

            'debug' => [
                'label' => 'Mode debug',
                'operational' => ! app()->environment('production')
                    || ! config('app.debug'),
                'value' => config('app.debug')
                    ? 'Activé'
                    : 'Désactivé',
            ],
        ];

        $technical = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'filesystem_disk' => config('filesystems.default'),
            'mail_driver' => config('mail.default'),
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
        ];

        $globalOperational = collect($health)
            ->every(
                fn (array $component): bool =>
                    $component['operational'] === true
            );

        return view(
            'admin.system-health.index',
            compact(
                'health',
                'technical',
                'globalOperational'
            )
        );
    }

    private function databaseOperational(): bool
    {
        try {
            DB::select('SELECT 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function storageOperational(): bool
    {
        try {
            $path = 'health-check/pnae-health.txt';

            Storage::disk(config('filesystems.default'))
                ->put($path, now()->toIso8601String());

            Storage::disk(config('filesystems.default'))
                ->delete($path);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}