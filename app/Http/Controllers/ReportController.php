<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{   	
	public function report() {
		// https://matomo.pauldiepold.de/?module=API&method=Live.getLastVisitsDetails&idSite=3&period=day&date=last10&format=JSON&token_auth=w0yt9murl8n1tt2d1y6x3ppat7uk4nz1
		$url = 'https://matomo.pauldiepold.de/?module=API&method=Live.getLastVisitsDetails&idSite=3&period=day&date=last10&format=JSON&token_auth=' . env('MATOMO_TOKEN');
		
		$fetched = file_get_contents($url);
		$content = collect(json_decode($fetched,true));

		$reportData = collect();
		foreach ($content as $action) {
			$action = collect($action);
			$actionData = collect();
		
			$actionData->push($action->get('dimension1'));
			$actionData->push(date("d.m.y",strtotime($action->get('serverDate'))));		
			$actionData->push($action->get('serverTimePrettyFirstAction'));
			$actionData->push($action->get('serverTimePretty'));
			$actionData->push(count($action->get('actionDetails')));
			$actionData->push(collect($action->get('actionDetails')));
			
			$reportData->push($actionData);
		}
		
		return view('report.index', compact('reportData'));
	}
}
