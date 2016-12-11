<?php namespace Omnipay\Elavon\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

class ConvergeResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $redirect = false;
    protected $callbackUrl = '';

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;

        if (preg_match('/^\s*</s', $data)) {
            $this->redirect = true;
            $this->data = $data;
        } else {
            parse_str(implode('&', preg_split('/\n/', $data)), $this->data);
        }
    }

    public function isSuccessful()
    {
        return (isset($this->data['ssl_result']) && $this->data['ssl_result'] == 0);
    }

    public function isRedirect()
    {
        return $this->redirect;
    }


    public function getTransactionReference()
    {
        return (isset($this->data['ssl_txn_id'])) ? $this->data['ssl_txn_id'] : null;
    }

    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            return isset($this->data['errorMessage']) ? $this->data['errorMessage'] : null;
        }

        return isset($this->data['ssl_result_message']) ? $this->data['ssl_result_message'] : null;
    }

    public function getCode()
    {
        if (!$this->isSuccessful()) {
            return isset($this->data['errorCode']) ? $this->data['errorCode'] : null;
        }

        return $this->data['ssl_result'];
    }

    public function getCardToken()
    {
        return (isset($this->data['ssl_token'])) ? $this->data['ssl_token'] : null;
    }

    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
    }

    public function getRedirectUrl()
    {
        preg_match('/<form.+?action="(.*?)"/s', $this->data, $matches);

        return $matches[1];
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        preg_match_all('/<input type="hidden" name="(.+?)" value="(.+?)"/s', $this->data, $matches);
        $data = [];
        foreach ($matches[1] as $i => $key) {
            if ($key == 'TermUrl' && !empty($this->callbackUrl)) {
                $data[$key] = $this->callbackUrl;
                continue;
            }

            $data[$key] = $matches[2][$i];
        }

        return $data;
    }
}
