<?php

namespace App\Http\Middleware;

use App\Models\AdminInvoice;
use App\Models\Usaha;
use Closure;
use Illuminate\Http\Request;
use Session;
use Symfony\Component\HttpFoundation\Response;

class Aktif
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = $request->getHost();
        if (str_contains($url, 'siupk.net')) {
            $domain = explode('.', $url)[0];
            if ($domain == 'simak-apps') {
                $domain = 'demo';
            }

            return redirect('https://' . $domain . '.akubumdes.com');
        } else {
            if (Session::get('lokasi')) {
                $invoice = AdminInvoice::where([
                    ['status', 'UNPAID'],
                    ['lokasi', Session::get('lokasi')]
                ])->first();
                Session::put('invoice', $invoice);

                $usaha = Usaha::where('id', Session::get('lokasi'))->first();
                if ($request->is('dashboard')) {
                    return $next($request);
                }

                if ($usaha->masa_aktif <= date('Y-m-d')) {
                    return redirect('/dashboard');
                }
            }
        }

        return $next($request);
    }
}
