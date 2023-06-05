<?php 
namespace App\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Multitenancy\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CSVHelper
{
    
    public static function cleanDuplicates(string $filePath, string $newFile) : string
    {

        $contents = file_get_contents($filePath);

        // Split the contents into an array of lines
        $lines = explode("\n", $contents);

        // Remove duplicate phone numbers
        $uniquePhoneNumbers = array_unique($lines);

        // Remove empty values  
        $filteredPhoneNumbers = array_filter($uniquePhoneNumbers);

        // Save the filtered phone numbers to a new file
        $filteredContents = implode("\n", $filteredPhoneNumbers);

        //$filteredFile = $newPath . $fileInfo['filename'] . '_cleaned.' . $fileInfo['extension'];
        file_put_contents($newFile, $filteredContents);

        return $newFile;
    }

    public static function removeNumbersFromFile(string $leftFile, string $rightFile, string $newFile): void
    {
        $leftContents = file_get_contents($leftFile);
        $rightContents = file_get_contents($rightFile);

        // Split the contents of the left file into an array of numbers
        $numbersToRemove = explode("\n", $leftContents);

        // Remove any numbers found in the left file from the contents of the right file
        foreach ($numbersToRemove as $number) {
            $rightContents = str_replace($number, '', $rightContents);
        }

        // Remove any empty lines
        $rightContents = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $rightContents);


        //$rightContents = str_replace("\n", '', $rightContents);

        // Save the updated contents back to the right file
        file_put_contents($newFile, $rightContents);
    }
}

?>