<?php namespace Kakuki\OAuth2;

use App;
use Illuminate\Foundation\AliasLoader;
use Laravel\Socialite\Facades\Socialite;
use System\Classes\PluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Backend;
/**
 * OAuth2 Plugin Information File
 */
class Plugin extends PluginBase {


    /**
     * Set required dependencies
     *
     * plugin /RainLab/User
     */

    public $require = [
        'RainLab.User'
    ];


    /**
     * Returns information about this plugin.
     *
     * @return array
     */

	public function pluginDetails() {
		return [
			'name' => 'Laravel Socialite',
			'description' => 'Log in User with OAuth2',
			'author' => 'Kakuki',
			'icon' => 'icon-leaf',
		];
	}


	public function boot() {
        $alias = AliasLoader::getInstance();
        $alias->alias('SocialiteFactory', App::make('Laravel\Socialite\Contracts\Factory'));

	}

    /**
     * Register service provider and alias facade.
     */
    public function register(){

        App::register('Laravel\Socialite\SocialiteServiceProvider');
        // Register alias
        $alias = AliasLoader::getInstance();
        $alias->alias('Socialite', 'Laravel\Socialite\Facades\Socialite');

    }

	public function registerSettings() {
		return [
			'api_credentials' => [
				'label' => 'OAuth2 Credentials',
				'description' => 'Manage OAuth2 API credentials for Socialite Login',
				'category' => 'Socialite',
				'icon' => 'icon-cog',
				'class' => 'Kakuki\OAuth2\Models\Settings',
				'order' => 500,
				'keywords' => 'social log in with facebook, twitter, google, or github',
			],
            'providers' => [
                'label' => 'Add Provider',
                'description' => 'Setup a provider to be accessable across component',
                'category' => 'Socialite',
                'url'         => Backend::url('kakuki/oauth2/settings'),
                'icon' => 'icon-globe',
                'order' => 400,
                'keywords' => 'social log in with facebook, twitter, google, or github',
            ],
		];
	}

	public function registerComponents() {
		return [
			'Kakuki\OAuth2\Components\SocialLogin' => 'socialLogin',
		];
	}


    /**
     * Return providers implemented in this plugin.
     * Hardcoded variant !!!
     * TODO: logic to register Socialite Providers on the flow
     */

    public function registerSocialiteProviders()
    {

    }

}
