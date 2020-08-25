<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;
use Cache;
use DB;
class IndexController extends Controller
{
    /**
     * 登录
     * @param Request $request
     * @return IndexController|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $post = $request->post();
            $user = DB::table('user')->where('username', $post['username'])->first();
            if (!$user) {
                return $this->responed_data(500,'用户不存在');
            }
            if(!password_verify ( $post['password'] , $user->password)){
                return $this->responed_data(500,'密码输入不正确!');
            };
            $user->user_id = $user->id;
            session(['user'=>$user]);
            return $this->responed_data(200,'登录成功');
        } else {
            return view('login');
        }
    }

    /**
     * 注册
     * @param Request $request
     * @return IndexController|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $post = $request->all();
            $validator = Validator::make($post, [
                'username' => 'required|unique:user,username|max:100',
                'password' => 'required|between:6,20',
                'nickname' => 'required|string|max:255'
            ],[
                'username.required'=>'请填写用户名',
                'username.unique'=>'用户名已存在，请更换',
                'username.max'=>'用户名超长，请不要输入太长',
                'password.required'=>'请填写密码',
                'password.between'=>'请输入6-20位密码',
                'nickname.required'=>'请填写昵称',
            ]);
            if ($validator->fails()) {
                return $this->responed_data(500, $validator->errors()->first());
            }
            $code_value = \Illuminate\Support\Facades\Cache::get('image:'.$post['key']);
            if ($code_value != $post['code']) {
                return $this->responed_data(500,'验证码错误');
            }
            $user = DB::table('user')->where('username', $post['username'])->first();
            if ($user) {
                return $this->responed_data(500,'用户名已存在');
            }
            $data = [
                'avatar' => $post['avatar'] ?? 'uploads/avatar/avatar.jpg',
                'nickname' => $post['nickname'],
                'username' => $post['username'],
                'password' => password_hash($post['password'], PASSWORD_DEFAULT),
                'sign' => $post['sign'] ? $post['sign'] : '这个人很懒，什么都没有填写' ,
            ];
            $user_id = DB::table('user')->insertGetId($data);
            if (!$user_id) {
                return $this->responed_data(500,'注册失败');
            }
            //为用户创建默认分组
            DB::table('friend_group')->insert([
                'user_id' => $user_id,
                'groupname' => '默认分组'
            ]);
            //将用户添加到所有人都在群
            DB::table('group_member')->insert([
                'user_id' => $user_id,
                'group_id' => 10017
            ]);
            return $this->responed_data(200,'注册成功');
        } else {
            $code_hash = uniqid().uniqid();
            return view('register',['code_hash' => $code_hash]);
        }
    }

    /**
     * 首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('index',['sessionid' => session('user')->password,'sessionname'=>session('user')->username]);
    }

    /**
     * 文件上传
     * @param Request $request
     * @return IndexController
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $type = $request->input('type');
        $path = $request->input('path') ? $request->input('path') : '';
        $path = 'uploads/'.$path.'/'.date('Ymd').'/';
        if (!$file) {
            return $this->responed_data(500,'请选择上传的文件');
        }
        if (!$file->isValid()) {
            return $this->responed_data(500,'文件验证失败！');
        }
        $size = $file->getSize();
        if($size > 1024 * 1024 * 5 ){
            return $this->responed_data(500,'图片不能大于5M！');
        }
        $ext = $file->getClientOriginalExtension();     // 扩展名
        if ($type == 'im_image') {
            if (!in_array($ext, ['png', 'jpg', 'gif', 'jpeg', 'pem', 'ico'])) {
                return $this->responed_data(500, '文件类型不正确！');
            }
        }
        $filename = uniqid() . '.' . $ext;
        $res = $file->move(base_path('public/'.$path), $filename);
        if($res){
            $data = ['src'=>$path.$filename];
            if ($type == 'im_path') {
                $data['name'] = $file->getFilename();
            }
            return $this->responed_data(0,'上传成功',$data);
        }else{
            return $this->responed_data(500,'上传失败！');
        }
    }

    /**
     * 图片验证码
     * @param Request $request
     * @return mixed
     */
    public function imageCode(Request $request)
    {
        $key = $request->input('key');
        return getValidate(210,70,$key);
    }

    /**
     * 刷新图片验证码
     * @return IndexController
     */
    public function refreshImg()
    {
        $code_hash = uniqid().uniqid();
        return $this->responed_data(200,'',['code_hash' => $code_hash]);
    }

    /**
     * 消息盒子
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function messageBox()
    {
        $session = session('user');
        DB::table('system_message')->where('user_id',$session->user_id)->update(['read' => 1]);
        $list = DB::table('system_message as sm')
            ->leftJoin('user as f','f.id','=','sm.from_id')
            ->select('sm.id','f.id as uid','f.avatar','f.nickname','sm.remark','sm.time','sm.type','sm.group_id','sm.status')
            ->where('user_id',$session->user_id)
            ->orderBy('id', 'DESC')
            ->paginate(10);
        foreach ($list as $k => $v) {
            $list[$k]->time = time_tranx($v->time);
        }
        return view('message_box',['list' => $list]);
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginOut()
    {
        session(['user'=>null]);
        return redirect('/');
    }

    /**
     * 聊天日志页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chatLog(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        return view('chat_log',['id' => $id,'type' => $type]);
    }


    /**
     * 聊天日志数据
     * @param Request $request
     * @return mixed
     */
    public function chatRecordData(Request $request)
    {
        $session = session('user');
        $id = $request->get('id');
        $type = $request->get('type');
        if ($type == 'group') {
            $list = DB::table('chat_record as cr')
                ->leftJoin('user as u','u.id','=','cr.user_id')
                ->select('u.nickname as username','u.id','u.avatar','time as timestamp','cr.content')
                ->where('cr.group_id',$id)
                ->orderBy('time','DESC')
                ->paginate(10);
        } else {
            $list = DB::table('chat_record as cr')
                ->leftJoin('user as u','u.id','=','cr.user_id')
                ->select('u.nickname as username','u.id','u.avatar','time as timestamp','cr.content')
                ->where(function ($query) use($session, $id) {
                    $query->where('user_id', $session->user_id)
                        ->where('friend_id', $id);
                })
                ->orWhere(function ($query) use($session, $id) {
                    $query->where('friend_id', $session->user_id)
                        ->where('user_id', $id);
                })
                ->orderBy('time','DESC')
                ->paginate(10);
        }
        foreach ($list as $k=>$v){
            $list[$k]->timestamp = $v->timestamp * 1000;
        }
        return $this->responed_data(0,'',$list);

    }

}
