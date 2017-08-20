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
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;


class ActivationServices {

    protected $mailer;
    protected $activation;
    protected $resendAfter = 24;

    public function __construct(Mailer $mailer, App\Activation $activation) {
        $this->mailer = $mailer;
        $this->activation = $activation;
    }

    public function sendActivationMail($user) {

//        if ($user->confirmed || !$this->shouldSend($user)) {
        if ($user->confirmed) {
            return;
        }

        $token = $this->activation->createActivation($user);

        $link = route('user.activate', $token);
        //$message = sprintf('Click on the link to Activate your account <a href="%s">%s</a>', $link, $link);

        $emailContent = array(
            'email' => $user -> email,
            'url' => $link
        );
        
        $this->mailer->send('emails.confirmation',$emailContent, function (Message $message) use ($user) {
            $message->to($user->email)->subject('Activation mail');
        });
        return;
    }

    public function activateUser($token) {
        $activation = $this->activation->getActivationByToken($token);

        if ($activation == null) {
            return null;
        }

        $user = App\User::find($activation->user_id);

        $user->confirmation = true;

        $user->save();

        $this->activation->deleteActivation($token);

        return $user;
    }

    private function shouldSend($user) {
        $activation = $this->activation->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }

}
