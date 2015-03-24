<?php namespace Kakuki\OAuth2\Components;

use Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Request;
use Illuminate\Routing\Controller;
use Cms\Classes\ComponentBase;
use Kakuki\OAuth2\Models\Settings;
use RainLab\User\Components\Session;
use RainLab\User\Models\User;
use Cms\Classes\Page;
use Redirect;
use App;


class SocialLogin extends ComponentBase {


    protected $request;

    public $provider;


	public function componentDetails() {
		return [
			'name' => 'SocialLogin',
			'description' => 'Allow users to login with 3th Party Accounts',
		];
	}

	public function defineProperties() {
		return [];
	}

	public function onRun() {

		$this->addCss('assets/css/custom.css');

        $redirectUrl = $this->pageUrl($this->property('redirect'));


        dd($this->property('redirect'));
        if($this->user())
            if ($redirectUrl = post('redirect', $redirectUrl)) {
                return Redirect::intended($redirectUrl);
            }

        //check for provider param in url
        if($provider = $this->param('provider')){


            $this->providerSession($provider);

            $request = $this->createRequest($provider);

            return $request->redirect();
        }

        //Log in user if Request has code
        if(Request::has('code')){

            $this->provider = Session::get('provider');

            $this->crawlUser();
            /*
            * Redirect to the intended page after successful sign in
            */

            $user = User::where( 'email', $fb_user->getProperty( 'email' ) )->first();
            if (!$user) {
                $password = uniqid();
                $user = Auth::register([
                    'name' => $fb_user->getProperty('first_name'),
                    'surname' => $fb_user->getProperty('last_name'),
                    'email' => $fb_user->getProperty('email'),
                    'username' => $fb_user->getProperty('email'),
                    'password' => $password,
                    'password_confirmation' => $password
                ], true);
            }


            Auth::login($user, true);

            if ($redirectUrl = post('redirect', $redirectUrl)) {
                return Redirect::intended($redirectUrl);
            }

        }

	}


	public function crawlUser() {

        $user = $this->createRequest($this->provider)->user();

        dd($user);
        Auth::login($user, true);

        return $user;

	}


    public function createRequest($provider){

        $instance = Socialite::driver($provider);
        return $this->injectCredentials($instance, $provider);
    }

    public function providerSession($provider){

        if(Session::has('provider'))
            Session::forget('provider');

        return Session::put('provider', $provider );
    }


    public function injectCredentials($instance, $provider){

        $instance = new $instance
        (
            Request::instance(),
            Settings::get('client_id_' . $provider),
            Settings::get('client_secret_' . $provider),
            Settings::get('callback_url')
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

}