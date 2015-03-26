<?php namespace Kakuki\OAuth2;

use App;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use System\Classes\PluginBase;
use Rainlab\User\Models\User as UserModel;
use Backend;
use Event;


/**
 * OAuth2 Plugin Information File
 */
class Plugin extends PluginBase
{


    /**
     * Set required dependencies
     *
     * plugin /RainLab/User
     */

    public $require
        = [
            'RainLab.User'
        ];


    /**
     * Returns information about this plugin.
     *
     * @return array
     */

    public function pluginDetails()
    {
        return [
            'name'        => 'Laravel Socialite',
            'description' => 'Log in User with OAuth2',
            'author'      => 'Kakuki',
            'icon'        => 'icon-leaf',
        ];
    }

    /**
     * Register service provider and alias facade.
     */
    public function register()
    {

        App::register('SocialiteProviders\Manager\ServiceProvider');

        // Register alias
        $alias = AliasLoader::getInstance();
        $alias->alias('Socialite', 'Laravel\Socialite\Facades\Socialite');

    }


    public function boot()
    {
        UserModel::extend(function($model){
           $model->hasOne['token'] = ['Kakuki\OAuth2\Models\SocialiteUsers'];
        });

        Event::fire('SocialiteProviders\Manager\SocialiteWasCalled', function(Socialite $socialite, Request $request){
            dd($socialite);
        });
    }

    /**
     * Register Settings Menu
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'providers' => [
                'label'       => 'Set up available OAuth Providers',
                'description' => 'Setup provider to be available across component',
                'category'    => 'Socialite',
                'url'         => Backend::url('kakuki/oauth2/settings'),
                'icon'        => 'icon-globe',
                'order'       => 400,
                'keywords'    => 'social log in with facebook, twitter, google, or github ...',
            ],
            'composer_providers' => [
                'label'       => 'Download OAuth Providers',
                'description' => 'Download an available Provider from Repository',
                'category'    => 'Socialite',
                'url'         => Backend::url('kakuki/oauth2/settings'),
                'icon'        => 'icon-download',
                'order'       => 400,
                'keywords'    => 'social log in with facebook, twitter, google, or github ...',
            ],
        ];
    }

    /**
     * Register Component
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Kakuki\OAuth2\Components\SocialLogin' => 'socialLogin',
        ];
    }

}
