<?php

namespace App\Http\Controllers;

use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function store(Request $request, $board, $list)
    {
        $validate = Validator::make($request->all(), [
            'task' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $order = Card::where('list_id', $list)->max('order') + 1;
        Card::create([
            'list_id' => $list,
            'order' => $order,
            'task' => $request->task
        ]);

        return response()->json(['message' => 'create card success'], 200);
    }

    public function update(Request $request, $board, $list, $card)
    {
        $validate = Validator::make($request->all(), [
            'task' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        $card = Card::findOrFail($card);

        $card->update([
            'task' => $request->task
        ]);

        return response()->json(['message' => 'update card success'], 200);
    }

    public function destroy($board, $list, $card)
    {
        $card = Card::findOrFail($card);

        $card->delete();

        return response()->json(['message' => 'delete card success'], 200);
    }

    public function up(Card $card) {
        $up = Card::where('list_id', $card->list_id)->where('order', '<', $card->order)->first();
        if (!$up) return response()->json(['message' => 'move success'], 200);

        $prevOrd = $card->order;
        $newOrd = $up->order;

        $up->update(['order' => $prevOrd]);
        $card->update(['order' => $newOrd]);

        return response()->json(['message' => 'move success'], 200);
    }

    public function down(Card $card) {
        $down = Card::where('list_id', $card->list_id)->where('order', '>', $card->order)->first();
        if (!$down) return response()->json(['message' => 'move success'], 200);

        $prevOrd = $card->order;
        $newOrd = $down->order;

        $down->update(['order' => $prevOrd]);
        $card->update(['order' => $newOrd]);

        return response()->json(['message' => 'move success'], 200);
    }

    public function move(Card $card, BoardList $list) {
        if ($card->list->board->id != $list->board->id) {
            return response()->json(['message' => 'move list invalid'], 422);
        }

        return 'pl';
    }
}
