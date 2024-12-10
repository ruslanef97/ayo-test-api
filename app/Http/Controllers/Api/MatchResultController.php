<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;

use App\Models\MatchResultHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\GameMatch;
use App\Models\MatchResult;

class MatchResultController extends Controller
{
    public function index()
    {
        //get all matchs
        $matchResults = MatchResult::with(['match', 'match.home', 'match.away', 'history', 'history.player', 'history.sec_player'])->latest()->paginate(5);

        return new BasicResource(true, 'Data fetched', $matchResults);
    }

    public function store(Request $request)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'match_id' => 'required',
            'home_score' => 'required',
            'away_score' => 'required',
            'history' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if history is array
        if(!is_array($request->history) || (is_array($request->history) && count($request->history) < 1)){
            return response()->json("Please send at least one history data", 422);
        }

        //check if match has result
        if(MatchResult::where('match_id', $request->match_id)->exists()){
            return response()->json("Match already has result data, delete it first to re-create", 422);
        }

        //check if match not exists
        if(!GameMatch::where('id', $request->match_id)->exists()){
            return response()->json("Match is not exists", 422);
        }
        //validation - end

        //match result create - start
        try {
            DB::beginTransaction();

            $dataInput = $request->all();
            unset($dataInput['history']);

            if(intval($dataInput['home_score']) > intval($dataInput['away_score'])){
                $dataInput['result'] = "1";
            }elseif(intval($dataInput['home_score']) < intval($dataInput['away_score'])){
                $dataInput['result'] = "2";
            }else{
                $dataInput['result'] = "0";
            }

            $matchResult = MatchResult::create($dataInput);
            
            foreach ($request->history as $kh => $vh) {
                $validateDetail = Validator::make($vh, [
                    'type' => 'required',
                    'half' => 'required',
                    'time' => 'required',
                    'player_id' => 'required'
                ]);

                if ($validateDetail->fails()) {
                    DB::rollBack();

                    return response()->json($validateDetail->errors(), 422);
                }

                $vh['match_result_id'] = $matchResult->id;

                MatchResultHistory::create($vh);
            }

            DB::commit();

            return new BasicResource(true, 'Data created', $matchResult);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage(), 500);
        }
        //match result create - end
    }

    public function show($id)
    {
        //find match result - start
        $matchResult = MatchResult::with(['match', 'match.home', 'match.away', 'history', 'history.player', 'history.sec_player'])->find($id);

        if(!$matchResult){
            return response()->json("Data not found", 404);
        }
        //find match result - end

        return new BasicResource(true, 'Data fetched', $matchResult);
    }
    
    public function destroy($id)
    {

        //find match result - start
        $matchResult = MatchResult::find($id);

        if(!$matchResult){
            return response()->json("Data not found", 404);
        }
        //find match result - end

        //delete match
        $matchResult->delete();

        return new BasicResource(true, 'Data deleted', null);
    }
}
