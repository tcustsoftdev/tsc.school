<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ContactInfo;

class Center extends Model
{
	public static $snakeAttributes = false;
	
	protected $fillable = [  'head', 'oversea', 'areaId','importance',
							 'name','code', 'courseTel','rule',
							 'active','removed', 'updatedBy'
						  ];

	public static function init()
	{
		return [
			'head' => 0,
			'oversea' => 0,
			'areaId' => 0,
			'name' => '',
			'code' => '',
			'courseTel' => '',
			'importance' => 0,
			'rule' => '',
			'active' => 0,

			'contactInfo'=> ContactInfo::init()

		];
	}	

    public function contactInfoes() 
	{
		return $this->hasMany('App\ContactInfo','centerId');
	}

	public function courses() 
	{
		return $this->hasMany('App\Course','centerId');
	}
	public function notices() 
	{
		return $this->hasMany('App\Notice','centerId');
	}

	public function area() 
	{
		return $this->hasOne('App\Area', 'id' ,'areaId');
	}

	public function admins()
    {
        return $this->belongsToMany(Admin::class,'center_admin','center_id','admin_id');
	}

	public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'center_teacher','center_id','teacher_id');
	}
	public function teacherGroups()
    {
		return $this->hasMany('App\TeacherGroup','centerId', 'id');
      
	}
	public function discounts()
    {
        return $this->belongsToMany(Discount::class,'center_discount','center_id','discount_id');
	}

	public function getContactInfo()
	{
		return $this->contactInfoes->first();
	}
	public function loadContactInfo()
	{
		$this->contactInfo=$this->getContactInfo();
		if($this->contactInfo)  $this->contactInfo->address->fullText();
	}

	public function  toOption()
    {
		return [ 'text' => $this->name ,  'value' => $this->id ];
       
	}
	
	public function isEast()
	{
		return $this->head;
	}

	
}
