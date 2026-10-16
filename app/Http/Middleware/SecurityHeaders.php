<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan dasar untuk setiap respons web.
 *
 * Sengaja belum memakai CSP script/style penuh: halaman memakai <script> inline
 * dan Alpine.js, sehingga CSP ketat akan mematahkan tampilan. Yang dipasang di
 * sini hanya header yang aman tanpa perlu menyesuaikan view.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=()',
            // Cegah halaman di-embed di situs lain (clickjacking)
            'Content-Security-Policy' => "frame-ancestors 'self'",
        ];

        // HSTS hanya bermakna (dan hanya aman) di koneksi HTTPS
        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $nama => $nilai) {
            if (! $response->headers->has($nama)) {
                $response->headers->set($nama, $nilai);
            }
        }

        return $response;
    }
}
