<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try {
            $result = $easySms->send($phone, [
                'template'  =>  "294095",
                'data' => [ //data数组的内容对应于腾讯云“短信正文“里的变量
                    $code, //变量1
                    2, //变量2
                ],
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('qcloud')->getMessage();
            return $this->response->errorInternal($message ?: '短信发送异常');
        }

        $key = 'verificationCode_'.str_random(15);
        // 设置过期时间10分钟
        $expiredAt = now()->addMinutes(10);

        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            ])->setStatusCode(201);
    }
}
