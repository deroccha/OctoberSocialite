<?php namespace Kakuki\OAuth2\Models;

use Model;

/**
 * Provider Model
 */
class Provider extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'kakuki_oauth2_providers';

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
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}