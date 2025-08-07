<?php

use XoopsModules\Jobs\Resume;
use XoopsModules\Jobs\ResumeHandler;

class ResumeHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testInsert()
    {
        $dbMock = $this->createMock(\XoopsDatabase::class);
        $dbMock->expects($this->once())
            ->method('queryF')
            ->willReturn(true);

        $resumeHandler = new ResumeHandler($dbMock);

        $resume = $resumeHandler->create();
        $resume->setVar('name', 'John Doe');
        $resume->setVar('title', 'PHP Developer');
        $resume->setVar('cid', 1);

        $this->assertTrue($resumeHandler->insert($resume));
    }
}
