<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryClients extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_clients';
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
		'analyzer',
		'delivery'
    ];    
}
