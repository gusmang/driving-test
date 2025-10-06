<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthDocs
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('basic_auth_authenticated') === true) {
            return $next($request);
        }


        $USERNAME = env('DOCS_USER', 'goesmang'); // default user
        $PASSWORD = env('DOCS_PASS', 'pass123'); // default pass

        if ($request->getUser() !== $USERNAME || $request->getPassword() !== $PASSWORD) {
            $headers = ['WWW-Authenticate' => 'Basic'];
            return response('Unauthorized', 401, $headers);
        }
        // if ($request->header('Authorization')) {
        //     // ... (Logic Kredensial BENAR)

        //     if ($request->getUser() === $USERNAME && $request->getPassword() === $PASSWORD) {
        //         // ... Loloskan
        //         // ...
        //     }

        //     // Kredensial SALAH/Gagal: Kirim 403 (Memutus Loop)
        //     $host = request()->getHost();
        //     $port = request()->getPort() ? ':' . request()->getPort() : '';
        //     $retryLink = 'http://fail:retry@' . $host . $port . '/docs/api';

        //     // Mengembalikan respons HTML yang berisi link trik URL
        //     return response('
        //         <h1>Invalid Credentials</h1>
        //         <p>Kredensial yang Anda masukkan salah.</p>
        //         <p>Klik <a href="' . $retryLink . '">tautan ini</a> untuk mencoba lagi. (Ini akan memunculkan pop-up login)</p>
        //     ', 403);
        // }

        $request->session()->put('basic_auth_authenticated', true);
        return $next($request);
    }

    // public function handle(Request $request, Closure $next): Response
    // {
    //     $USERNAME = env('DOCS_USER', 'goesmang');
    //     $PASSWORD = env('DOCS_PASS', 'pass123');
    //     $realm = 'Scramble Docs';

    //     // 1. KONDISI KELUAR: Lolos jika Sesi Sudah Terotentikasi
    //     if ($request->session()->get('basic_auth_authenticated') === true) {
    //         return $next($request);
    //     }

    //     // 2. KONDISI: Request memiliki Header Otorisasi (User mencoba login)
    //     if ($request->header('Authorization')) {

    //         // 2a. Kredensial BENAR: Lolos dan Set Session
    //         if ($request->getUser() === $USERNAME && $request->getPassword() === $PASSWORD) {
    //             $request->session()->put('basic_auth_authenticated', true);
    //             $request->session()->put('basic_auth_user', $USERNAME);
    //             return $next($request);
    //         }

    //         // 2b. Kredensial SALAH: Kirim 403 (Memutus Loop) dan Tautan Retry
    //         $host = $request->getHost();
    //         $port = $request->getPort() ? ':' . $request->getPort() : '';
    //         $retryUrl = 'http://fail:retry@' . $host . $port . '/docs/api';

    //         // Mengembalikan 403 agar browser tidak terjebak loop 401
    //         return response('
    //         <h1>Invalid Credentials</h1>
    //         <p>Kredensial yang Anda masukkan salah.</p>
    //         <p>Untuk mencoba lagi, silakan <strong>salin URL ini</strong> dan tempel di address bar browser Anda:</p>
    //         <input type="text" value="' . $retryUrl . '" style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; background-color: #eee;">
    //         <p style="margin-top: 15px;"><strong>Setelah di-paste, browser akan memunculkan pop-up login baru.</strong></p>
    //     ', 403);
    //     }

    //     // 3. KONDISI KELUAR: Tidak ada Header Otorisasi (Request Awal/Logout)
    //     //    Minta Otentikasi (Ini yang MEMUNCULKAN POP-UP)
    //     return response('Unauthorized', 401, ['WWW-Authenticate' => "Basic realm=\"$realm\""]);
    // }
}
