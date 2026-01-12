<?php
namespace toubilib\core\application\ports\api;

class AuthDTO {
    public string $ID;
    public string $email;
    public string $role;
    public string $access_token;
    public string $refresh_token;

    public function __construct(
        UserProfileDTO $profile, 
        string $access_token, 
        string $refresh_token
    ) {
        $this->ID = $profile->id;
        $this->email = $profile->email;
        $this->role = $profile->role;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
    }
}