<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('created_at','DESC')->get();
        return response()->json([
            'status'=> true,
            'data'=>$projects
        ],200);

    }

    public function store(Request $request){
       $validator = Validator::make($request->all(),[
          'title'=>'required|unique:projects,title',
       ]);

       if ($validator->fails())
       {
           return response()->json([
               'status'=>false,
               'errors'=>$validator->errors()
           ],422);
       }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('projects', 'public');
        }

        $project = new Project();
        $project->title = $request->title;
        $project->slug = Str::slug($request->title, '_');
        $project->short_desc = $request->short_desc;
        $project->text_content = $request->text_content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->location = $request->location;
        $project->status = $request->status;
        $project->image = $imagePath;
        $project->save();

        return response()->json([
            'status'=>true,
            'message'=>"Project added successfully."
        ], 200);
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(),[
            'title'=>'required',
            'status'=>'required'
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ],422);
        }

        $project = Project::find($id);

        if ($project == null){
            return response()->json([
                'status'=> false,
                'message'=>'Project not found'
            ],404);
        }

        $imagePath = $project->image;
        if ($request->hasFile('image')) {
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            $imagePath = $request->file('image')->store('projects', 'public');
        }

        $project->title = $request->title;
        $project->slug = Str::slug($request->title, '_');
        $project->short_desc = $request->short_desc;
        $project->text_content = $request->text_content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->location = $request->location;
        $project->status = $request->status;
        $project->image = $imagePath;
        $project->save();

        return response()->json([
            'status'=>true,
            'message'=>"Project updated successfully."
        ]);

    }

    public function destroy($id){
        $project = Project::find($id);
        if ($project == null)
        {
            return response()->json([
                'status'=>false,
                'message'=>"Project not found."
            ],404);
        }
        if ($project->image && Storage::disk('public')->exists($project->image))
        {
            Storage::disk('public')->delete($project->image);
        }
        $project->delete();

        return response()->json([
            'status'=>true,
            'message'=>"Project deleted successfully."
        ],200);
    }

}
