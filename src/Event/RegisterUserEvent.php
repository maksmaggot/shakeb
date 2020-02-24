<?php
/**
 * Created by PhpStorm.
 * User: maggotik
 * Date: 2/24/20
 * Time: 11:34 PM
 */

namespace App\Event;



use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class RegisterUserEvent extends Event
{
    const NAME = 'user.register';

    /**
     * @var User
     */
    private $registeredUser;

    /**
     * RegisterUserEvent constructor.
     * @param User $registeredUser
     */
    public function __construct(User $registeredUser)
    {

        $this->registeredUser = $registeredUser;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return $this->registeredUser;
    }
}