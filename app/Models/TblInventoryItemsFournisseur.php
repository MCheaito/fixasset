<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryItemsFournisseur extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_items_fournisseur';
	protected $fillable = [
		'id',
		'code',		
        'name',
		'adresse',
		'tel',
		'email',
		'ville',
		'province',
		'codepostal',
		'pays',
		'contact',
		'fax',
		'num_compte',
		'sequence',
		'types',
		'item_seq',
		'code_method',
		'notes',		
		'user_id',
		'clinic_num',
        'active',
		'fournisseur',
		'vat',
		'regno'
    ];    
}
