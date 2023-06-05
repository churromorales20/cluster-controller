<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CSVHelper;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;

class CSVController extends Controller
{
    
    public function cleaner(Request $request)
    {
        $file = $request->file('jids_file');
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = $file->getClientOriginalExtension();
        $newFileName = '/home/cecilio/Documents/Proyectos/REST/phonenumbers/' . $fileName . '_cleaned.' . $fileExtension;
        
        CSVHelper::cleanDuplicates($request->file('jids_file')->path(), $newFileName);
    }

    public function twinsCleaner(Request $request)
    {
        $file = $request->file('jids_file_right');
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = $file->getClientOriginalExtension();
        $newFileName = '/home/cecilio/Documents/Proyectos/REST/phonenumbers/' . $fileName . '_twin_cleaned.' . $fileExtension;
        
        CSVHelper::removeNumbersFromFile($request->file('jids_file_left')->path(), $request->file('jids_file_right')->path(), $newFileName);
    }
}