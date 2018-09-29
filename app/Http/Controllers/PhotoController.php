<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo;
use App\User;
use File;

use Auth;

class PhotoController extends Controller
{
   
    public function __construct()
    {
    	$this->middleware('auth:api')
    		->except(['index', 'show']);
    }

    public function index()
    {
    	$photos = Photo::orderBy('created_at', 'desc')
    		->get(['id', 'name', 'image']);
    	return response()
    		->json([
                'photos' => $photos
    		]);
    }

    public function create()
    {
        $form = Photo::form();
    	return response()
    		->json([
    			'form' => $form
    		]);
    }

    public function store(Request $request)
    {
                
        $hauth = str_replace("Bearer ", "", $request->header('Authorization'));      

        //Get signed in user's id
        $user = User::where('api_token', $hauth)->first();

        //if user is not authenticated
        if (!$user)
        {
            return response()
    	        ->json([
    	        'status' => 401,
                'message' => 'Unauthorized'
    	    ]);
        }

        $this->validate($request, [
    		'name' => 'required|max:255',
    		'description' => 'required|max:3000',
    		'image' => 'required|image'
        ]);

    	
    	if(!$request->hasFile('image') && !$request->file('image')->isValid()) {
    		return abort(404, 'Image not uploaded!');
        }
        
    	$filename = $this->getFileName($request->image);
        $request->image->move(base_path('public/images'), $filename);
        
        $photo = new Photo();
        $photo->name = $request->name;
        $photo->description = $request->description;
        
        $photo->user_id = $user->id;
        
        $photo->image = $filename;
        
        $photo->save();
 	
    	return response()
    	    ->json([
    	        'saved' => true,
    	        'id' => $photo->id,
                'message' => 'You have successfully created a photo!'
    	    ]);
    }

    public function bookmark(Request $request)
    {
        $hauth = str_replace("Bearer ", "", $request->header('Authorization'));
        
        //TODO: Check if token is not expired

        //Get signed in user's id
        $user = User::where('api_token', $hauth)->first();
        $photo_id = $request->id;

        //Get photo
        $photo = Photo::find($photo_id);

        //$user->photos()->attach($photo);

        $exists = $photo->users->contains($user->id);

        if(!$exists) 
        {
            $photo->users()->attach($user);

            return response()
    	        ->json([
                'bookmarked' => 'Bookmarked',
                'message' => 'You have bookmarked this image'
    	    
    	    ]);
        }

        $photo->users()->detach($user);
        return response()
    	        ->json([
                'bookmarked' => 'Bookmark This Image',
                'message' => 'You have removed the bookmark from this image'
    	    
    	    ]);

    }

    public function bookmarked_photos(Request $request)
    {
        $hauth = str_replace("Bearer ", "", $request->header('Authorization'));      

        //Get signed in user's id
        $user = User::where('api_token', $hauth)->first();

        return response()
    	        ->json([
                'photos' => $user->images()->get()
            
    	    ]);

    }
    

    private function getFileName($file)
    {
    	return str_random(32).'.'.$file->extension();
    }

    public function show($id, Request $request)
    {

        $hauth = str_replace("Bearer ", "", $request->header('Authorization'));      
            
        $user = User::where('api_token', $hauth)->first();

        if ($user)
        {

            $photo = Photo::with(['user'])
                ->findOrFail($id);

            $exists = $photo->users->contains($user->id);

        
            if($exists == false) 
            {
                return response()
                    ->json([
                    'photo' => $photo,
                    'bookmarked' => 'Bookmark This Image',
                            
                ]);
            }
        
        
            return response()
                ->json([
                    'photo' => $photo,
                    'bookmarked' => 'Bookmarked'
                ]);
        
        }

        $photo = Photo::findOrFail($id);

        return response()
            ->json([
            'photo' => $photo,
            
                    
        ]);

        
        
    }

    public function edit($id, Request $request)
    {
       

        $id = (int)$id;
            
        $form = Photo::findOrFail($id);
        return response()
            ->json([
                'form' => $form
            ]);
    }

    public function update($id, Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:3000',
            'image' => 'image'
        ]);


        $id = (int)$id;
        $photo = Photo::findOrFail($id);
        
        $photo->name = $request->name;
        $photo->description = $request->description;
        // upload image
        if ($request->hasfile('image') && $request->file('image')->isValid()) {
            $filename = $this->getFileName($request->image);
            $request->image->move(base_path('/public/images'), $filename);
            // remove old image
            File::delete(base_path('/public/images/'.$photo->image));
            $photo->image = $filename;
        }
        $photo->save();
        
        return response()
            ->json([
                'saved' => true,
                'id' => $photo->id,
                'message' => 'You have successfully updated a photo!'
            ]);
    }

    
    public function destroy($id)
    {        

        $id = (int)$id;    
        $photo = Photo::findOrFail($id);
        
        // remove image
        File::delete(base_path('/public/images/'.$photo->image));
        $photo->users()->detach();
        $photo->delete();
        return response()
            ->json([
                'deleted' => true
            ]);
    }
}