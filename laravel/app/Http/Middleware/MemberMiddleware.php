<?php

namespace App\Http\Middleware;

use App\Models\BoardMember;
use App\Models\LoginToken;
use Closure;
use Illuminate\Http\Request;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userId = LoginToken::where('token', $request->token)->first();
        
        if (!$request->token) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }
        
        if (!$userId) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }
        
        $boardId = $request->board ?: $request->card->list->board->id;
        $cek = BoardMember::where('board_id', $boardId)->where('user_id', $userId->user_id)->first();
        
        if (!$cek) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        return $next($request);
    }
}
