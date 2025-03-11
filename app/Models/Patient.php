<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
protected $table = 'tbl_patients';
											
    protected $fillable = [
          
     'patient_user_num', 
	 'patient_kind', 
	 'user_num', 
	 'clinic_num', 
	 'referred_by', 
	 'first_name', 
	 'middle_name', 
	 'last_name', 
	 'birthdate', 
	 'sex', 
	 'marital_status', 
	 'ramq', 
	 'addresse', 
	 'appt_nb', 
	 'city', 
	 'state', 
	 'codepostale', 
	 'first_phone', 
	 'cell_phone', 
	 'work_phone', 
	 'email', 
	 'fax', 
	 'receive_mail', 
	 'receive_sms', 
	 'occupation', 
	 'mother_name', 
	 'father_name', 
	 'blood_group', 
	 'doctor_num', 
	 'title', 
	 'ext_lab', 
	 'husband_name', 
	 'passport_nb', 
	 'status',
	 'file_nb'
    ];
	
	 public static function generateFileNb()
    {
        // Get today's date in the format 'yymmdd'
        $dateToday = now()->format('ymd');
        
        // Find the latest file_nb for today's date
        $latestItem = self::where('file_nb', 'like', $dateToday . '%')
                          ->orderBy('file_nb', 'desc')
                          ->first();
                          
        // Determine the next sequence number
        $nextSequence = $latestItem ? intval(substr($latestItem->file_nb, 6)) + 1 : 1;
        
        // Format the next file_nb
        $nextFileNb = $dateToday . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        return $nextFileNb;
    }
   

}
