<?php

namespace App\Controller;

use App\Cache\PromotionCache;
use App\DTO\LowestPriceEnquiry;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController

{
    public function __construct(
        private ProductRepository $repository,
        private EntityManagerInterface $entityManager
    )
    {
        
    }

    #[Route('/products/{id}/lowest-price',name:'lowest-price', methods: 'POST')]
    public function lowestPrice(
        Request $request,
        int $id,
        DTOSerializer $serializer,
        PromotionsFilterInterface $promotionsFilter,
        PromotionCache $promotionCache
        ): Response {

        $lowestPriceEnquiry = $serializer->deserialize($request->getContent(), LowestPriceEnquiry::class, 'json');

        $product = $this->repository->findOrFail($id);

        $lowestPriceEnquiry->setProduct($product);

        $promotions = $promotionCache->findValidForProduct($product,$lowestPriceEnquiry->getRequestDate());

        $modifiedEnquiry = $promotionsFilter->apply($lowestPriceEnquiry,...$promotions);

        //while using DtoSerializer(custom serializer) 
        $responseContent = $serializer->serialize($modifiedEnquiry,'json');
        // return new Response($responseContent,200,['Content-Type' => 'application/json']);
        return new JsonResponse(data: $responseContent, status: Response::HTTP_OK, json: true );

        // return new JsonResponse($lowestPriceEnquiry,200); -- if using the inbuilt serializerInterface directly
    }
}
