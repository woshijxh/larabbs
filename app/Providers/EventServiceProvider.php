<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \Illuminate\Auth\Events\Verified::class => [
            \App\Listeners\EmailVerified::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // add your listeners (aka providers) here
            'SocialiteProviders\Weixin\WeixinExtendSocialite@handle'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

/**
 *
 *
https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx788d3b04a4d76176&redirect_uri=http://larabbs.test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
 *
 *
$accessToken = '19__xUzasEMAFy0SeE73a2zbiXT-sb-PKPhwYMzxJDX2ATn7xfrO_xEubMKVcVFWl-FMTlbpkRiu-TmzXI1IRy4Jw';
$openID = 'omP_t5-fy3WmtvzDDInEpr4Za5a8';
$driver = Socialite::driver('weixin');
$driver->setOpenId($openID);
$oauthUser = $driver->userFromToken($accessToken);'

$code = '021gGRFi2rsRCB0qZBFi2lV2Gi2gGRF3';
$driver = Socialite::driver('weixin');
$response = $driver->getAccessTokenResponse($code);
$driver->setOpenId($response['openid']);
$oauthUser = $driver->userFromToken($response['access_token']);
 *
 */