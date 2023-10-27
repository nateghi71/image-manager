<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class imageController extends Controller
{
    public function index()
    {
        $images = Image::all();
        return view('index', ['images'=>$images]);
    }

    public function store(Request $request)
    {

//        dd($request);
        $request->validate([
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' ,
        ]);
//        dd($request->img);

        $fileName =time().'.'. $request->img->getClientOriginalName();
        $request->img->move(public_path(env('IMAGE_UPLOADED_PATH')),$fileName);
        $image =  Image::create(['image'=>$fileName,'description'=> '']);
        return ['name'=>$fileName , 'id'=>$image->id];
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'=> 'required|integer',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' ,
            'description' => 'nullable|string' ,
        ]);
        $image = Image::find($request->id);
        if($request->img){
            if(File::exists(public_path('upload/images/').$image->image)){
                File::delete(public_path('upload/images/').$image->image);
            }
            $fileName =time().'.'. $request->img->getClientOriginalName();
            $request->img->move(public_path(env('IMAGE_UPLOADED_PATH')),$fileName);

            if($request->description){
                $image->update(['image'=>$fileName , 'description' => $request->description]);
            }
            else{
                $image->update(['image'=>$fileName]);
            }
        }
        elseif ($request->description){
            $image->update(['description' => $request->description]);
        }

        return ['id'=>$image->id , 'image'=>$image->image, 'description' => $image->description];
    }

    public function destroy($id)
    {
        $image = Image::find($id);
        if(File::exists(public_path('upload/images/').$image->image)){
            File::delete(public_path('upload/images/').$image->image);
        }

        Image::destroy($id);
//
        return ['id' => $id , 'images' => Image::all()];
    }
}
