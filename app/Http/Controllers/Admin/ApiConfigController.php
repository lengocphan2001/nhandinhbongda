<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ApiConfigController extends Controller
{
    /**
     * Display the API configuration page.
     */
    public function index()
    {
        $configFile = base_path('.env');
        $config = [];
        
        if (File::exists($configFile)) {
            $lines = File::lines($configFile);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, 'SOCCERSAPI') !== false) {
                    [$key, $value] = explode('=', $line, 2);
                    $config[trim($key)] = trim($value);
                }
            }
        }

        return view('admin.api-config.index', compact('config'));
    }

    /**
     * Update API configuration.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'SOCCERSAPI_USER' => 'nullable|string',
            'SOCCERSAPI_TOKEN' => 'nullable|string',
        ]);

        $envFile = base_path('.env');
        
        if (File::exists($envFile)) {
            $content = File::get($envFile);
            
            foreach ($validated as $key => $value) {
                if ($value) {
                    // Replace existing or add new
                    if (preg_match("/^{$key}=.*/m", $content)) {
                        $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
                    } else {
                        $content .= "\n{$key}={$value}";
                    }
                }
            }
            
            File::put($envFile, $content);
            
            // Clear config cache to ensure new values are loaded
            Artisan::call('config:clear');
        }

        return redirect()->route('admin.api-config.index')
            ->with('success', 'Cấu hình API đã được cập nhật thành công!');
    }
}
