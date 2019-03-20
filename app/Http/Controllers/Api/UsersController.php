<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verigyData = \Cache::get($request->verification_key);

        if (!$verigyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verigyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $verigyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->created();
    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }
}
