<?php

use XoopsModules\Jobs\Listing;
use XoopsModules\Jobs\ListingHandler;

class ListingHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testInsert()
    {
        $dbMock = $this->createMock(\XoopsDatabase::class);
        $dbMock->expects($this->once())
            ->method('queryF')
            ->willReturn(true);

        $listingHandler = new ListingHandler($dbMock);

        $listing = $listingHandler->create();
        $listing->setVar('title', 'Test Job');
        $listing->setVar('cid', 1);

        $this->assertTrue($listingHandler->insert($listing));
    }
}
