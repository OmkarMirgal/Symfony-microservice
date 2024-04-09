<?php

namespace App\Service\Serializer;

use App\Event\AfterDtoCreatedEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DTOSerializer implements SerializerInterface {

    private SerializerInterface $serializer;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct (EventDispatcherInterface $eventDispatcher) {
        
        $this->eventDispatcher = $eventDispatcher;

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $this->serializer = new Serializer( 
            //normalizers
            [new ObjectNormalizer($classMetadataFactory,nameConverter: new CamelCaseToSnakeCaseNameConverter())],
            //encoders
            [new JsonEncoder()]
        );
    }

    public function serialize(mixed $data, string $format, array $context = []): string 
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        $dto = $this->serializer->deserialize($data, $type, $format, $context);

        //Dispatch an after dto created event
        $event  = new AfterDtoCreatedEvent($dto);
        
        //listeners
        $this->eventDispatcher->dispatch($event,$event::NAME);

        return $dto;
    }
}

// {# Notes: About seriazation

//     Desializaton works like : 
//     1. (any format) to Array[Encoding] 
//     2. Array to Objects [denormalize]
    
//     and vice versa for serialization. #}

?>



