<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ActivationServices
 *
 * @author amadosi
 */

namespace App\Services;

use App;
use App\Jobs\SendPasswordResetEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;


class PasswordServices {

    use DispatchesJobs;

    protected $password_reset;
    protected $resendAfter = 24;

    public function __construct(App\PasswordReset $password_reset) {
        $this->password_reset = $password_reset;
    }

    public function sendPasswordResetMail($user) {

        $token = $this->password_reset->createResetToken($user);

        $query_string = "?token=".$token."&action=password-reset&src=email&email=".$user->email."&uuid=".$user->uuid."";
        $link = route('user.password.reset');
        //append the query string to the link
        $link = $link.''.$query_string;

        //$message = sprintf('Click on the link to Activate your account <a href="%s">%s</a>', $link, $link);

        $emailContent = array(
            'email' => $user -> email,
            'url' => $link
        );

        //dispatch the password reset job
        $this->dispatch((new SendPasswordResetEmail($user,$emailContent))-> delay(60 * 10));

        return;
    }

    public function resetUserPassword($token,$password) {
        //first confirm if token has expired or not

        $password_reset = $this->password_reset->getResetByToken($token);

        if ($password_reset === null) {
            return null;
        }

        $user = App\User::whereEmail($password_reset->email);

        $user->password = bcrypt($password);

        $user->save();

        $this->password_reset->deletePasswordReset($token);

        return $user;
    }

}
