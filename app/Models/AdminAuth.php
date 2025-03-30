<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminAuth extends Authenticatable implements JWTSubject
{
  //
  
  protected $fillable = [
    'email', 'password', 'password_str', 'utype'
  ];
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }
  public function getJWTCustomClaims()
  {
    return [];
  }
  
  public function getRememberToken()
  {
    return null; // not supported
  }

  public function setRememberToken($value)
  {
    // not supported
  }

  public function getRememberTokenName()
  {
    return null; // not supported
  }

  /**
  * Overrides the method to ignore the remember token.
  */
  public function setAttribute($key, $value)
  {
    $isRememberTokenAttribute = $key == $this->getRememberTokenName();
    if (!$isRememberTokenAttribute)
    {
      parent::setAttribute($key, $value);
    }
  }
}
