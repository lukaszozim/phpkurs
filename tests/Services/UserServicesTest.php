<?php
namespace App\test\Services;

use App\DTO\UserDTO;
use App\Service\UserServices;
use App\Service\AddressService;
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

    // protected function setUp(): void
    // { 
    //     $userRepositoryMock = $this->createMock(UserRepository::class);
    //     $userCreatorMock = $this->createMock(UserCreationInterface::class);
    //     $addressRepositoryMock = $this->createMock(AddressRepository::class);
    //     $addressServiceMock = $this->createMock(AddressService::class);
    //     $validatorMock = $this->createMock(ValidatorInterface::class);
        
    //     static::$userService = new UserServices($userRepositoryMock,
    //         $userCreatorMock,
    //         $addressRepositoryMock,
    //         $addressServiceMock,
    //         $validatorMock
    //     );
    // }
    public function testShowTheTest()
    {
        $managerRegistryMock = self::createMock(ManagerRegistry::class);
        // $userRepositoryMock = $this->getMockBuilder(UserRepository::class)
        // ->setConstructorArgs($managerRegistry)
        // ->getMock();
        $userRepositoryMock = new UserRepository($managerRegistryMock);
        
        $userCreatorMock = $this->getMockBuilder(UserCreationInterface::class)->getMock();
        $addressRepositoryMock = new AddressRepository($managerRegistryMock);
        $userDTOMock = $this->createMock(UserDTO::class);
        $addressServiceMock = new AddressService($addressRepositoryMock, $userRepositoryMock, $userDTOMock);
        // $addressServiceMock = $this->getMockBuilder(AddressService::class)->setConstructorArgs($addressRepositoryMock, $userRepositoryMock, $userDTOMock)->getMock();

        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)->getMock();

        // $userServiceMock = new UserServices(
        //     $userRepositoryMock,
        //     $userCreatorMock,
        //     $addressRepositoryMock,
        //     $addressServiceMock,
        //     $validatorMock);
    
            // ->method('showTest')->willReturn('This is showTest!');
        $userServiceMock = $this->getMockBuilder(UserServices::class)->setConstructorArgs([
            $userRepositoryMock,
            $userCreatorMock,
            $addressRepositoryMock,
            $addressServiceMock,
            $validatorMock])->getMock();

        $userServiceMock->method('showTest')->willReturn('This is showTest!');
        // $userService = $this->getMockBuilder(UserServices::class)->setConstructorArgs(
        //     $userRepositoryMock,
        //     $userCreatorMock,
        //     $addressRepositoryMock,
        //     $addressServiceMock,
        //     $validatorMock)->getMock();
        
        

        $actual = $userServiceMock->showTest();
                // var_dump($actual);
        $expected = "This is showTest!";
        // $this->assertEquals($actual, $expected);
    }
}