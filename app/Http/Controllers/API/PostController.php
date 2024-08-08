<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use App\http\Controllers\APi\BaseController as baseController;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['post'] = Post::all();
        
        return $this->serndResponse($data,'All Post Data');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'description' => 'required|min:20',
                'image' => 'required|mimes:png,jpeg,jpg,gif'
            ]
        );
        
      
        if($validator->fails()){
            // return response()->json([
            //     'status'=>false,
            //     'message'=>'Validation Error',
            //     'error'=>$validator->errors()->all()
            // ],401);
            return $this->sendError('Validation Error',$validator->errors()->all(),401);
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time().'.'.$ext;
        $img->move(public_path().'/uploads',$imageName);        
        
        $post = Post::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName
            // 'password'=>bcrypt($request->password)
        ]); 
  

        // return response()->json([
        //     'status'=>true,
        //     'message'=>'One Post Create Successfully',
        //     'user'=>$post,           
        // ],200);
        return $this->serndResponse($post,'One Post Create Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data['post'] = Post::select(
            'id',
            'title',
            'description',
            'image'
        )->where(['id'=>$id])->get();

        // return response()->json([
        //     'status'=>true,
        //     'message'=>'Your Post get by Id ',
        //     'user'=>$data,           
        // ],200);
        return $this->serndResponse($data,'Your Post get by Id ');
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'description' => 'required|min:20',
                'image' => 'required|mimes:png,jpeg,jpg,gif'
            ]
        );
        
      
        if($validator->fails()){
            // return response()->json([
            //     'status'=>false,
            //     'message'=>'Validation Error',
            //     'error'=>$validator->errors()->all()
            // ],401);
            return $this->sendError('Validation Error',$validator->errors()->all(),401);
        }
        
        $postImageOld = Post::select('id','image')->where(['id'=>$id])->get();
        // $postImage = $postImageOld[0]['image'];
        if($request->image != ''){
            $path = public_path().'/uploads';

            if($postImageOld[0]['image'] != '' && $postImageOld[0]['image'] != null){
                $old_file = $path.$postImageOld[0]['image'];
                    
                if(file_exists($old_file)){
                    unlink($old_file);
                }
            }
            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $img->move(public_path().'/uploads',$imageName);        
        }else{
            $imageName = $postImageOld[0]['image'];

        }
        
        
        $post = Post::where(['id'=>$id])->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName
            // 'password'=>bcrypt($request->password)
        ]);        

        // return response()->json([
        //     'status'=>true,
        //     'message'=>'Post Update Successfully',
        //     'post'=>$post,           
        // ],200);
        return $this->serndResponse($post,'Post Update Successfully');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
         $imagePath = Post::select('image')->where('id',$id)->get();
         
         $filePath = public_path().'/uploads/'.$imagePath[0]['image'];
         
         unlink($filePath);

        $post = Post::where('id',$id)->delete();

        // return response()->json([
        //     'status'=>true,
        //     'message'=>'Your post has been removed.',
        //     'post'=>$post,           
        // ],200);
        return $this->serndResponse($post,'Your post has been removed.');
    }
}
