<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\User;

class UserController extends Controller
{

    public function dropzoneExample()
    {
        return view('dropzone_view');
    }

    public function dropzoneStore(Request $request)
    {
        $image = $request->file('file');

        $imageName = time().'.'.$image->extension();
        $image->move(public_path('images'),$imageName);
   
        return response()->json(['success'=>$imageName]);
    }
    
    public function getFiles()
    {
	$targetDir = public_path('APE/');
	$fileList = [];
  
	$dir = $targetDir;
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if($file != '' && $file != '.' && $file != '..'){
					$file_path = $targetDir.$file;
					if(!is_dir($file_path)){
						$size = filesize($file_path);
						$fileList[] = ['name'=>$file, 'size'=>$size, 'path'=>$file_path];
					}
				}
			}
		      closedir($dh);
		}
	}
	return json_encode($fileList);
    }
}