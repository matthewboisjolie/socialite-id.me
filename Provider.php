<?php

namespace SocialiteProviders\IdMe;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'IDME';

    protected $response_code;

    /**
     * {@inheritdoc}
     */
    protected $parameters = [
        'response_type'   => 'code',
    ];

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['military','student','teacher'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        $url = str_replace(array('&scope='), array('&scopes='), urldecode($this->buildAuthUrlFromBase('https://groups.id.me', $state)));
        return $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.id.me/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {

        $user_attribute_url = 'https://api.id.me/api/public/v3/attributes.json?access_token='.$token;

        try {
            $response = $this->getHttpClient()->get($user_attribute_url);
        } catch (Exception $e) {
            return;
        }

        $output = json_decode($response->getBody(), true);

        return $output;

    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        // Lets flatten the retuned array.
        $user_array = [];
        foreach($user['attributes'] as $attribute_array){
            $user_array[$attribute_array['handle']] = $attribute_array['value'];
        }

        return (new User())->setRaw($user)->map([
            'id'       => $user_array['uuid'],
            'name'     => $user_array['fname'] . ' ' . $user_array['lname'],
            'email'    => $user_array['email'],
            'avatar'   => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
