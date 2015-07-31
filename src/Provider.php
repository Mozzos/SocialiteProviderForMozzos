<?php

namespace Udoless\SocialiteProviders\Mozzos;

use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected $scopes = [];

    function __construct(Request $request, $clientId, $clientSecret, $redirectUrl){
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);

        $scopesConfig = config('services.mozzos.scopes');
        $scopesConfig = is_array($scopesConfig)?$scopesConfig:['r_user.profile'];
        $this->scopes = $scopesConfig;
    }
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'http://www.mozzos.com/oauth2/authorization', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'http://www.mozzos.com/zh/oauth2/accessToken?grant_type=authorization_code';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'http://api.mozzos.com/user/profile', [
            'headers' => [
                'Accept-Language' => 'en-US',
                'x-li-format' => 'json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'], 'nickname' => $user['nickname'],
            'name' => $user['name'], 'email' => $user['email'],
            'avatar' => array_get($user, 'avatarUrl'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => array_merge($this->getTokenFields($code),['grant_type'=>'authorization_code']),
        ]);

        return $this->parseAccessToken($response->getBody());
    }
}
