<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoLocation extends Model
{
    protected $table = 'geo_locations';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	public $timestamps = false;
}
