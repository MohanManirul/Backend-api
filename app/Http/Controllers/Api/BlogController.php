<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::orderBy('id', 'desc')->get();
        return send_response('Success' , BlogResource::collection($blogs));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|min:10|max:255',
            'description' => 'required|min:20',
        ]);

       if($validator->fails()) return send_error('Validation error', $validator->errors() , 422);


       try{
            $blog = Blog::create([
                "title"  => $request->title,
                "description"  => $request->description
            ]);

            return send_response('blog Create success !', new BlogResource($blog) );


        } catch( Exception $e){
            return send_error($e->getMessage(),$e->getCode());

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $blog = Blog::find($id);
       if($blog){
        return send_response('Success !', new BlogResource($blog));
       }else{
        return send_error('blog not found !');
       }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|min:10|max:255',
            'description' => 'required|min:20',
        ]);

       if($validator->fails()) return send_error('Validation error', $validator->errors() , 422);


       try{
            $blog->title  = $request->title;
            $blog->description  = $request->description;
            $blog->save();
            return send_response('blog updated success !', new BlogResource($blog) );


        } catch( Exception $e){
            return send_error($e->getMessage(),$e->getCode());

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $blog = Blog::find($id);
            if($blog){
                    $blog->delete();
                }
            return send_response('Blog Deteled Successfully...', []);
        }catch(Exception $e){
            return send_error('Something Wrong !', $e->getCode() );
        }
    }
}
