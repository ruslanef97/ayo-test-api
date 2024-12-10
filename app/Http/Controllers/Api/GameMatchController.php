<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\GameMatch;
use App\Models\MatchResult;

class GameMatchController extends Controller
{
    public function index()
    {
        //get all matchs
        $matches = GameMatch::with(['home', 'away'])->latest()->paginate(5);

        return new BasicResource(true, 'Data fetched', $matches);
    }

    public function store(Request $request)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:"Y-m-d H:i"',
            'duration' => 'required',
            'home_team' => 'required',
            'away_team' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if team has schedule on the same day
        if(GameMatch::whereRaw('(home_team = '.$request->home_team.' OR home_team = '.$request->away_team.')')->whereRaw('(away_team = '.$request->home_team.' OR away_team = '.$request->away_team.')')->whereDate('date', date("Y-m-d", strtotime($request->date)))->exists()){
            return response()->json("Team has match on selected date, team only can play once a day", 422);
        }
        //validation - end

        //match create - start
        $dataInput = $request->all();

        $match = GameMatch::create($dataInput);
        //match create - end

        return new BasicResource(true, 'Data created', $match);
    }

    public function show($id)
    {
        //find match - start
        $match = GameMatch::with(['home', 'away'])->find($id);

        if(!$match){
            return response()->json("Data not found", 404);
        }
        //find match - end

        return new BasicResource(true, 'Data fetched', $match);
    }

    public function update(Request $request, $id)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:"Y-m-d H:i"',
            'duration' => 'required',
            'home_team' => 'required',
            'away_team' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if team has schedule on the same day
        if(GameMatch::where('id', '<>', $id)->whereRaw('(home_team = '.$request->home_team.' OR home_team = '.$request->away_team.')')->whereRaw('(away_team = '.$request->home_team.' OR away_team = '.$request->away_team.')')->whereDate('date', date("Y-m-d", strtotime($request->date)))->exists()){
            return response()->json("Team has match on selected date, team only can play once a day", 422);
        }
        //validation - end

        //find match - start
        $match = GameMatch::find($id);

        if(!$match){
            return response()->json("Data not found", 404);
        }
        //find match - end

        //match update - start
        $dataUpdate = $request->all();
        $match->update($dataUpdate);
        //match update - start

        return new BasicResource(true, 'Data updated', $match);
    }
    
    public function destroy($id)
    {

        //find team - start
        $match = GameMatch::find($id);

        if(!$match){
            return response()->json("Data not found", 404);
        }
        //find team - end

        //usage validation - start
        if(MatchResult::where('match_id', $id)->orWhere('sec_match_id', $id)->exists()){
            return response()->json("Match has history data, cannot be deleted", 422);
        }
        //usage validation - end

        //delete match
        $match->delete();

        return new BasicResource(true, 'Data deleted', null);
    }
}
