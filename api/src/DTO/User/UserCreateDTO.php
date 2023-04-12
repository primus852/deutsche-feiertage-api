<?php

namespace App\DTO\User;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserCreateDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['write', 'read'])]
    public string $email;

    #[Groups(['write', 'read'])]
    public array $roles;

    #[Assert\NotBlank]
    #[Groups(['write'])]
    public string $password;
}
