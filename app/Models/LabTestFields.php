<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTestFields extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_lab_tests_fields';

    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
	 'test_id', 'test_code', 'fage', 'tage', 'gender', 'normal_value1', 'normal_value2', 'mytype', 'rtype', 'descrip', 
	 'clinic_num', 'user_num', 'active','panic_low_value','panic_high_value','remark','unit',
	 'desirable_low','desirable_high','is_comparison','field_order','criteria','sign_min','sign_max',
	 'sign'
	 ];

    
}
