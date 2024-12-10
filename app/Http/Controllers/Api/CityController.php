<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\City;
use App\Models\Team;

class CityController extends Controller
{
    public function index()
    {
        //get all city
        $cities = City::latest()->paginate(5);

        return new BasicResource(true, 'Data fetched', $cities);
    }

    public function store(Request $request)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //validation - end

        //city create - start
        $dataInput = $request->all();

        $city = City::create($dataInput);
        //city create - end

        return new BasicResource(true, 'Data created', $city);
    }

    public function show($id)
    {
        //find city - start
        $city = City::find($id);

        if(!$city){
            return response()->json("Data not found", 404);
        }
        //find city - end

        return new BasicResource(true, 'Data fetched', $city);
    }

    public function update(Request $request, $id)
    {
        //validation - start
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //validation - end

        //find city - start
        $city = City::find($id);

        if(!$city){
            return response()->json("Data not found", 404);
        }
        //find city - end

        //city update - start
        $dataUpdate = $request->all();
        $city->update($dataUpdate);
        //city update - start

        return new BasicResource(true, 'Data updated', $city);
    }
    
    public function destroy($id)
    {

        //find city - start
        $city = City::find($id);

        if(!$city){
            return response()->json("Data not found", 404);
        }
        //find city - end

        //usage validation - start
        if(Team::where('city_id', $id)->exists()){
            return response()->json("City has team data, cannot be deleted", 422);
        }
        //usage validation - end

        //delete city
        $city->delete();

        return new BasicResource(true, 'Data deleted', null);
    }
}
