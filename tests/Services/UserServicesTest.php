<?php

namespace App\test\Services;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Service\UserServices;
use App\Service\AddressService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use App\Interfaces\UserCreationInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserServicesTest extends TestCase
{
    //fixture of this class
    // protected static $userService;
    /**
     * @var UserServices
     */
    private UserServices $userService;

    private UserRepository|MockObject $userRepositoryMock;

    private UserCreationInterface|MockObject $userCreatorMock;

    private AddressService|MockObject $addressServiceMock;

    private ValidatorInterface|MockObject $validatorMock;


    protected function setUp(): void
    {

        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->userCreatorMock = $this->createMock(UserCreationInterface::class);
        $this->addressRepositoryMock = $this->createMock(AddressRepository::class);
        $this->addressServiceMock = $this->createMock(AddressService::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);


        $this->userService = new UserServices(
            $this->userRepositoryMock,
            $this->userCreatorMock,
            $this->addressRepositoryMock,
            $this->addressServiceMock,
            $this->validatorMock
        );
    }

    public function testShowTheTest()
    {
        $actual = $this->userService->showTest();
        $this->assertEquals("This is showTest!", $actual);

    }

    public function testGetAllUsers()
    {
        $user = new User();
        $user->setFirstName('Kris');
        $user->setLastName('Smith');
        $user->setEmail('s@s.pl');
        $user->setPassword('jdksjdsldjs');

        /**
         * @var UserRepository|MockObject $userRepositoryMock
         */
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->expects(self::once())->method('findAll')->willReturn([$user]);
        $userService = new UserServices(
            $userRepositoryMock,
            $this->userCreatorMock,
            $this->addressRepositoryMock,
            $this->addressServiceMock,
            $this->validatorMock);
        $actual = $userService->getAllUsers();
        $this->assertIsArray($actual);
    }

}