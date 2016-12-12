<?php namespace Omnipay\Elavon\Message;

class ConvergePurchaseRequest extends ConvergeAuthorizeRequest
{
    public function getData()
    {
        $this->transactionType = 'ccsale';

        if (!empty($this->getCurrency())) {
            $data = [
                'ssl_transaction_currency' => $this->getCurrency(),
                'ssl_txn_currency_code' => $this->getCurrency()
            ];
        } else {
            $data = [];
        }

        return array_merge(parent::getData(), $data);
    }
}
