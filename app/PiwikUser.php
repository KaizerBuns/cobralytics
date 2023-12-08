<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PiwikUser extends Model {

	protected $primaryKey = 'login';
	protected $table = 'user';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	protected $connection = 'mysql2';
	public $timestamps = false;
}
