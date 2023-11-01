<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function isAdmin(){
        $courier_arr = array('courier', 'courier_1', 'courier_2', 'courier_3', 'courier_4', 'courier_5', 'courier_6', 'courier_7');
        if($this->role === 'admin' || $this->role === 'office_1' || $this->role === 'office_ru' || $this->role === 'warehouse' || $this->role === 'office_agent_ru' || in_array($this->role, $courier_arr))
        {
            return true;
        } else{
            return false;
        }
    }


    public function isOffice_1(){
        if($this->role === 'admin' || $this->role === 'office_1')
        {
            return true;
        } else{
            return false;
        }
    }


    public function isChinaAdmin(){
        if($this->role === 'admin' || $this->role === 'china_admin' || $this->role === 'china_viewer' || $this->role === 'office_1')
        {
            return true;
        } else{
            return false;
        }
    }


    public function isPhilIndAdmin(){
        if($this->role === 'admin' || $this->role === 'office_1' || $this->role === 'office_eng' || $this->role === 'warehouse' || $this->role === 'viewer_eng' || $this->role === 'viewer' || $this->role === 'office_ind')
        {
            return true;
        } else{
            return false;
        }
    }


    public function isUser(){
        if($this->role === 'user')
        {
            return true;
        } else{
            return false;
        }
    }
}
