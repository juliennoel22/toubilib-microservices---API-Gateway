<?php
namespace toubilib\core\application\ports\api;



class UserProfileDTO{
    public string $id;
    public string $email;
    public string $role;

    public function __construct(
        $id,
        $email,
        $role
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
        
    }
}