<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Signup;
use App\Bill;
use App\Student;

use App\Services\Signups;
use App\Services\Bills;

use App\Services\ESuns;
use App\Services\Students;
use App\Http\Requests\BillRequest;

use App\Core\PagedList;
use App\Core\Helper;
use DB;

class BillsController extends Controller
{
    public function __construct(Signups $signups, ESuns $ESuns,Bills $bills, Students $students)        
    {
        $this->signups=$signups;
        $this->ESuns=$ESuns;
        $this->bills=$bills;
        $this->students=$students;
    }



    public function show($id)
    {
        
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        if($signup->userId != $this->currentUserId()) abort(404);

        $signup->loadViewModel();
        foreach($signup->details as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();

        }


        $model=[
            'title' => '繳費',
            'topMenus' => $this->clientMenus(),

            'signup' => $signup
           
        ];

        return view('client.bills.show')->with($model);
    }

    public function print($id)
    {
        $signup = $this->signups->getById($id);
        if(!$signup) abort(404);

        if($signup->userId != $this->currentUserId()) abort(404);

        if($signup->bill->payed) abort(404);

        $this->bills->createBillCode($signup);

        $signup = $this->signups->getById($id);
        
        $signup->loadViewModel();
        foreach($signup->details as $detail){
          
            $detail->course->fullName();
            $detail->course->loadClassTimes();

        }

        $model=[
            'title' => '列印繳費單',
            'topMenus' => $this->clientMenus(),

            'signup' => $signup
           
        ];

        return view('client.bills.print')->with($model);
    }

    //信用卡繳費
    public function credit($id)
    {
        $this->ESuns->credit();
        dd($id);
    }


    //銀行回傳資料用
    public function store(Request $request)
    {
       
        return response()->json($request->toArray());

        // $code='';
        // $amount='';
        // $payway='';

        // $bill=$this->bills->payBill($code, $amount, $payway);

        // $this->students->createStudent($bill->signup->courseId, $bill->signup->userId);
      
        // return response()->json();
       
    }

   

}
