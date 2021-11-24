<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Controller\Checkout;

use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PlaceOrderTest extends TestCase
{
    /** @var Quote|MockObject */
    private $quote;

    protected function setUp(): void
    {
        $this->quote = $this->createMock(Quote::class);
        $this->quote
            ->expects($this->any())
            ->method('hasItems')
            ->willReturn(true);
    }

    public function testQuoteHasItems()
    {
        $this->assertTrue($this->quote->hasItems());
    }
}
