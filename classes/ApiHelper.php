<?php

require_once(__DIR__ . "/../vendor/autoload.php");
require(__DIR__ . "/Parser.php");

class ApiHelper {

    protected $config;
    protected $crmClient;
    protected $parser;

    public function __construct($config) {

        $this->config = $config;

        $this->crmClient = new \RetailCrm\ApiClient(
            $this->config['retailcrm_url'],
            $this->config['retailcrm_apikey']
        );

        $this->parser = new Parser($config);
    }

    public function processXMLOrders() {
        $url = $this->config['tiu_xml_url'];
        $xml = simplexml_load_file($url);
        if (! $xml instanceof SimpleXMLElement) {
            $this->writeLog("ApiHelper::processXmlOrders: cannot get XML from $url");
            return false;
        }

        $orders = array();
        foreach($xml as $xmlOrder) {
            $order = $this->parser->parseXMLNewOrder($xmlOrder);
            if ($order) {
                $customerId = $this->checkCustomers($order);
                if ($customerId === false) {
                    echo "upload failed" . PHP_EOL;
                    return false;
                } elseif ($customerId !== 0) {
                    $order['customerId'] = $customerId;
                }
                $orders[$order['number']] = $order;
            }
        }
        $orders = $this->filterOrders($orders);
        if ($this->uploadOrders($orders)) {
            $a = sizeof($orders);
            echo "uploaded $a orders" . PHP_EOL;
            return true;
        } else {
            echo "upload failed" . PHP_EOL;
            return false;
        }
    }

    protected function uploadOrders($orders) {
        if ($orders === false)
            return false;
        if (sizeof($orders)) {
            $orders = array_chunk($orders, $this->config['retailcrm_order_chunk_size']);
            foreach ($orders as $chunk) {
                try {
                    $result = $this->crmClient->ordersUpload($chunk);
                } catch (\RetailCrm\Exception\CurlException $e) {
                    $this->writeLog(
                        '\Retailcrm\ApiClient::ordersUpload: ' . $e,
                        'error'
                    );
                    return false;
                }
                if (! $result->isSuccessful()) {
                    $this->writeLog(
                        '\Retailcrm\ApiClient::ordersUpload: ' . $result['errorMsg'] . (isset($result['errors']) ? ': ' . json_encode($result['errors']) : '')
                    );
                }
                time_nanosleep(0, 200000000);
            }
        }
        return true;
    }

    protected function checkCustomers($order) {
        $customerId = 0;
        if ($order['email'] != '')
            $filter = array('email' => $order['email']);
        if ($order['phone'] != '')
            $filter = array('name' => $order['phone']);
        if (isset($filter)) {
            try {
                $customers = $this->crmClient->customersList($filter);
            } catch (\RetailCrm\Exception\CurlException $e) {
                $this->writeLog(
                    '\Retailcrm\ApiClient::customersList: ' . $e,
                    'error'
                );
                return false;
            }
            if (isset($customers['customers']) && sizeof($customers['customers'] && isset($customers['customers'][0]['externalId']))) {
                $customerId = $customers['customers'][0]['externalId'];
            }
            time_nanosleep(0, 200000000);
        }
        return $customerId;
    }

    protected function writeLog($text, $type = null) {
        if (! file_exists(__DIR__ ."/../logs"))
            mkdir(__DIR__ ."/../logs");
        $date = date('Y-m-d H:i:s');
        file_put_contents(__DIR__ . "/../logs/error.log", "[$date]$text" . PHP_EOL, FILE_APPEND);
        if ($type == 'error') {
            $this->sendAlertEmail($text);
        }
    }

    protected function sendAlertEmail($text) {
        mail($this->config['support_email'], $this->config['support_email_subject'], $text);
    }

    protected function filterOrders($toUpload) {
        $numbers = array_keys($toUpload);
        if (date_create_from_format('Y-m-d H:i:s', $this->config['filter_date'])) {
            foreach ($toUpload as $i => $order) {
                if ($order['createdAt'] < $this->config['filter_date']) {
                    unset($toUpload[$i]);
                }
            }
        }

        $ordersListPage = 0;
        do {
            $ordersListPage++;
            try {
                $orders = $this->crmClient->ordersList(array(
                    'numbers' => $numbers
                ), $ordersListPage, 100);
            } catch (\RetailCrm\Exception\CurlException $e) {
                $text = '\Retailcrm\ApiClient::ordersList: ' . $e;
                $this->writeLog($text, 'error');
                return false;
            }
            if (isset($orders['orders']) && sizeof($orders['orders'])) {
                foreach ($orders['orders'] as $order) {
                    if (isset($toUpload[$order['number']]))
                        unset($toUpload[$order['number']]);
                }
            }
            time_nanosleep(0, 200000000);
        } while ($ordersListPage < $orders['pagination']['totalPageCount']);

        return $toUpload;
    }

}