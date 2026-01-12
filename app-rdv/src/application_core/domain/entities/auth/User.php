<?php
namespace toubilib\core\domain\entities\auth;

class User{
    private string $id;
    private string $email;
    private string $password;
    private string $role;

    public function __construct(
        $id,
        $email,
        $password,
        $role
    )
    {
        $this->id=$id;
        $this->email=$email;
        $this->password=$password;
        $this->role=$role;
    }

    public function getId() : string
    {
        return $this->id;
    }
    public function getEmail() : string
    {
        return $this->email;
    }
    public function getPassword() : string
    {
        return $this->password;
    }
    public function getRole() : string
    {
        return $this->role;
    }
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }



}















?>