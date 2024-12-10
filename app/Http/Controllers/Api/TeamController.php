<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\Team;
use App\Models\City;
use App\Models\Player;
use App\Models\GameMatch;

class TeamController extends Controller
{
    public function index()
    {
        //get all teams
        $teams = Team::with(['city'])->latest()->paginate(5);

        return new BasicResource(true, 'Data fetched', $teams);
    }

    public function store(Request $request)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'year_of_est' => 'required|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if city available
        if(isset($request->city_id) && !empty($request->city_id) && !City::where('status', "1")->where('id', $request->city_id)->exists()){
            return response()->json("City that you choose doesn't exists", 422);
        }
        //validation - end

        //logo image upload - start
        $logo = $request->file('logo');
        $logo->storeAs('public/teams', $logo->hashName());
        //logo image upload - end

        //team create - start
        $dataInput = $request->all();
        $dataInput['logo'] = $logo->hashName();

        $team = Team::create($dataInput);
        //team create - end

        return new BasicResource(true, 'Data created', $team);
    }

    public function show($id)
    {
        //find team - start
        $team = Team::with(['city'])->find($id);

        if(!$team){
            return response()->json("Data not found", 404);
        }
        //find team - end

        return new BasicResource(true, 'Data fetched', $team);
    }

    public function update(Request $request, $id)
    {
        //validation - start
        if ($request->hasFile('logo')) {
            $validatorRules = [
                'name' => 'required|max:191',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'year_of_est' => 'required|max:4',
            ];
        }else{
            $validatorRules = [
                'name' => 'required|max:191',
                'year_of_est' => 'required|max:4',
            ];
        }

        $validator = Validator::make($request->all(), $validatorRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if city available
        if(isset($request->city_id) && !empty($request->city_id) && !City::where('status', "1")->where('id', $request->city_id)->exists()){
            return response()->json("City that you choose doesn't exists", 422);
        }
        //validation - end

        //find team - start
        $team = Team::find($id);

        if(!$team){
            return response()->json("Data not found", 404);
        }
        //find team - end

        //team update - start
        $dataUpdate = $request->all();
        if ($request->hasFile('logo')) {

            //upload image and update if exist
            $logo = $request->file('logo');
            $logo->storeAs('public/teams', $logo->hashName());
            $dataUpdate['logo'] = $logo->hashName();

            //delete old image
            Storage::delete('public/teams/' . basename($team->logo));

            //update team
            $team->update($dataUpdate);
        } else {

            //update without image
            unset($dataUpdate['logo']);
            $team->update($dataUpdate);
        }
        //team update - start

        return new BasicResource(true, 'Data updated', $team);
    }
    
    public function destroy($id)
    {

        //find team - start
        $team = Team::find($id);

        if(!$team){
            return response()->json("Data not found", 404);
        }
        //find team - end

        //usage validation - start
        if(GameMatch::where('home', $id)->orWhere('away', $id)->exists()){
            return response()->json("Team has match schedule, cannot be deleted", 422);
        }

        if(Player::where('team_id', $id)->exists()){
            return response()->json("Team has player data, cannot be deleted", 422);
        }
        //usage validation - end

        //delete image
        Storage::delete('public/teams/'.basename($team->logo));

        //delete team
        $team->delete();

        return new BasicResource(true, 'Data deleted', null);
    }
}
