<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use function PHPUnit\Framework\status;

class ServiceController extends Controller
{
    public function index(){
        $services = Service::where('status',1)->orderBy('created_at','DESC')->get();
        return response()->json([
            'status'=> true,
            'data'=>$services
        ]);
    }

    public function latestServices(Request $request){
        $services = Service::where('status',1)
            ->take($request->get('limit'))
            ->orderBy('created_at','DESC')->get();
        return response()->json([
            'status'=> true,
            'data'=>$services
        ]);
    }


}
