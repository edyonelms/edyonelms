<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SecureInput
{
    protected $maliciousPatterns = [
        // Your specific threat - FIXED: removed extra slashes
        'carto\.run\.place',
        'https:\/\/carto\.run\.place\/index\.js\?a',

        // General XSS patterns - FIXED: proper regex
        '<script\b[^>]*>.*?</script>',
        'javascript:',
        'onload\s*=',
        'onerror\s*=',
        'onclick\s*=',
        'onmouseover\s*=',
        'data:text\/html',
        'base64_decode',
        'eval\s*\(',
        'document\.',
        'window\.',
        'alert\s*\(',
        'prompt\s*\(',
        'confirm\s*\(',

        // SQL Injection patterns
        'union\s+select',
        'select\s+from',
        'insert\s+into',
        'delete\s+from',
        'update\s+set',
        'drop\s+table',
        'truncate\s+table',
        'exec\s*\(',
        '--\s',
        '\/\*',
        '\*\/',
        'waitfor\s+delay',
        'sleep\s*\(',
        'benchmark\s*\(',

        // Command injection
        'system\s*\(',
        'shell_exec\s*\(',
        'exec\s*\(',
        'passthru\s*\(',
        'popen\s*\(',
        'proc_open\s*\(',
        '`.*`',

        // Path traversal
        '\.\.\/',
        '\.\.\\\\',
        '\/etc\/passwd',
        '\/etc\/shadow',
        '\/proc\/self',
        'C:\\\\\.*',

        // PHP code injection
        'php:\/\/',
        '<\?php',
        '<\?=',
        '\?>',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Clean all input
        $this->sanitizeRequest($request);

        // Check for threats in URL, headers, and body
        if ($this->detectThreat($request)) {
            return $this->logAndBlock($request);
        }

        $response = $next($request);

        // Add security headers
        return $this->addSecurityHeaders($response);
    }

    private function sanitizeRequest(Request $request): void
    {
        // Clean input data
        $input = $request->all();
        $this->cleanArray($input);
        $request->merge($input);

        // Clean query parameters
        $query = $request->query();
        $this->cleanArray($query);

        // Note: Laravel में query parameters directly modify नहीं कर सकते
        // पर input merge करने से काम चल जाएगा
    }

    private function cleanArray(array &$array): void
    {
        foreach ($array as &$value) {
            if (is_string($value)) {
                $value = $this->cleanString($value);
            } elseif (is_array($value)) {
                $this->cleanArray($value);
            }
        }
    }

    private function cleanString(string $value): string
    {
        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Remove specific malicious script - FIXED regex
        $value = preg_replace('/<script\s+[^>]*src=["\']https?:\/\/carto\.run\.place\/[^"\']*["\'][^>]*>.*?<\/script>/is', '', $value);

        // Remove all script tags
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);

        // Remove event handlers
        $value = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        $value = preg_replace('/on\w+\s*=\s*[^ >]+/i', '', $value);

        // Remove javascript: protocol
        $value = preg_replace('/javascript:/i', '', $value);

        // Remove specific domain
        $value = str_replace('carto.run.place', '[BLOCKED]', $value);
        $value = str_replace('https://carto.run.place/index.js?a', '[BLOCKED]', $value);

        // For non-JSON responses, escape HTML
        if (!request()->expectsJson() && !Str::contains(request()->header('Accept') ?? '', 'json')) {
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        }

        return $value;
    }

    private function detectThreat(Request $request): bool
    {
        $checkData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent() ?? '',
            'referer' => $request->header('referer') ?? '',
            'ip' => $request->ip(),
        ];

        // Check all request data
        $allData = array_merge(
            $request->all(),
            $checkData,
            $request->headers->all()
        );

        foreach ($allData as $key => $data) {
            if (is_string($data) && $this->containsMaliciousPattern($data)) {
                Log::warning('Malicious pattern detected in request', [
                    'key' => $key,
                    'data_preview' => substr($data, 0, 100),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return true;
            }

            // Also check arrays recursively
            if (is_array($data)) {
                if ($this->checkArrayRecursive($data)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function checkArrayRecursive(array $array): bool
    {
        foreach ($array as $value) {
            if (is_string($value) && $this->containsMaliciousPattern($value)) {
                return true;
            }

            if (is_array($value) && $this->checkArrayRecursive($value)) {
                return true;
            }
        }

        return false;
    }

    private function containsMaliciousPattern(string $content): bool
    {
        foreach ($this->maliciousPatterns as $pattern) {
            // FIXED: Proper regex delimiter handling
            try {
                // Check if pattern already has delimiters
                if (preg_match('/^\/.*\/[imsxuADU]*$/', $pattern)) {
                    // Pattern already has delimiters
                    $regex = $pattern;
                } else {
                    // Add delimiters and make case-insensitive
                    $regex = '/' . $pattern . '/i';
                }

                if (@preg_match($regex, $content) === 1) {
                    Log::debug('Pattern matched', [
                        'pattern' => $pattern,
                        'regex' => $regex,
                        'content_preview' => substr($content, 0, 50)
                    ]);
                    return true;
                }
            } catch (\Exception $e) {
                Log::error('Regex error in security middleware', [
                    'pattern' => $pattern,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
        return false;
    }

    private function logAndBlock(Request $request)
    {
        // Log to database
        try {
            if (Schema::hasTable('security_logs')) {
                DB::table('security_logs')->insert([
                    'event_type' => 'malicious_input',
                    'details' => json_encode([
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'input_safe' => $this->getSafeInput($request),
                    ], JSON_PRETTY_PRINT),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'endpoint' => $request->path(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log security threat: ' . $e->getMessage());
        }

        // Optional: Send email alert
        $this->sendSecurityAlert($request);

        // Send response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Security violation detected',
                'status_code' => 403
            ], 403);
        } else {
            abort(403, 'Security violation detected');
        }
    }

    private function getSafeInput(Request $request): array
    {
        $input = $request->except(['password', 'token', 'api_key', 'secret']);

        // Sanitize sensitive data
        foreach ($input as $key => &$value) {
            if (is_string($value) && stripos($key, 'pass') !== false) {
                $value = '***REDACTED***';
            }
        }

        return $input;
    }

    private function sendSecurityAlert(Request $request): void
    {
        $alertEmail = config('app.security_alert_email');

        if ($alertEmail && filter_var($alertEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::raw($this->getAlertMessage($request), function ($message) use ($alertEmail) {
                    $message->to($alertEmail)
                        ->subject('🚨 Security Alert - Malicious Input Attempted');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send security alert email: ' . $e->getMessage());
            }
        }
    }

    private function getAlertMessage(Request $request): string
    {
        return sprintf(
            "Security Threat Detected!\n\n" .
                "Time: %s\n" .
                "IP: %s\n" .
                "URL: %s\n" .
                "Method: %s\n" .
                "User Agent: %s\n" .
                "Endpoint: %s\n\n" .
                "Action Required: Review security logs and consider blocking this IP if repeated.",
            now()->toDateTimeString(),
            $request->ip(),
            $request->fullUrl(),
            $request->method(),
            $request->userAgent() ?? 'Unknown',
            $request->path()
        );
    }

    private function addSecurityHeaders($response)
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Strict CSP
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data: https:",
            "font-src 'self' https://fonts.gstatic.com",
            "connect-src 'self'",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
