<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasicResource;

use Illuminate\Http\Request;

use App\Models\GameMatch;
use App\Models\MatchResult;
use App\Models\MatchResultHistory;

class ReportController extends Controller
{
    public function matchResult(Request $request){
        $finalData = [];

        $match = GameMatch::with(['home', 'away', 'match_result'])->get();
        foreach($match as $k => $v){
            $finalData[$k]['date'] = date('d F Y - H:i', strtotime($v->date));
            $finalData[$k]['home_team'] = $v->home->name;
            $finalData[$k]['away_team'] = $v->away->name;

            $totalWinHome = GameMatch::join('match_results', 'match_results.match_id', '=', 'matches.id')
                                    ->where('matches.home_team', $v->home_team)
                                    ->where('match_results.result', '1')
                                    ->where('matches.id', '<=',  $v->id)
                                    ->groupBy('match_results.id')
                                    ->count();

            $totalWinHome2 = GameMatch::join('match_results', 'match_results.match_id', '=', 'matches.id')
                                    ->where('matches.away_team', $v->home_team)
                                    ->where('match_results.result', '2')
                                    ->where('matches.id', '<=',  $v->id)
                                    ->groupBy('match_results.id')
                                    ->count();

            $totalWinAway = GameMatch::join('match_results', 'match_results.match_id', '=', 'matches.id')
                                    ->where('matches.home_team', $v->away_team)
                                    ->where('match_results.result', '1')
                                    ->where('matches.id', '<=',  $v->id)
                                    ->groupBy('match_results.id')
                                    ->count();
            
            $totalWinAway2 = GameMatch::join('match_results', 'match_results.match_id', '=', 'matches.id')
                                    ->where('matches.home_team', $v->away_team)
                                    ->where('match_results.result', '2')
                                    ->where('matches.id', '<=',  $v->id)
                                    ->groupBy('match_results.id')
                                    ->count();

            if(!empty($v->match_result)){
                $scorer = MatchResultHistory::selectRaw('count(*) as total_score, player_id')->with('player')->where('type', '1')->where('match_result_id', $v->match_result->id)->groupBy('player_id')->orderBy('total_score', 'DESC')->first();

                $finalData[$k]['final_score'] = '(H) '.$v->match_result->home_score.' - (A) '.$v->match_result->away_score;
                $finalData[$k]['result'] = $v->match_result->result;
                $finalData[$k]['most_scorer'] = $scorer->player->name;
                $finalData[$k]['home_win_total'] = $totalWinHome + $totalWinHome2;
                $finalData[$k]['away_win_total'] = $totalWinAway + $totalWinAway2;
            }else{
                $finalData[$k]['final_score'] = '-';
                $finalData[$k]['result'] = '-';
                $finalData[$k]['most_scorer'] = '-';
                $finalData[$k]['home_win_total'] = '-';
                $finalData[$k]['away_win_total'] = '-';
            }
            
        }

        return new BasicResource(true, 'Data fetched', $finalData);
    }
        
}
