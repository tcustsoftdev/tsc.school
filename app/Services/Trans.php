<?php

namespace App\Services;
use App\User;
use App\Student;
use App\Profile;
use App\Center;
use App\Tran;
use App\Course;
use App\Services\Users;
use DB;

class Trans 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
        $this->with=['signupDetail.signup','course.center'];
    }
    
    public function getById($id)
    {   
        return Tran::with($this->with)->find($id);
    }

    

    public function createTran(Tran $tran,Student $student)
    {
       
        $tran= DB::transaction(function() use($tran,$student) {
            $tran->save();
            $student->update([
                'status' => -1, 
            
            ]);
            Student::create([
                'status' => 1,
                'userId' => $student->userId,
                'courseId' => $tran->courseId,
            ]);
           
            return $tran;
		});
    

        return $tran;
    }

    
    
   

    
    
}