<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Team;
use App\Models\Player;
use App\Models\MatchResultHistory;

class PlayerController extends Controller
{
    public function index()
    {
        //get all players
        $players = Player::with(['team'])->latest()->paginate(5);

        return new BasicResource(true, 'Data fetched', $players);
    }

    public function store(Request $request)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'team_id' => 'required',
            'name' => 'required|max:191',
            'height' => 'required',
            'weight' => 'required',
            'position' => 'required',
            'number' => 'required|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if player number is registered in the same team
        if(Player::where(['team_id' => $request->team_id, 'number' => $request->number])->exists()){
            return response()->json("Player number has been registered, change the player number", 422);
        }

        //check if team available
        if(!Team::where('status', "1")->where('id', $request->team_id)->exists()){
            return response()->json("Team that you choose doesn't exists", 422);
        }
        //validation - end

        //player create - start
        $dataInput = $request->all();

        $player = Player::create($dataInput);
        //player create - end

        return new BasicResource(true, 'Data created', $player);
    }

    public function show($id)
    {
        //find player - start
        $player = Player::with(['team'])->find($id);

        if(!$player){
            return response()->json("Data not found", 404);
        }
        //find player - end

        return new BasicResource(true, 'Data fetched', $player);
    }

    public function update(Request $request, $id)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'team_id' => 'required',
            'name' => 'required|max:191',
            'height' => 'required',
            'weight' => 'required',
            'position' => 'required',
            'number' => 'required|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if player number is registered in the same team
        if(Player::where(['team_id' => $request->team_id, 'number' => $request->number])->where('id', '<>', $id)->exists()){
            return response()->json("Player number has been registered, change the player number", 422);
        }

        //check if team available
        if(!Team::where('status', "1")->where('id', $request->team_id)->exists()){
            return response()->json("Team that you choose doesn't exists", 422);
        }
        //validation - end

        //find player - start
        $player = Player::find($id);

        if(!$player){
            return response()->json("Data not found", 404);
        }
        //find player - end

        //player update - start
        $dataUpdate = $request->all();
        $player->update($dataUpdate);
        //player update - start

        return new BasicResource(true, 'Data updated', $player);
    }
    
    public function destroy($id)
    {

        //find team - start
        $player = Team::find($id);

        if(!$player){
            return response()->json("Data not found", 404);
        }
        //find team - end

        //usage validation - start
        if(MatchResultHistory::where('player_id', $id)->orWhere('sec_player_id', $id)->exists()){
            return response()->json("Player has match history, cannot be deleted", 422);
        }
        //usage validation - end

        //delete player
        $player->delete();

        return new BasicResource(true, 'Data deleted', null);
    }
}
