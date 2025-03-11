<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LabOrders extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_visits_orders';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['user_num', 'clinic_num','doctor_num', 'patient_num', 'ext_lab', 
	                       'tests','chosen_codes', 'status','active','fixed_comment',
						   'report_datetime','order_datetime','preanalytical',
						   'collection_date','is_phlotobomy','coll_notes','chk_specimens','chk_specialcons',
						   'is_trial','import_date_time','pdf_path',
						   'request_nb','click_pdf','reject_note','other_reject_note'];
						   
	 public static function generateRequestNb()
    {
        // Get today's date in the format 'yymmdd'
        $dateToday = now()->format('ymd');
        
        // Find the latest request_nb for today's date
        $latestItem = self::where('request_nb', 'like', $dateToday . '%')
                          ->orderBy('request_nb', 'desc')
                          ->first();
                          
        // Determine the next sequence number
        $nextSequence = $latestItem ? intval(substr($latestItem->request_nb, 6)) + 1 : 1;
        
        // Format the next request_nb
        $nextRequestNb = $dateToday . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        return $nextRequestNb;
    }					   
    
    
}
