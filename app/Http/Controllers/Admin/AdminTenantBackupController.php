<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class AdminTenantBackupController extends Controller
{
    public function index()
    {
        $tenants = Tenant::orderBy('TenantID')->get();

        return view('admin.backups.tenants', compact('tenants'));
    }

    public function backupTenant(Request $request, Tenant $tenant): RedirectResponse
    {
        [$ok, $message] = $this->runBackup($tenant);

        return back()->with($ok ? 'status' : 'error', $message);
    }

    public function backupAll(Request $request): RedirectResponse
    {
        $tenants = Tenant::orderBy('TenantID')->get();
        $success = 0;
        $failed = 0;

        foreach ($tenants as $tenant) {
            [$ok, $message] = $this->runBackup($tenant);
            if ($ok) {
                $success++;
            } else {
                $failed++;
                Log::warning('Tenant backup failed', [
                    'tenant_id' => $tenant->TenantID,
                    'db' => $tenant->DBName,
                    'message' => $message,
                ]);
            }
        }

        if ($failed === 0) {
            return back()->with('status', __('تم إنشاء نسخ احتياطية لجميع المشتركين بنجاح (:count).', ['count' => $success]));
        }

        return back()->with('error', __('تم إنشاء نسخ احتياطية لعدد :ok مشتركين، وفشل العدد :failed. تحقق من سجل النظام.', [
            'ok' => $success,
            'failed' => $failed,
        ]));
    }

    /**
     * Run a database backup for a single tenant.
     */
    protected function runBackup(Tenant $tenant): array
    {
        $dbName = $tenant->DBName;
        if (! $dbName) {
            return [false, __('لا يوجد اسم قاعدة بيانات معرف لهذا المشترك.')];
        }

        $default = config('database.connections.mysql');

        // استخدم بيانات اتصال MySQL الرئيسية (مثل root من .env) لعمل النسخ الاحتياطي
        // حتى لو كان لكل تينانت مستخدمه الخاص، فهذا الإجراء إداري فقط.
        $host = $default['host'] ?? '127.0.0.1';
        $port = $default['port'] ?? 3306;
        $username = $default['username'] ?? 'root';
        $password = $default['password'] ?? '';

        $backupDir = storage_path('app/tenant-backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');
        $fileName = sprintf('%s_%s.sql', $dbName, $timestamp);
        $filePath = $backupDir.DIRECTORY_SEPARATOR.$fileName;

        // Allow configuring full mysqldump path from .env (e.g. on Windows/Laragon)
        $mysqldump = env('MYSQLDUMP_PATH', 'mysqldump');

        $command = [
            $mysqldump,
            '-h', $host,
            '-P', (string) $port,
            '-u', $username,
        ];

        // Only append password flag if a password is set
        if ($password !== '') {
            $command[] = '-p'.$password;
        }

        $command = array_merge($command, [
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--default-auth=caching_sha2_password',
            $dbName,
        ]);

        $process = new Process($command);
        $process->setTimeout(300); // 5 minutes
        $process->run();

        if (! $process->isSuccessful()) {
            return [false, trim($process->getErrorOutput()) ?: __('فشل تنفيذ أمر النسخ الاحتياطي. تأكد من توفر mysqldump.')];
        }

        file_put_contents($filePath, $process->getOutput());

        return [true, __('تم إنشاء نسخة احتياطية لقاعدة بيانات :db بنجاح.', ['db' => $dbName])];
    }
}
