<?php

namespace QuickPay\API;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Client::setBaseUri(QP_API_BASE_URI);
        $this->_client = new Client(QP_API_USER, QP_API_PASSWORD);
    }

    public function testGetNetsFees()
    {
        $result = $this->_client->getNetsFee(234,'dankort');
        $this->assertTrue(get_class($result) == 'stdClass');
        $this->assertEquals('dankort', $result->lockname);
        $this->assertEquals(234, $result->amount);
        $this->assertEquals(70, $result->fee);
        $this->assertEquals(304, $result->total);
    }

    public function testGetNetsStatus()
    {
        $result = $this->_client->getNetsStatus();
        $this->assertTrue(get_class($result) == 'stdClass');
    }
}