<?php

namespace App\Http\Controllers\Ticket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Validator;

class SupervisorController extends Controller
{

    /**
     * 客服休假，休假的客服将不会被分配新的工单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function onVacation(Request $request)
    {
        $supervisorId = $request->input('supervisorId');
        DB::table('otc_supervisor_state')->where('supervisor_id',$supervisorId)->update(['active_state'=>1]);

        return response()->json(['msg'=>'success']);
    }    

    /**
     * 客服开工，开工后系统将向客服派发新工单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function backToWork(Request $request)
    {
        $supervisorId = $request->input('supervisorId');
        DB::table('otc_supervisor_state')->where('supervisor_id',$supervisorId)->update(['active_state'=>0]);

        return response()->json(['msg'=>'success']);
    }

    public function index()
    {
    	$data['res'] = DB::table('otc_supervisor_state')
                        ->join('auth_admins','otc_supervisor_state.supervisor_id','=','auth_admins.id')
                        ->paginate(20);

    	return view('Ticket.Supervisor.index',$data);
    }

    /**
     * 添加工单客服
     * @return [type] [description]
     */
    public function create()
    {

        return view('Ticket.Supervisor.create');
    }

    public function store(Request $request)
    {
    	$input = $request->all();

        $rules = [
            'name' => 'required|unique:auth_admins,name',
            'email' => 'required|unique:auth_admins,email|email',
        ];

		$message = [
            'name.required'=>'用户名不能为空',
            'name.unique'=>'该用户名已经存在',
            'email.required'=>'邮箱地址不能为空',
            'email.unique'=>'该邮箱地址已经存在',
            'email.email'=>'请输入正确的邮箱格式',
        ];


        $validator = Validator::make($input,$rules,$message );
        if ($validator->fails()) {
            return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {
        	// 账号信息
        	$supervisor = [
        		'name' =>$request->input('name'),
        		'email' =>$request->input('email'),
        		'role_id' =>3,
        		'password' =>bcrypt('123456'),
        		'created_at'=> \Carbon\Carbon::now(),
        		'updated_at'=> \Carbon\Carbon::now(),
        	];

        	DB::transaction(function () use($supervisor) {
			    $supervisorId = DB::table('auth_admins')->insertGetId($supervisor);

			    $supervisorState = [
			    	'supervisor_id'=>$supervisorId,
			    	'live_state'=>0,
			    	'active_state'=>0,
			    	'ticket_amount'=>0,
			    	'created_at'=> \Carbon\Carbon::now(),
        			'updated_at'=> \Carbon\Carbon::now(),
			    ];

			    DB::table('otc_supervisor_state')->insert($supervisorState);
			    
			});

			return response()->json([
			    'success' => true,
			]);

        }


    }

    /**
     * 更新账户邮箱
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id)
    {
    	$data['res'] = DB::table('auth_admins')->where('id',$id)->first();

    	return view('Ticket.Supervisor.edit',$data);
    }


    public function update(Request $request, $id)
    {
    	$input = $request->all();

        $rules = [
            'email' => 'required|unique:auth_admins,email|email',
        ];

		$message = [
            'email.required'=>'邮箱地址不能为空',
            'email.unique'=>'该邮箱地址已经存在',
            'email.email'=>'请输入正确的邮箱格式',
        ];


        $validator = Validator::make($input,$rules,$message );
        if ($validator->fails()) {
            return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {
        	$supervisor = [
        		'email' => $request->input('email'),
        		'updated_at'=> \Carbon\Carbon::now(),
        	];

        	DB::table('auth_admins')->where('id',$id)->update($supervisor);
			return response()->json([
			    'success' => true,
			]);

        }
    }

    /**
     * 重置密码
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function reset($id)
    {
        $data['id'] = $id;
        return view('Ticket.Supervisor.reset',$data);
    }

    public function savePassword(Request $request, $id)
    {
        $input = $request->all();

        $rules = [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ];

        $message = [
            'password.required'=>'请填写密码',
            'password.confirmed'=>'两次输入的密码不同，请重新输入',
            'password_confirmation.required'=>'请重复输入密码',
        ];


        $validator = Validator::make($input,$rules,$message );

        if ($validator->fails()) {
            return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {

            $newPassword = $request->input('password');

            DB::table('auth_admins')->where('id',$id)->update(['password'=>bcrypt($newPassword)]);
            return response()->json([
                'success' => true,
            ]);

        }
    }
}
