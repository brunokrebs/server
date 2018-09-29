<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
    	'name', 'description', 'image', 'user_sub'
    ];
    public function user()
    {
    	return $this->belongsTo('App\User');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public static function form()
    {
        return [
            'name' => '',
            'image' => '',
            'description' => ''
            
        ];
    }
}