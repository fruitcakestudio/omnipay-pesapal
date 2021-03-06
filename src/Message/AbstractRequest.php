<?php

namespace Omnipay\Pesapal\Message;

use Eher\OAuth\Consumer;
use Eher\OAuth\HmacSha1;
use Eher\OAuth\Request;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $liveEndpoint = 'https://www.pesapal.com/API/';
    protected $testEndpoint = 'http://demo.pesapal.com/API/';
    protected $resource;

    public function getKey()
    {
        return $this->getParameter('key');
    }

    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getType()
    {
        return $this->getParameter('type');
    }

    public function setType($value)
    {
        return $this->setParameter('type', $value);
    }

    public function getDescription()
    {
        return $this->getParameter('description');
    }

    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

    public function sendData($data)
    {
        $url = $this->createSignedUrl($data);

        $response = $this->httpClient->get($url)->send();

        return $this->createResponse($response->getBody(true));
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    abstract protected function createResponse($data);

    protected function createSignedUrl($params = array())
    {
        $url = $this->getEndpoint(). $this->resource;

        // Generate signature
        $consumer = new Consumer($this->getKey(), $this->getSecret());
        $request = Request::from_consumer_and_token($consumer, null, 'GET', $url, $params);
        $request->sign_request(new HmacSha1(), $consumer, null);

        return $request->to_url();
    }
}
