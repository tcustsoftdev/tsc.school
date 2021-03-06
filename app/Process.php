<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = [  'courseId', 'order', 
        'title','content', 'materials'
    ];

    public static function init($year)
	{
		return [
			'courseId' => 0,
			'order' => 1,
			'title' => '',
			'content' => '',
			'materials' => '',

		];
    }	
    
    public function course() 
	{
		return $this->hasOne('App\Course', 'id' ,'courseId');
    }
}
