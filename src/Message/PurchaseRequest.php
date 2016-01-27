<?php
namespace Omnipay\Pesapal\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Purchase Request
 *
 * @method PurchaseResponse send()
 */
class PurchaseRequest extends AbstractRequest
{

    function getResource()
    {
        return 'PostPesapalDirectOrderV4';
    }

    public function sendData($data)
    {
        $xml = new \SimpleXMLElement('<PesapalDirectOrderInfo
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://www.pesapal.com"
/>');
        foreach ($data as $key => $value) {
            $xml->addAttribute($key, $value);
        }

        $url = $this->createSignedUrl(array(
            'pesapal_request_data' => $xml->asXML(),
            'oauth_callback' => $this->getReturnUrl(),
            ));

        return $this->createResponse($url);
    }

    public function getData()
    {
        $this->validate('key', 'secret', 'amount', 'card');
        $card = $this->getCard();

        // Either phone or email is required
        if ( !$card->getEmail() && ! $card->getPhone()) {
            throw new InvalidRequestException("A phonenumber or email is required");
        }

        $data = array(
            'Amount' => $this->getAmount(),
            'Description' => $this->getDescription(),
            'Type' => $this->getType(),
        );

        if ($this->getCurrency()) {
            $data['Currency'] = $this->getCurrency();
        }

        if ($card->getEmail()) {
            $data['Email'] = $this->getCard()->getEmail();
        }
        if ($card->getPhone()) {
            $data['PhoneNumber'] = $this->getCard()->getPhone();
        }

        if ($card->getFirstName()){
            $data['FirstName'] = $card->getFirstName();
        }

        if ($card->getLastName()) {
            $data['LastName'] = $card->getLastName();
        }

        if ( ! $this->getTransactionReference()) {
            // Create an unique reference based on the data + time
            $reference = sha1(uniqid(serialize($data), true));
            $this->setTransactionReference($reference);
        }

        $data['Reference'] = $this->getTransactionReference();

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
