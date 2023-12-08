<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PiwikSite extends Model {

	protected $primaryKey = 'idsite';
	protected $table = 'site';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	protected $connection = 'mysql2';
	public $timestamps = false;

}
