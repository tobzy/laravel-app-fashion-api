<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;

class ApiController extends Controller {

    protected $statusCode = 200;
    protected $user;

    public function __construct() {
        if (JWTAuth::getToken()) {
            $this->user = $this->getAuthenticatedUser();
        }
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    protected function respondWithoutError($data) {

        $response = [
            'status' => true,
            'data' => $data,
        ];

        return response()->json($response);
    }

    protected function respondWithError($errorCode, $title, $errorMessage) {
        return response()->json([
                    'status' => false,
                    'errors' => [
                        'details' => [
                            'code' => $errorCode,
                            'title' => $title,
                            'message' => $errorMessage
                        ]
                    ]
        ]);
    }

    // somewhere in your controller
    private function getAuthenticatedUser() {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->respondWithError('ERR-USR-0002', 'user_not_found', 'The user can not be found or doesnt exist.');
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return $this->respondWithError('ERR-AUTH-0004', 'token_expired', $e->getMessage());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return $this->respondWithError('ERR-AUTH-0005', 'token_invalid', $e->getMessage());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return $this->respondWithError('ERR-AUTH-0006', 'token_absent', $e->getMessage());
        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }

}
