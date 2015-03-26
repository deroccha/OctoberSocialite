<?php namespace Kakuki\OAuth2\Classes;

use Laravel\Socialite\Facades\Socialite;
use Session;


class ProviderSession
{

    use \October\Rain\Support\Traits\Singleton;


    protected $provider;


    public function __construct(Socialite $socialite, Request $request)
    {

    }

    public function setSession($obj)
    {
        if(Session::has('socialite_object'))
            Session::forget('socialite_object');

        return Session::put('socialite_object', $obj );

    }


    public function getSession()
    {
        return Session::get('socialite_object');
    }


}