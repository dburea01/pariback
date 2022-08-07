<?php
namespace App\Http\Middleware;

use App\Models\Bet;
use App\Models\Bettor;
use Closure;
use Illuminate\Http\Request;

class EnsureBetIsInProgress
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
        $bettor = Bettor::where('token', $request->route('token'))
        ->where('bet_id', $request->route('bet')->id)
        ->with('bet')
        ->firstOrFail();

        abort_if($bettor->bet->status !== 'INPROGRESS', 403, 'Bet not in progress.');

        $request->merge([
            'bettor' => $bettor,
            'user_id' => $bettor->user_id,
        ]);

        return $next($request);
    }
}
