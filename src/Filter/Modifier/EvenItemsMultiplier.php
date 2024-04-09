<?php 

namespace App\Filter\Modifier;

use App\DTO\PromotionEnquiryInterface;
use App\Entity\Promotion;

class EvenItemsMultiplier implements PriceModifierInterface
{
    public function modify(int $price, int $quantity, Promotion $promotion, PromotionEnquiryInterface $enquiry): int
    {
    //    if(!($enquiry->getQuantity() >= $promotion->getCriteria()["minimum_quantity"])){
    //         return $price * $quantity;
    //    }
    //    return $price * (ceil($quantity/$promotion->getCriteria()["minimum_quantity"]));

        if($quantity < 2) {
            return $price * $quantity;
        }

        $oddCount = $quantity % 2;
        $evenCount = $quantity - $oddCount;
        
        return (($price * $evenCount) * $promotion->getAdjustment()) + ($oddCount * $price); 

    }

}

?>