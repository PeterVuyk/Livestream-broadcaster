<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     */
    public function removeUser(int $userId): void
    {
        $user = $this->getUserById($userId);
        if (!$user instanceof User) {
            throw UserNotFoundException::couldNotRemoveUser($userId);
        }
        $this->userRepository->remove($user);
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createUser(User $user): void
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $this->userRepository->save($user);
    }

    /**
     * @param int $userId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     */
    public function toggleDisablingUser(int $userId)
    {
        $user = $this->getUserById($userId);
        if (!$user instanceof User) {
            throw UserNotFoundException::couldNotToggleDisablingUser($userId);
        }
        $user->setEnabled(!$user->isEnabled());
        $this->updateUser($user);
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * @param int $userId
     * @return null|object|User
     */
    public function getUserById(int $userId): ?User
    {
        return $this->userRepository->findOneBy(['id' => $userId]);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): ?array
    {
        return $this->userRepository->findAll();
    }
}
