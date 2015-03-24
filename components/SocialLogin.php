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

    private   $client_secret;

    private   $client_id;


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


	public function onRun()
    {

        $this->addCss('assets/css/custom.css');
        $this->socialite_providers = $this->page['socialite_providers'] =$this->providersList();

        //check for provider param in url
        if($provider = $this->param('provider')){

            $this->provider = $provider;
            $this->callback_url = preg_replace('~.*\K:(.*)~s','',Request::root().$this->page->url);
            $this->providerData($provider);
            $this->setSessionProvider();
            $this->request = $this->createRequest($provider);

            return $this->request->redirect();

        }

        //Authorize user if Request has code
        if(Request::has('code')){

            if(!$this->getSession())
                return;

            $credentials = $this->getSession();
            $this->provider = $credentials['provider'];
            $this->client_id = $credentials['client_id'];
            $this->callback_url = $credentials['callback_url'];
            $this->client_secret = $credentials['client_secret'];

            $this->request = $this->createRequest($this->provider);

            dd($this->request->user());
        //reuse save session

        }

	}

    public function createRequest($provider)
    {
        $instance = Socialite::driver($provider);
        $init = $this->injectCredentials($instance);

        return $init;
    }

    public function setSessionProvider()
    {
        if(Session::has('oauth'))
            Session::forget('oauth');

        $data = array(
            'provider' =>$this->provider,
            'callback_url'=> $this->callback_url,
            'client_id' =>$this->client_id,
            'client_secret'=>$this->client_secret
        );

        Session::put('oauth', $data );
        Session::save();

    }

    public function getSession()
    {
        return Session::get('oauth');
    }

    public function injectCredentials($instance){

        $instance = new $instance
        (
            Request::instance(),
            $this->client_id,
            $this->client_secret,
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
        $credential = Setting::where('provider', $provider)->first()->toArray();

        if($credential){
            $this->client_id = $credential['client_id'];
            $this->client_secret = $credential['client_secret'];
        }

        return;
    }
}