<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades\Crypt;

class FileController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth',['except'=>['getQrCodeFile']]);
    }
	
	public function get($file){
	
	if(Storage::disk('private')->exists($file)){
			
			return Storage::disk('private')->response($file);
			
		}else{
			abort(403);
		}
	}
	
	public function getQrCodeFile(Request $request){
	  
     $encryptedPath = $request->query('encryptedPath');
  
		if (!$request->hasValidSignature()) {
			abort(403, 'Unauthorized access.');
		}

		try {
			$pdfPath = Crypt::decrypt($encryptedPath);
		} catch (DecryptException $e) {
			abort(403, 'Invalid  path.');
		}

		if (Storage::disk('private')->exists($pdfPath)) {
			return response()->download(Storage::disk('private')->path($pdfPath), basename($pdfPath), [
				'Content-Disposition' => 'attachment; filename="' . basename($pdfPath) . '"'
			]);
		} else {
			abort(404, 'File not found.');
		}
	  
   }
}
