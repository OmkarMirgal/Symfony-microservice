<?php

namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Filter\Modifier\DateRangeMultiplier;
use App\Filter\Modifier\FixedPriceVoucher;
use App\Entity\Promotion;
use App\Filter\Modifier\EvenItemsMultiplier;
use App\Tests\ServiceTestCase;

class PriceModifiersTest extends ServiceTestCase
{

    /** @test */
    public function DateRangeMultiplier_returns_a_correctly_modified_price(): void
    {
        // given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setRequestDate('2022-11-27');

        $promotion = new Promotion(); 
        $promotion->setName('Black Friday half price sale');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["from" => "2022-11-25", "to" => "2022-11-28"]);
        $promotion->setType("date_range_multiplier");

        $dateRangeModifier = new DateRangeMultiplier();

        // when
        $modifiedPrice = $dateRangeModifier->modify(100, 5, $promotion, $enquiry);

        // then
        $this->assertEquals(250,$modifiedPrice);

    }

    /** @test */
    public function FixedPriceVoucher_returns_a_correctly_modified_price(): void
    {
        // given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode('OU812');

        $promotion = new Promotion(); 
        $promotion->setName('Voucher OU812');
        $promotion->setAdjustment(100);
        $promotion->setCriteria(["code" => "OU812"]);
        $promotion->setType("fixed_price_voucher");


        // when 
        $FixedPriceVoucher = new FixedPriceVoucher();
        $modifiedPrice = $FixedPriceVoucher->modify(150, 5, $promotion, $enquiry);
        // then
        $this->assertEquals(500,$modifiedPrice);
    }

    /** @test */
    public function EvenItemsMultiplier_returns_a_correctly_modified_price(): void
    {
        // given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);

        $promotion = new Promotion(); 
        $promotion->setName('Buy one get one free');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["minimum_quantity" => 2]);
        $promotion->setType("even_items__multiplier");
 
 
        $EvenItemsMultiplier = new EvenItemsMultiplier();
        // when 
        $modifiedPrice = $EvenItemsMultiplier->modify(100, 5, $promotion, $enquiry);
        // then
        $this->assertEquals(300,$modifiedPrice);
    }

    /** @test */
    public function EvenItemsMultiplier_returns_a_correctly_caluculates_alternatives(): void
    {
        // given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);

        $promotion = new Promotion(); 
        $promotion->setName('Buy one get one half price');
        $promotion->setAdjustment(0.75);
        $promotion->setCriteria(["minimum_quantity" => 2]);
        $promotion->setType("even_items__multiplier");
 
 
        $EvenItemsMultiplier = new EvenItemsMultiplier();
        // when 
        $modifiedPrice = $EvenItemsMultiplier->modify(100, 5, $promotion, $enquiry);
        
        // then
        // 300+100
        $this->assertEquals(400,$modifiedPrice);
    }

}

?>