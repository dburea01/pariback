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
        $bettor = Bettor::where('token', $request->route('token'))->first();
        abort_if(is_null($bettor), 404);

        $bet = Bet::find($bettor->bet_id);

        abort_if($bet->status !== 'INPROGRESS', 403, 'Bet not in progress.');
        abort_if($request->route('bet')->id !== $bettor->bet_id, 403, 'Bet and token not coherent.');
        $request->merge([
            'bettor' => $bettor,
            'user_id' => $bettor->user_id,
        ]);

        return $next($request);
    }
}
