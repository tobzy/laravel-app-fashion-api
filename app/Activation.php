<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activation extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_activations';
    
    /**
     * Sets wether to require time stamps.
     *
     * @var boolean
     */
    public $timestamps = false;


    /**
     * generates a token from hash and sha.
     *
     * @var none
     * @return string
     */
    
    protected function getToken() {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    /**
     * creates an activation for the given user.
     *
     * @var User
     * @return string
     */
    public function createActivation($user) {

        $activation = $this->getActivation($user);

        if (!$activation) {
            return $this->createToken($user);
        }
        return $this->regenerateToken($user);
    }

    /**
     * used to regenerate token incase user doesnt get initial token.
     *
     * @var User
     * @return string
     */
    private function regenerateToken($user)
    {

        $token = $this->getToken();
        $this->where('user_id', $user->id)->update([
            'token' => $token,
            'created_at' => new Carbon()
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
        $token = $this->getToken();
        $this->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => new Carbon()
        ]);
        return $token;
    }
    
    /**
     * Returns the activation model by token.
     *
     * @var User
     */
    public function getActivation($user)
    {
        return $this->where('user_id', $user->id)->first();
    }
    
    /**
     * Returns the activation model by token.
     *
     * @var User
     */
    public function getActivationByToken($token)
    {
        return $this->where('token', $token)->first();
    }
    
    /**
     * Deletes the token model.
     *
     * @var User
     */
    public function deleteActivation($token)
    {
        $this->where('token', $token)->delete();
    }
    
    public function user() {
        return $this->belongsTo('App\User');
    }

}
