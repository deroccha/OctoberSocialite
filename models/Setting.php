<?php namespace Kakuki\OAuth2\Models;

use Model;

/**
 * Setting Model
 */
class Setting extends Model
{

    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'client_id' => 'required',
        'client_secret' => 'required',
    ];


    public $hidden = ['provider'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kakuki_oauth2_settings';

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['client_id', 'client_secret'];

}