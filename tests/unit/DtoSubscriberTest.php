<?php
namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Event\AfterDtoCreatedEvent;
use App\EventSubscriber\DtoSubscriber;
use App\Service\ServiceException;
use App\Tests\ServiceTestCase;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DtoSubscriberTest extends ServiceTestCase
{
    /** @test */
    public function testEventSubscription(): void 
    {
        $this->assertArrayHasKey(AfterDtoCreatedEvent::NAME, DtoSubscriber::getSubscribedEvents());
    }


    /** @test */
    public function testValidateDto(): void
    {
        $dto = new LowestPriceEnquiry();
        $dto->setQuantity(-5);
        $event = new AfterDtoCreatedEvent($dto);
        $dispatcher = $this->container->get(EventDispatcherInterface::class);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('ConstraintViolationList');
    
        $dispatcher->dispatch($event, $event::NAME);
    }

}

?>