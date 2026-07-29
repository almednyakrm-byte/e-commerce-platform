<?php

namespace App\Tests;

use App\Auth\AuthService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TestAuth extends TestCase
{
    private $authService;
    private $session;
    private $entityManagerMock;
    private $userRepositoryMock;

    protected function setUp(): void
    {
        $this->session = new Session();
        $this->entityManagerMock = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $this->userRepositoryMock = $this->createMock('App\Repository\UserRepository');
        $this->authService = new AuthService($this->entityManagerMock, $this->userRepositoryMock);
    }

    public function testLoginSuccess()
    {
        // Arrange
        $username = 'test_user';
        $password = 'test_password';
        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())
            ->method('getCredentials')
            ->willReturn($username);

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByUsername')
            ->with($username)
            ->willReturn($this->createMock(UserInterface::class));

        // Act
        $this->authService->login($username, $password, $this->session);

        // Assert
        $this->assertTrue($this->session->has('user'));
    }

    public function testLoginFailure()
    {
        // Arrange
        $username = 'test_user';
        $password = 'test_password';
        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())
            ->method('getCredentials')
            ->willReturn($username);

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByUsername')
            ->with($username)
            ->willReturn(null);

        // Act
        $this->authService->login($username, $password, $this->session);

        // Assert
        $this->assertFalse($this->session->has('user'));
    }

    public function testRegisterSuccess()
    {
        // Arrange
        $username = 'test_user';
        $email = 'test_email@example.com';
        $password = 'test_password';

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByUsername')
            ->with($username)
            ->willReturn(null);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->userRepositoryMock->findOneByUsername($username));

        $this->entityManagerMock->expects($this->once())
            ->method('flush')
            ->willReturn(true);

        // Act
        $this->authService->register($username, $email, $password, $this->session);

        // Assert
        $this->assertTrue($this->session->has('user'));
    }

    public function testRegisterFailure()
    {
        // Arrange
        $username = 'test_user';
        $email = 'test_email@example.com';
        $password = 'test_password';

        $this->userRepositoryMock->expects($this->once())
            ->method('findOneByUsername')
            ->with($username)
            ->willReturn($this->createMock(UserInterface::class));

        // Act
        $this->authService->register($username, $email, $password, $this->session);

        // Assert
        $this->assertFalse($this->session->has('user'));
    }
}


This test file covers the following scenarios:

- `testLoginSuccess`: Tests successful login with a valid username and password.
- `testLoginFailure`: Tests failed login with an invalid username or password.
- `testRegisterSuccess`: Tests successful registration with a new user.
- `testRegisterFailure`: Tests failed registration with an existing username.

Each test method uses PHPUnit's mocking capabilities to isolate the dependencies of the `AuthService` class and focus on the behavior being tested. The assertions are made using `assertTrue` and `assertFalse` to verify the expected outcome of each test scenario.