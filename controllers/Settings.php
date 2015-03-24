<?php namespace Kakuki\OAuth2\Controllers;

use BackendMenu;
use Kakuki\OAuth2\Classes\ProviderManager;
use System\Classes\SettingsManager;
use Backend\Classes\Controller;
use Kakuki\OAuth2\Models\Setting;
use Parsedown;
use Flash;
use Lang;
use Exception;

/**
 * Settings Back-end Controller
 */
class Settings extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';


    public $providerAlias;
    protected $providerClass;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Kakuki.OAuth2', 'oauth2', 'providers');

        ProviderManager::createPartials();
    }

    protected function index_onLoadAddPopup()
    {
        try {
            $providers = ProviderManager::instance()->listProviders(true);
            $this->vars['providers'] = $providers;
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }


        return $this->makePartial('add_providers_form');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $itemId) {
                if (!$item = Setting::find($itemId)) {
                    continue;
                }

                $item->delete();

            }

            Flash::success('Successfully deleted those selected.');
        }

    }


    public function create($providerAlias)
    {
        ProviderManager::readme();
        try {
            $this->providerAlias = $providerAlias;
            $this->asExtension('FormController')->create();
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }
    }


}