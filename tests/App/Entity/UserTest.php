<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\User
 * @covers ::<!public>
 * @uses \App\Entity\User
 */
class UserTest extends TestCase
{
    /**
     * @covers ::getUsername
     * @covers ::setUsername
     */
    public function testUserName()
    {
        $user = new User();
        $user->setUsername('userName');
        $this->assertSame('userName', $user->getUsername());
    }

    /**
     * @covers ::getEmail
     * @covers ::setEmail
     */
    public function testEmail()
    {
        $user = new User();
        $user->setEmail('some@mail.nl');
        $this->assertSame('some@mail.nl', $user->getEmail());
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testId()
    {
        $user = new User();
        $user->setId(3);
        $this->assertSame(3, $user->getId());
    }

    /**
     * @covers ::isActive
     * @covers ::setActive
     */
    public function testIsActive()
    {
        $user = new User();
        $user->setActive(true);
        $this->assertSame(true, $user->isActive());
    }

    /**
     * @covers ::getPassword
     * @covers ::setPassword
     */
    public function testPassword()
    {
        $user = new User();
        $user->setPassword('very-secret-password');
        $this->assertSame('very-secret-password', $user->getPassword());
    }

    /**
     * @covers ::getPlainPassword
     * @covers ::setPlainPassword
     */
    public function testPlainPassword()
    {
        $user = new User();
        $user->setPlainPassword('plain!');
        $this->assertSame('plain!', $user->getPlainPassword());
    }

    /**
     * @covers ::getRoles
     * @covers ::setRoles
     */
    public function testRoles()
    {
        $user = new User();
        $user->setRoles('ROLE_USER');
        $this->assertSame('ROLE_USER', $user->getRoles());
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $this->assertSame(
            'a:4:{i:0;i:3;i:1;s:8:"userName";i:2;s:12:"some@mail.nl";i:3;s:4:"hash";}',
            $this->getUser()->serialize()
        );
    }

    /**
     * @covers ::unserialize
     */
    public function testUnserialize()
    {
        $user = new User();
        $user->unserialize('a:4:{i:0;i:3;i:1;s:8:"userName";i:2;s:12:"some@mail.nl";i:3;s:4:"hash";}');
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::getSalt
     */
    public function testGetSalt()
    {
        $user = new User();
        $this->assertNull($user->getSalt()); //When using BCrypt, salt is not needed because it salt its internally.
    }

    /**
     * @covers ::eraseCredentials
     */
    public function testEraseCredentials()
    {
        $user = new User();
        $user->eraseCredentials(); //This function is not needed but required by the interface.
        $this->addToAssertionCount(1);
    }

    /**
     * @return User
     */
    private function getUser()
    {
        $user = new User();
        $user->setEmail('some@mail.nl');
        $user->setId(3);
        $user->setActive(true);
        $user->setPassword('hash');
        $user->setPlainPassword('plain-password');
        $user->setRoles('ROLE_USER');
        $user->setUsername('userName');
        return $user;
    }
}