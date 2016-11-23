<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;
use JWTFactory;
use Carbon\Carbon;

class PasswordReset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     **/
    protected $table = 'password_resets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['email','token','created_at','expires_at'];

    /**
     * Sets wether to require time stamps.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * function to generate a token based
     * on the user
     *
     * @var array
     * @return string
     */
    public function createResetToken($user){

        $password_reset = $this->getPasswordReset($user);

        if (!$password_reset) {
            return $this->createToken($user);
        }
        return $this->regenerateToken($user);
    }


    protected function getToken($user) {
        $payload = JWTFactory::sub($user->id)->aud($user->email)->make();

        $token = JWTAuth::encode($payload);

        return $token;
    }

    /**
     * used to regenerate token in case user doesn't get initial token.
     *
     * @var User
     * @return string
     */
    private function regenerateToken($user)
    {
        $now = Carbon::now();

        $token = $this->getToken($user);

        $this->where('email', $user->email)->update([
            'token' => $token,
            'created_at' => $now,
            'expires_at' => Carbon::now()->addDay(1),
        ]);
        return $token;
    }

    /**
     * used to assign a generated token to a user.
     *
     * @var User
     * @return string
     */
    private function createToken($user)
    {
        $now = Carbon::now();

        $token = $this->getToken($user);

        $this->insert([
            'email' => $user->email,
            'token' => $token,
            'expires_at' => Carbon::now()->addDay(1),
            'created_at' => $now
        ]);
        return $token;
    }

    /**
     * Returns the activation model by token.
     *
     * @var User
     */
    public function getPasswordReset($user)
    {
        return $this->where('email', $user->email)->first();
    }

    /**
     * Returns the activation model by token.
     *
     * @var User
     */
    public function getResetByToken($token)
    {
        return $this->where('token', $token)->first();
    }

    /**
     * Deletes the token model.
     *
     * @var User
     */
    public function deletePasswordReset($token)
    {
        $this->where('token', $token)->delete();
    }

    public function user() {
        return $this->belongsTo('App\User');
    }


}
