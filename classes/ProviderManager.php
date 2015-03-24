<?php namespace Kakuki\OAuth2\Classes;

use Cms\Classes\Partial;
use System\Classes\PluginManager;
use Kakuki\OAuth2\Models\Setting;

class ProviderManager {

    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var array List of registered providers.
     */
    private $providers;


    /**
     * @var System\Classes\PluginManager
     */
    protected $pluginManager;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
        $this->providers = $this->loadProviders();
    }


    /**
     * Loops over each payment type and ensures the editing theme has a payment form partial,
     * if the partial does not exist, it will create one.
     * @return void
     */
    public static function createPartials()
    {
        $partials = Partial::lists('baseFileName', 'baseFileName');

    }

    /**
     * Loads the hardcoded available Providers across Socialite
     * @return void
     */
    protected function loadProviders()
    {
        return [
            'Bitbucket',
            'Facebook',
            'Google',
            'GitHub',
            'Linkedin',
            'Twitter',
            'Xing',

        ];
    }

    /**
     * Returns a list of Providers which has not been defined to choose from
     */

    public function listProviders()
    {

        return $this->providers = array_diff( $this->providers, $this->registeredProviders() );
    }

    /*
     * Return from Model all defined Providers
     */

    public function registeredProviders(){

        return Setting::lists('provider');
    }

}