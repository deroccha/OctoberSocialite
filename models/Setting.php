<?php namespace Kakuki\OAuth2\Models;

use Model;
use Parsedown;

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
     * @var array Guarded fields
     */
    protected $guarded = ['config_data'];


    protected $jsonable = ['config_data'];
    /**
     * @var array Fillable fields
     */
    protected $fillable = ['client_id', 'client_secret'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];



    public function afterFetch()
    {

    }


    public function beforeValidate()
    {

    }
}