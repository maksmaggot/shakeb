<?php
declare(strict_types=1);

namespace App\Security;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @return \App\Entity\User|UserInterface|null
     */
    public function loadUserByUsername($username)
    {
        return $this->userRepository->loadUserByUsername($username);
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException('Instances of ' . get_class($user) . ' are not supported.');
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
         return $class === User::class;
    }

}