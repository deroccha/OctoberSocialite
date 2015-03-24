<?php namespace Kakuki\OAuth2\Components;

use Session;
use Cms\Classes\ComponentBase;
use Auth;
use Request;
use Kakuki\OAuth2\Models\Setting;
//use RainLab\User\Components\Session as RainLabSession;
//use RainLab\User\Models\User;
use Socialite;
use Redirect;
//use Kakuki\OAuth2\Classes\ProviderSession;


class SocialLogin extends ComponentBase {

    public    $request;

    public    $provider;

    public    $callback_url;

    public    $socialite_session;

    public    $socialite_providers;

    //private   $socialite;


	public function componentDetails() {
		return [
			'name' => 'SocialLogin',
			'description' => 'Allow users to login with 3th Party Accounts',
		];
	}

	public function defineProperties() {
		return [];
	}

    public function init()
    {

        $this->setSessionProvider();
    }


	public function onRun()
    {

        $this->addCss('assets/css/custom.css');
        $this->socialite_providers = $this->page['socialite_providers'] =$this->providersList();

        //check for provider param in url
        if($provider = $this->param('provider')){

            $this->callback_url = preg_replace('~.*\K:(.*)~s','',Request::root().$this->page->url);
            $this->request = $this->createRequest($provider);
            $this->setSession();
            return $this->request->redirect();

        }

        //Authorize user if Request has code
        if(Request::has('code')){

            var_dump(Session::all());
            if(!$this->getSession())
                return;

            dd($this->getSession()->user() );

        }

	}

    public function createRequest($provider)
    {
        $instance = Socialite::driver($provider);
        $init = $this->injectCredentials($instance, $provider);

        return $init;
    }


    public function setSession()
    {
        if(Session::has('socialite_object'))
            Session::forget('socialite_object');

        Session::put('socialite_object', $this->request );

        return;

    }


    public function setSessionProvider()
    {
        if($provider = $this->param('provider')){

            if(Session::has('provider'))
                Session::forget('provider');

            var_dump( Session::get('provider'));
            return  Session::put('provider', $this->provider );

        }

        return;

    }


    public function getSession()
    {
        return Session::get('socialite_object');
    }



    public function injectCredentials($instance, $provider){
        $credential = $this->providerData($provider)->toArray();
        $instance = new $instance
        (
            Request::instance(),
            $credential['client_id'],
            $credential['client_secret'],
            $this->callback_url
        );

        return $instance;
    }

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check())
            return null;

        return Auth::getUser();
    }


    public function providersList()
    {
        return Setting::lists('provider');
    }

    public function providerData($provider)
    {
        return Setting::where('provider', $provider)->first();
    }
}