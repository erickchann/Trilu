<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\BoardMember;
use App\Models\Card;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{
    public function index(Request $request)
    {
        $user = LoginToken::where('token', $request->token)->first();
        if (!$user || !$request->token) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        $member = BoardMember::where('user_id', $user->user_id)->pluck('board_id');
        $boards = Board::whereIn('id', $member)->get();

        return response()->json($boards, 200);
    }

    public function store(Request $request)
    {
        $user = LoginToken::where('token', $request->token)->first();
        if (!$user || !$request->token) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $board = Board::create([
            'creator_id' => $user->user_id,
            'name' => $request->name
        ]);

        BoardMember::create([
            'board_id' => $board->id,
            'user_id' => $user->user_id
        ]);

        return response()->json(['message' => 'create board success'], 200);
    }

    public function show($id)
    {
        $board = Board::with([
            'members',
            'lists' => function($query) {
                $query->orderBy('order');
            },
            'lists.cards' => function($query) {
                $query->orderBy('order');
            }
        ])->find($id);
        
        return response()->json($board, 200);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }
        
        $board = Board::findOrFail($id);
        $board->update($request->only('name'));

        return response()->json(['message' => 'update board success'], 200);
    }

    public function destroy(Request $request, Board $board)
    {
        $user = LoginToken::where('token', $request->token)->first();
        if ($user->user_id != $board->creator_id || !$request->token) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        $member = BoardMember::where('board_id', $board->id)->delete();
        $lists = BoardList::where('board_id', $board->id)->pluck('id');
        $card = Card::whereIn('list_id', $lists)->delete();
        $list = BoardList::where('board_id', $board->id)->delete();

        $board->delete();

        return response()->json(['message' => 'delete board success'], 200);
    }

    public function add(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            'username' => 'required|exists:users,username'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'user did not exist'], 422);
        }

        $user = User::where('username', $request->username)->first();
        BoardMember::create([
            'board_id' => $id,
            'user_id' => $user->id
        ]);

        return response()->json(['message' => 'add member success'], 200);
    }

    public function remove($board, $user) {
        BoardMember::where([
            'board_id' => $board,
            'user_id' => $user
        ])->delete();

        return response()->json(['message' => 'remove member success'], 200);
    }
}
