<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BoardListController extends Controller
{
    public function store(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $order = BoardList::where('board_id', $id)->max('order') + 1;

        BoardList::create([
            'name' => $request->name,
            'board_id' => $id,
            'order' => $order
        ]);

        return response()->json(['message' => 'create list success'], 200);
    }

    public function update(Request $request, $board, $list)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $boardList = BoardList::findOrFail($list);
        $boardList->update([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'update list success'], 200);
    }

    public function destroy($board, $list)
    {
        $card = Card::where('list_id', $list)->delete();
        $list = BoardList::findOrFail($list)->delete();
        return response()->json(['message' => 'delete list success'], 200);
    }

    public function right($board, $list) {
        $list = BoardList::where('board_id', $board)->where('id', $list)->first();
        $next = BoardList::where('board_id', $board)->where('order', '>', $list->order)->first();
        if (!$next) return response()->json(['message' => 'move success'], 200);

        $nowOrder = $list->order;
        $nextOrder = $next->order;

        $list->update(['order' => $nextOrder]);
        $next->update(['order' => $nowOrder]);

        return response()->json(['message' => 'move success'], 200);
    }

    public function left($board, $list) {
        $list = BoardList::where('board_id', $board)->where('id', $list)->first();
        $prev = BoardList::where('board_id', $board)->where('order', '<', $list->order)->first();
        if (!$prev) return response()->json(['message' => 'move success'], 200);

        $nowOrder = $list->order;
        $prevOrder = $prev->order;

        $list->update(['order' => $prevOrder]);
        $prev->update(['order' => $nowOrder]);

        return response()->json(['message' => 'move success'], 200);
    }
}
