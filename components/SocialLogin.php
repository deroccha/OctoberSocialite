<?php namespace Kakuki\OAuth2\Components;

use App;
use Session;
use Cms\Classes\ComponentBase;
use Auth;
use Request;
use Kakuki\OAuth2\Models\Setting;
use RainLab\User\Components\Session as RainLabSession;
use RainLab\User\Models\User;
use Kakuki\OAuth2\Models\SocialiteUsers;
use Socialite;
use Redirect;
use Exception;
use Flash;
use Event;
use Kakuki\OAuth2\Classes\DataFetcher;
use Kakuki\OAuth2\Classes\ProviderSession;


class SocialLogin extends ComponentBase
{

    /**
     * The request Object
     *
     * @object
     */
    public $request;

    /**
     * Requested Provider
     *
     * @var
     */
    public $provider;

    /**
     * OAuth Callback URL which matches with Page URL where Component is attached
     *
     * @var
     */
    public $callback_url;

    /**
     * All registered Socialate Provider by Plugin User
     *
     * @var
     */
    public $socialite_providers;

    /**
     * Holds OAuth credentials
     *
     * @var array
     */
    private $oauth = array();


    public function componentDetails()
    {
        return [
            'name'        => 'SocialLogin',
            'description' => 'Allow users to login with 3th Party Accounts',
        ];
    }

    public function defineProperties()
    {
        return [];
    }


    public function onRun()
    {

        if ($this->user()) {
            return Redirect::to('login');
        }

        $this->addCss('assets/css/custom.css');
        $this->socialite_providers = $this->page['socialite_providers']
            = $this->providersList();


        //check for provider param in url
        if ($provider = $this->param('provider')) {

            $this->provider = $provider;
            $this->callback_url = preg_replace('~.*\K:(.*)~s', '', Request::root().$this->page->url);
            $this->providerData($provider);
            $this->request = $this->createRequest($provider);

            return $this->request->redirect();

        }

        //Authorize user if Request has code
        if (Request::has('code')) {

            if (!$session = $this->getSession()) {
                return;
            }

            $this->request = $this->createRequest($this->revokeProvider());

            if ($social_user = $this->request->user()) {
                $this->authorize($social_user);
            }

            //Hardcoded redirect
            return Redirect::to('/login');

        }

    }

    /**
     * Initiate Socialite Provider
     *
     * @param $provider
     *
     * @return mixed
     */
    public function createRequest($provider)
    {

        $instance = Socialite::driver($provider);
        $init = $this->injectCredentials($instance);

        return $init;
    }

    /**
     * save OAuth credentials and Provider in a session
     *
     * @oauth    array()
     * @provider string
     */
    public function setSessionProvider()
    {
        if (Session::has('oauth')) {
            Session::forget('oauth');
        }

        if (Session::has('provider')) {
            Session::forget('provider');
        }

        Session::put('provider', $this->provider);
        Session::put('oauth', $this->oauth);
        Session::save();

    }

    /*
     * Get OAuth Session
     */
    public function getSession()
    {
        if (Session::has('oauth')) {
            return $this->oauth = Session::get('oauth');
        }

        return;
    }

    public function revokeProvider()
    {
        return Session::get('provider');
    }

    /**
     * Inject fetched Credentials in Socialite Object
     *
     * @param $instance
     *
     * @return mixed
     */
    public function injectCredentials($instance)
    {

        $reflection_class = new \ReflectionClass($instance);
        array_unshift($this->oauth, Request::instance());
        $instance = $reflection_class->newInstanceArgs($this->oauth);

        return $instance;
    }

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    /**
     * Get all registered Providers by Plugin User
     *
     * @return mixed
     */
    public function providersList()
    {
        return Setting::where('status', 1)->lists('provider');
    }

    /**
     * Fetch from Database Credentials on Provider
     *
     * @param $provider
     */
    public function providerData($provider)
    {
        $credential = Setting::where('provider', $provider)->first()->toArray();
        if ($credential) {
            $this->client_id = $credential['client_id'];
            $this->client_secret = $credential['client_secret'];
            $this->oauth = array(
                $credential['client_id'],
                $credential['client_secret'],
                $this->callback_url
            );

            $this->setSessionProvider();
        }

        return;
    }


    public function authorize($social_user)
    {

        $socialite_user = SocialiteUsers::where('socialite_id', $social_user->id)
            ->where('provider', $this->revokeProvider())->first();

        if (!$socialite_user) {

            $user = User::where('email', $social_user->email)->first();

            if (!$user) {
                $password = uniqid();

                $data = array(
                    'name'                  => $social_user->name,
                    'email'                 => $social_user->email,
                    'username'              => $social_user->email,
                    'password'              => $password,
                    'password_confirmation' => $password
                );
                //pass social user and provider to Fetcher

                try {

                    $user = Auth::register($data, true);

                } catch (Exception $ex) {

                    return Flash::error($ex->getMessage());

                }
            }
            $socialite_user = new SocialiteUsers();
            $socialite_user->user_id = $user->id;
            $socialite_user->socialite_id = $social_user->id;
            $socialite_user->provider = $this->revokeProvider();

            try {

                $socialite_user->save();

            } catch (Exception $ex) {

                return Flash::error($ex->getMessage());
            }

        } else {

            $user = $socialite_user->user;
        }

        Auth::login($user, true);

        return;


    }
}