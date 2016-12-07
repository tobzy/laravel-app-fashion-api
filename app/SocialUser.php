<?php

namespace App;

use Illuminate\Http\Request;

class SocialUser
{
    private $id;
    private $name;
    private $email;
    private $provider;
    
    public function __construct(Request $request) {
        
        $this->setId($request ->input('provider_user_id'));
        $this->setEmail( $request ->input('email'));
        $this->setName( $request ->input('name'));
        $this->setProvider($request ->input('provider'));
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getProvider() {
        return $this->provider;
    }

    private function setId($id) {
        $this->id = $id;
        return $this;
    }

    private function setName($name) {
        $this->name = $name;
        return $this;
    }

    private function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    private function setProvider($provider) {
        $this->provider = $provider;
        return $this;
    }


}
