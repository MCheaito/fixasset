<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LabResults extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_visits_order_results';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['user_num', 'visit_num', 'clinic_num', 
	                       'doctor_num', 'patient_num', 'ext_lab',
 				           'order_id','test_id','result','result_txt','result_status',
						   'active','instruction','field_num','sign','prev_result_num',
						   'ref_range','unit','calc_result','calc_unit','group_order','subgroup_order','group_num',
						   'one_ref_range','need_validation'];
    
    
}
