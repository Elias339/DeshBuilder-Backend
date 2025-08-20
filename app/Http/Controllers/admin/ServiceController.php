<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('created_at', 'DESC')->get();
        return response()->json([
           'status'=> true,
            'data'=>$services
        ],200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'title'=>'required',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
           'slug' => 'required|unique:services,slug',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ],422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $model = new Service();
        $model->title = $request->title;
        $model->slug = Str::slug($request->slug);
        $model->short_desc = $request->short_desc;
        $model->text_content = $request->text_content;
        $model->status = $request->status;
        $model->image = $imagePath;
        $model->save();

        return response()->json([
            'status'=>true,
            'message'=> "Service added successfully"
        ],200);
    }

    public function show($id)
    {
       $service = Service::find($id);
        if ($service == null){
            return response()->json([
                'status'=>false,
                'message'=>'Service not found'
            ],404);
        }

        return response()->json([
            'status'=>true,
            'data'=>$service
        ],200);
    }

    public function edit(Service $service)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if ($service == null){
            return response()->json([
                'status'=>false,
                'message'=>'Service not found'
            ],404);
        }

        $validator = Validator::make($request->all(),[
            'title'=>'required',
            'slug'=>'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ],422);
        }

        $path = $service->image;
        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $path = $request->file('image')->store('services', 'public');
        }

        $service->title = $request->title;
        $service->slug = Str::slug($request->slug);
        $service->short_desc = $request->short_desc;
        $service->text_content = $request->text_content;
        $service->status = $request->status;
        $service->image = $path;
        $service->save();

        return response()->json([
            'status'=>true,
            'message'=> "Service updated successfully"
        ],200);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        if ($service == null){
            return response()->json([
                'status'=>false,
                'message'=>'Service not found'
            ],404);
        }

        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json([
            'status'=>true,
            'message'=>'Service delete successfully'
        ],200);
    }
}

