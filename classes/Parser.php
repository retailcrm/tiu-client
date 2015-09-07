<?php

class Parser {

    const ENCODING = 'UTF-8';
    protected $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function parseXMLNewOrder(SimpleXMLElement $xml) {
        $order = $this->explodeFIO((string)$xml->name);

        foreach($xml->attributes() as $key => $val) {
            switch ($key) {
                case 'id':
                    $val = (string)$val;
                    $order['number'] = $this->config['order_prefix'] . $val;
                    break;
                case 'state':
                    $orderStatuses = array_flip($this->config['order_statuses']);
                    $order['status'] = $orderStatuses[(string)$val];
                    break;
            }
        }
        $createdAt = \DateTime::createFromFormat('d.m.y H:i', (string)$xml->date);
        $createdAt = $createdAt->format('Y-m-d H:i:s');
        if (isset($this->config['date_from']) && $createdAt < $this->config['date_from'])
            return false;

        $order = array_merge($order, array(
            'email' => (string)$xml->email,
            'phone' => (string)$xml->phone,
            'orderMethod' => $this->config['order_method'],
            'createdAt' => $createdAt,
            'paymentType' => $this->config['payment'][(string)$xml->paymentType],
            'delivery' => array(
                'address' => array(
                    'text' => (string)$xml->address
                ),
                'code' => $this->config['delivery'][(string)$xml->deliveryType]
            )
        ));
        $items = array();
        $xmlItems = $xml->items;
        foreach($xmlItems as $xmlItem) {
            $xmlItem = $xmlItem->item;
            $items[] = array(
                'productId' => (string)$xmlItem->external_id,
                'productName' => (string)$xmlItem->name,
                'quantity' => (string)$xmlItem->quantity,
                'initialPrice' => (string)$xmlItem->price
            );
        }
        $order['items'] = $items;
        return $order;
    }

    public function explodeFIO($name) {
        $name = explode(' ', $name, 3);
        $firstName = $lastName = $patronymic = '';
        switch (sizeof($name)) {
            case 1:
                $firstName = $name[0];
                break;
            case 2:
                $firstName = $name[0];
                $lastName = $name[1];
                break;
            case 3:
                $firstName = $name[1];
                $lastName = $name[0];
                $patronymic = $name[2];
                break;
        }
        return array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'patronymic' => $patronymic
        );
    }
}