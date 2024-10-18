<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HeaderType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $headerType = $this->determineHeaderType($request);
        $request->merge(['headerType' => $headerType]);
        return $next($request);
    }

    public function determineHeaderType(Request $request): string
    {
        /*
        * ロゴのみの/login, /registerはfortifyでルートが
        * 定義されているのでblade内（login.blade.php,
        * register.blade.php）で直接値を渡す
        */
        if (auth()->check()) {
            // 認証済の場合、ログアウトボタンを表示
            return 'logOut';
        } else {
            // 未認証の場合、ログインボタンを表示
            return 'logIn';
        }
    }
}
