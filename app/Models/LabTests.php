<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LabTests extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_lab_tests';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
	'clinic_num', 'user_num', 'test_code', 'descrip', 'group_num', 'normal_value', 'test_rq', 
	'unit', 'price', 'test_name', 'test_type', 'cnss', 'nbl', 'listcode', 'testord', 'active', 
	'category_num', 'is_group', 'referred_tests', 'preanalytical', 'storage', 'transport', 'tat_hrs',
	'is_printed','clinical_remark','dec_pts','specimen','special_considerations','is_culture','result_text',
	'custom_test','is_valid','is_title'
	];

    
    
}
