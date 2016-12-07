<?php

namespace App\Services;

use App;
use App\SocialUser as ProviderUser;
use Ramsey\Uuid\Uuid;

class SocialAccountService {

    public function createOrGetUser(ProviderUser $providerUser) {
        $account = App\SocialAccount::whereProvider($providerUser ->getProvider())
                ->whereProviderUserId($providerUser->getId())
                ->first();

        if ($account) {
            return $account->user;
        } else {

            $account = new App\SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $providerUser ->getProvider()
            ]);

            $user = App\User::whereEmail($providerUser->getEmail())->first();

            error_log('The returning email is'.$providerUser->getEmail());
            
            if (!$user) {

                $user = App\User::create([
                            'email' => $providerUser->getEmail(),
                            'first_name' => $this->getName($providerUser->getName(), 'firstname'),
                            'last_name' => $this->getName($providerUser->getName(), 'lastname'),
                            'confirmation' => 1,
                ]);

                //develop a uuid from the id of the user.
                $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $user->id . '.com');
                $user->uuid = $uuid5;
                $user->save();
            }

            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }

    private function getName($fullname, $index) {
        $nameArray = explode(' ', $fullname);
        switch ($index) {
            case 'firstname':
                return $nameArray[0];
            case 'lastname':
                return $nameArray[count($nameArray) - 1];
            default:
                return null;
        }
    }

}
