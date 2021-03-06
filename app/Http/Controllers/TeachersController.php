<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TeacherRequest;

use App\Teacher;
use App\User;
use App\Profile;
use App\Center;
use App\Role;
use App\Services\Teachers;
use App\Services\TeacherGroups;
use App\Services\Users;
use App\Services\Centers;
use App\Services\Courses;
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;
use Illuminate\Support\Facades\Input;

class TeachersController extends Controller
{
    
    public function __construct(Teachers $teachers, TeacherGroups $teacherGroups ,Users $users,
        Centers $centers ,Courses $courses,Files $files)
    {
        $this->teachers=$teachers;
        $this->teacherGroups=$teacherGroups;
        $this->users=$users;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->files=$files;
       
    }

    function canEdit($teacher)
    {
        if($this->currentUserIsDev()) return true;
        if(!count($teacher->centers)) return true;
      
        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect($teacher->centers);

        if(count($intersect)) return true;
        return false;

    }

    function canReview(Teacher $teacher)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect($teacher->centers);

        
        if(count($intersect)) return true;
        return false;

    }
    function canEditCenter(Center $center)
    {
        if($this->currentUserIsDev()) return true;
        
      
        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$center]);

        if(count($intersect)) return true;
        return false;

    }
    function canReviewCenter(Center $center)
    {
        if(!$center) return false;

        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        $centersCanAdmin= $this->centersCanAdmin();
        $intersect = $centersCanAdmin->intersect([$center]);

        if(count($intersect)) return true;
        return false;
    }

    function canDelete($teacher)
    {
        return $this->canReview($teacher);
    }


    function canImport()
    {
        return $this->currentUserIsDev();
    }

    function canEditCenters()
    {
        if($this->currentUserIsDev()) return true;
        return $this->currentUser()->admin->isHeadCenterTeacher();
    }


    function loadCenterNames($teacher)
    {
        if(count($teacher->centers)){
            $teacher->centerNames=join(',',$teacher->centers->pluck('name')->toArray() );
        }else{
            $teacher->centerNames='';
        }
    
    }

    function loadWage($teacher)
    {
        $wage = $teacher->getWage();
        if($wage){
            $teacher->wage=$wage->money;
            $teacher->account=$wage->account;
        }else{
            $teacher->wage=0;
            $teacher->account='';
        }
        
    }
 
   
    public function index()
    {
        
        $request=request();

        $group=false;
        if($request->group)  $group=Helper::isTrue($request->group);
      

        $center=0;
        if($request->center)  $center=(int)$request->center;

        $reviewed=true;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $selectedCenter = null;
        if ($center) $selectedCenter = Center::find($center);
        if ($selectedCenter == null)
        {
            $center = 0;
            if ($pageSize == 999) $pageSize = 10;
        }
        else
        {
            $pageSize = 999;
        }

        if($group){
            return $this->teacherGroupsIndex($selectedCenter, $keyword ,$page, $pageSize);
        }

        $canReview=false;
        if($selectedCenter)   $canReview=$this->canReviewCenter($selectedCenter);
      
        $teachers =  $this->teachers->fetchTeachers($selectedCenter, $reviewed, $keyword);
      
        $pageList = new PagedList($teachers,$page,$pageSize);
        
        if (!$selectedCenter)
        {
            foreach($pageList->viewList as $teacher){
                $this->loadCenterNames($teacher);
                $teacher->user->loadContactInfo();
            } 
        }
       

        if($this->isAjaxRequest()){
            return response() ->json([
                'canReview' => $canReview,
                'model' => $pageList
            ]);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');

        $centers=$this->centers->centerOptions();
       
       
        return view('teachers.index')->with([
            'title' => '教師管理',
            'menus' => $menus,
            'centers' => $centers,
            'canReview' => $canReview,
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

    function teacherGroupsIndex($selectedCenter, $keyword ,$page, $pageSize)
    {
        $teachers =  $this->teacherGroups->fetchTeacherGroups($selectedCenter, $keyword);
      
        $pageList = new PagedList($teachers,$page,$pageSize);
        
        if (!$selectedCenter)
        {
            foreach($pageList->viewList as $teacherGroup){
                $teacherGroup->getTeacherNames();
                $teacherGroup->centerName=$teacherGroup->center->name;
            } 
        }

        $canReview=false;
        if($selectedCenter)   $canReview=$this->canReviewCenter($selectedCenter);

        if($this->isAjaxRequest()){
            return response() ->json([
                'canReview' => $canReview,
                'model' => $pageList
            ]);
        }
       
     
        $menus=$this->adminMenus('UsersAdmin');

        

        $centers=$this->centers->centerOptions();
       
       
        return view('teachers.index')->with([
            'title' => '教師管理',
            'menus' => $menus,
            'centers' => $centers,
            'canReview' => $canReview,
            'canImport' => $this->canImport(),
            'list' =>  $pageList
        ]);
    }

   

    public function create()
    {
        $teacher=Teacher::init();
        $user=User::init();

    
        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

        $centerIds=[];
        if (count($centerOptions))
        {
            array_push($centerIds,$centerOptions[0]['value']);
        }
      
        $form=[
            'teacher' => $teacher,
            'user' => $user,
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
      
    }

    function validateTeacherInputs($values)
    {
        $errors=[];

        $group=false;
        if(array_key_exists('group',$values)) $group=Helper::isTrue($values['group']);
        
        if($group){


        }else{
            $wage=0;
            if($values['wage']) $wage=floatval($values['wage']);
            if(!$wage) 	$errors['teacher.wage'] = ['必須填寫鐘點費'];

            if(!$values['account']) 	$errors['teacher.account'] = ['必須填寫銀行帳號'];

        }

        return $errors;
    }

    public function store(TeacherRequest $request)
    {

        $teacherValues=$request->getTeacherValues();
        $userValues=$request->getUserValues();
        $profileValues= $userValues['profile'];

        
        $errors=$this->users->validateUserInputs($userValues,Role::teacherRoleName());
        if($errors) return $this->requestError($errors);

        $errors=$this->validateTeacherInputs($teacherValues);

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

      
        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $updatedBy=$current_user->id;

        $teacherValues['updatedBy']=$updatedBy;
        $userValues['updatedBy']=$updatedBy;
        $profileValues['updatedBy']=$updatedBy;
        
        $userValues=array_except($userValues,['profile']);
        $userId=$request->getUserId();
    
        $user=null;
        if($userId){
            $user = User::find($userId);

            $user->profile->update($profileValues);
            $this->users->updateUser($user,$userValues);
            
        }else{
          
           $user=$this->users-> createUser(new User($userValues),new Profile($profileValues));
           $userId=$user->id;
         
        }

        $wageValues=[
            'account' => $teacherValues['account'],
            'money' => $teacherValues['wage'],
            'updatedBy' => $updatedBy,
        ];

        $teacher=Teacher::find($userId);
        if($teacher){
            $teacher->update($teacherValues);
            $teacher->addRole();

            $wage=$teacher->getWage();
            if($wage) $wage->update($wageValues);

        }else{
            $teacher=$this->teachers->createTeacher($user,new Teacher($teacherValues), $wageValues);
            $teacher->userId=$userId;
        }

        $teacher->centers()->sync($centerIds);
       
        return response() ->json($teacher);
    }

    public function show($id)
    {
        $teacher = $this->teachers->getById($id);
        if(!$teacher) abort(404);

        $current_user=$this->currentUser();

        $this->loadCenterNames($teacher);
        

        $courseIds=$teacher->courses()->pluck('id')->toArray();
        $courses=$this->courses->getByIds($courseIds)->get();
        foreach($courses as $course){
            $course->fullName();
            $course->loadClassTimes();
        } 

        $teacher->courses = $courses;

        $this->loadWage($teacher);

        $teacher->user->loadContactInfo();
     
        $teacher->canEdit=$this->canEdit($teacher);
        $teacher->canDelete=$this->canDelete($teacher);
       

        return response() ->json($teacher);
        
    }

    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);        
        if(!$this->canEdit($teacher)) $this->unauthorized();

        $this->loadWage($teacher);
       
        $centerIds=$teacher->centers->pluck('id')->toArray();

        $centersCanAdmin= $this->centersCanAdmin();
        $centerOptions = $centersCanAdmin->map(function ($item) {
            return [ 'text' => $item->name ,  'value' => $item->id ];
        })->all();

      
        $form=[
            'teacher' => $teacher,
         
            'centerOptions' => $centerOptions,
            'centerIds' => $centerIds

        ];

        return response() ->json($form);
       
        
    }


    public function update(TeacherRequest $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        if(!$this->canEdit($teacher)) $this->unauthorized();
       
        $values=$request->getTeacherValues();
     
        $errors=$this->validateTeacherInputs($values);

        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
        }

        if($errors) return $this->requestError($errors);

        $current_user=$this->currentUser();
        $values['updatedBy'] = $current_user->id;

        $teacher->update($values);

        $wageValues=[
            'account' => $values['account'],
            'money' => $values['wage'],
            'updatedBy' =>  $current_user->id
        ];

        $teacher->setWage($wageValues);
     
        $centerIds=$request->getCenterIds();
        if(!count($centerIds)){
            $errors['centerIds'] = ['請選擇所屬中心'];
            return $this->requestError($errors);
        }

        $teacher->centers()->sync($centerIds);

        return response() ->json();
    }

    public function review(Request $form)
    {
        $reviewedBy=$this->currentUserId();
        
        $teachers=  $form['teachers'];

        if(count($teachers) > 1){
            $teacherIds=array_pluck($teachers, 'id');
            $this->teachers->reviewOK($teacherIds, $reviewedBy);
        }else{
            
            $id=$teachers[0]['id'];
         
            $reviewed=Helper::isTrue($teachers[0]['reviewed']);

            $this->teachers->updateReview($id,$reviewed ,$reviewedBy);
        }
        

        return response() ->json();


    }

    public function destroy($id) 
    {
        $teacher = Teacher::findOrFail($id);
        if(!$this->canDelete($teacher)) $this->unauthorized();

        $this->teachers->deleteTeacher($teacher, $this->currentUserId());
       
       
        return response() ->json();
    }

    public function import(Request $form)
    {
        
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        
        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);


        $file=Input::file('file');   

        $group=Helper::isTrue($form['group']);
        if($group){
            $err_msg=$this->teacherGroups->importTeacherGroups($file,$this->currentUserId());
        }else{
            
            $err_msg=$this->teachers->importTeachers($file,$this->currentUserId());
        
        }

     
        
        if($err_msg)
        {
            $errors['msg'] = [$err_msg];
            return $this->requestError($errors);
        }

        return response() ->json();

       
    }

    public function upload(Request $form)
    {
        if(!$this->canImport()){
            return $this->unauthorized();
        }

        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);

        $type=$form['type'];
        if(!$type) abort(500);

        $file=Input::file('file');  

        $center = Center::findOrFail($form['center']);  

        $canEdit = $this->canEditCenter($center);
        if(!$canEdit) return $this->unauthorized();

       

        $this->files->saveUploadsData($file,$type,$center);

        return response() ->json();
        
       
    }


}
