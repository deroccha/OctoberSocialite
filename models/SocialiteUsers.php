<?php namespace Kakuki\OAuth2\Models;

use Model;



/**
 * SocilatiteUsers Model
 */
class SocialiteUsers extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kakuki_oauth2_socialite_users';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'user' => ['\RainLab\User\Models\User']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}