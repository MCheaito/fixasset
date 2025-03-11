<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryCollectionFournisseur extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_fournisseur_collection';

    protected $fillable = [
        'fournisseur_id',
		'collection_id',
		'name',
		'name_eng',
		'user_id',
		'clinic_num',
		'active'
		
    ];
    
   
}
