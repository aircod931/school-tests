<?php
namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "To nie jest Twoje aktualne hasło!"
     * )
     */
    public $oldPassword;

    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Hasło musi mieć przynajmniej 6 znaków!"
     * )
     */
    public $newPassword;
}