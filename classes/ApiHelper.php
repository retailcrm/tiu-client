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

                if ($customerId !== false) {
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
        $customerId = false;
        if ($order['email'] != '') $filter['email']= $order['email'];
        if ($order['phone'] != '') $filter['name'] = $order['phone'];

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

            if (!empty($customers['customers'])) {
                foreach ($customers as $_customer) {
                    if (!empty($_customer['externalId'])) {
                        $customerId = $_customer['externalId'];
                        break;
                    }
                }
            }

        }

        return $customerId;
    }

    protected function writeLog($text, $type = null) {
        if (! file_exists(__DIR__ ."/../logs")) mkdir(__DIR__ ."/../logs");
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
        if (date_create_from_format('Y-m-d H:i:s', $this->config['date_from'])) {
            foreach ($toUpload as $i => $order) {
                if ($order['createdAt'] < $this->config['date_from']) {
                    unset($toUpload[$i]);
                }
            }
        }

        return $toUpload;
    }

}
