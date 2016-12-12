<?php
namespace Omnipay\Elavon\Message;

class ConvergeVerifyPaymentRequest extends ConvergeAbstractRequest
{
    public function getData()
    {
        return [
            'MD' => $this->getMD(),
            'PaRes' => $this->getPaRes()
        ];
    }

    public function setMD($md)
    {
        return $this->setParameter('MD', $md);
    }

    public function getMD()
    {
        return $this->getParameter('MD');
    }

    public function setPaRes($paRes)
    {
        return $this->setParameter('PaRes', $paRes);
    }

    public function getPaRes()
    {
        return $this->getParameter('PaRes');
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint() . '/process.do?dispatchMethod=processVerify', null, http_build_query($data))
            ->setHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->send();

        return $this->createResponse($httpResponse->getBody(true));
    }
}