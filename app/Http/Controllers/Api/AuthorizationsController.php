<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SocialAuthorizationRequest;
use Illuminate\Http\Request;
use App\Models\User;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->responser->errorBadRequest();
        }

        $driver = \Socialite::driver($type);

        try{
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;

                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                }else{
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
            if (!$user) {
                $user = User::create([
                    'name' => $oauthUser->getNickname(),
                    'avatar' => $oauthUser->getAvatar(),
                    'weixin_openid' => $oauthUser->getId(),
                    'weixin_unionid' => $unionid,
                ]);
            }

            break;
        }

        return $this->response->array(['token' => $user->id]);
    }
}
 // https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx788d3b04a4d76176&redirect_uri=http://larabbs.test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect

// 071Ym61v0R8ctj1ee52v08JO0v0Ym61h
