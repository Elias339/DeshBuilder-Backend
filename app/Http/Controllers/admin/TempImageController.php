<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
           'image'=>'required|mimes:png,jpg,jpeg,gif'
        ]);

        if ($validator->fails()){
            return response()->json([
               'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

        $image = $request->image;

        $ext = $image->getClientOriginalExtension();
        $imageName = strtotime('now').'.'.$ext;

        $model = new TempImage();
        $model->name = $imageName;
        $model->save();

        $image->move(public_path('uploads/temp'),$imageName);

        // create small image here using intervention library
        $sourcePath = public_path('uploads/temp/'.$imageName);
        $destPath = public_path('uploads/temp/thumb/'.$imageName);

        $manager = new ImageManager(Driver::class);
        $image = $manager->read($sourcePath);
        $image->coverDown(600, 360);
        $image->save($destPath);

        return response()->json([
            'status'=>true,
            'data'=>$model,
            'message'=>'Image uploaded successfully'
        ]);



    }
}
