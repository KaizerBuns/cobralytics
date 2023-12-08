<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PiwikAccess extends Model {

	protected $primaryKey = 'login,idsite';
	protected $table = 'access';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	protected $connection = 'mysql2';
	public $timestamps = false;
}
