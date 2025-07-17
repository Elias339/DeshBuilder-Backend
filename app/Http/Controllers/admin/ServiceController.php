<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('created_at', 'DESC')->get();
        return response()->json([
           'status'=> true,
            'data'=>$services
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'title'=>'required',
           'slug'=>'required | unique:services,slug',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

        $model = new Service();
        $model->title = $request->title;
        $model->slug = Str::slug($request->slug);
        $model->short_desc = $request->short_desc;
        $model->text_content = $request->text_content;
        $model->status = $request->status;
        $model->save();

        //save Temp image here
        if ($request->imageId > 0) {
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null){

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now').$model->id.'.'.$ext;

                // create small image here using intervention library
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/services/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);

                $image->coverDown(500, 600);
                $image->save($destPath);

                // create large image here using intervention library
                $destPath = public_path('uploads/services/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $model->image = $fileName;
                $model->save();
            }
        }

        return response()->json([
            'status'=>true,
            'message'=> "Service added successfully"
        ]);
    }

    public function show($id)
    {
       $service = Service::find($id);
        if ($service == null){
            return response()->json([
                'status'=>false,
                'message'=>'Service not found'
            ]);
        }

        return response()->json([
            'status'=>true,
            'data'=>$service
        ]);
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
            ]);
        }

        $validator = Validator::make($request->all(),[
            'title'=>'required',
            'slug'=>'required | unique:services,slug,'.$id.',id',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

        $service->title = $request->title;
        $service->slug = Str::slug($request->slug);
        $service->short_desc = $request->short_desc;
        $service->text_content = $request->text_content;
        $service->status = $request->status;
        $service->save();

        //save Temp image here
        if ($request->imageId > 0) {
            $oldImage = $service->image;
            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null){

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $fileName = strtotime('now').$service->id.'.'.$ext;

                // create small image here using intervention library
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/services/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);

                $image->coverDown(500, 600);
                $image->save($destPath);

                // create large image here using intervention library
                $destPath = public_path('uploads/services/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $service->image = $fileName;
                $service->save();
                if ($oldImage != ''){
                    File::delete(public_path('uploads/services/small/'.$oldImage));
                    File::delete(public_path('uploads/services/large/'.$oldImage));
                }

            }
        }

        return response()->json([
            'status'=>true,
            'message'=> "Service updated successfully"
        ]);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        if ($service == null){
            return response()->json([
                'status'=>false,
                'message'=>'Service not found'
            ]);
        }

        $service->delete();

        return response()->json([
            'status'=>true,
            'message'=>'Service delete successfully'
        ]);
    }
}

