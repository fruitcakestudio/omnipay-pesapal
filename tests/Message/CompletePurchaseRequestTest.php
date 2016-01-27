<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $httpRequest = $this->getHttpRequest();
        $httpRequest->query->set('pesapal_merchant_reference', '001');
        $httpRequest->query->set('pesapal_transaction_tracking_id', 'abc');
        $httpRequest->query->set('pesapal_notification_type', 'CHANGE');

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'key' => 'my-key',
                'secret' => 'my-secret',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('001', $data['pesapal_merchant_reference']);
        $this->assertSame('abc', $data['pesapal_transaction_tracking_id']);

        $this->assertSame('abc', $this->request->getTransactionReference());
        $this->assertSame('001', $this->request->getTransactionId());
        $this->assertSame('CHANGE', $this->request->getNotificationType());
    }
}
