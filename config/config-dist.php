<?php
$сonfig = array(
    /**
     * xml link (like https://my.tiu.ru/cabinet/export_orders/xml/1234567?hash_tag=472g8fzb1af38d35f2c521dc8a1e1208)
     * can be taken here: https://my.tiu.ru/cabinet/order/export_orders
     */
    'tiu_xml_url' => '',

    'retailcrm_url' => 'https://demo.retailcrm.ru',
    'retailcrm_apikey' => 'apiKeyValue',
    'retailcrm_order_chunk_size' => 50,

    /**
     * upload orders only from a certain date (Y-m-d H:i:s)
     */
    'date_from' => '',

    'order_prefix' => '', // optional
    'set_external_ids' => false, // set externalId field
    'order_method'  => 'shopping-cart',

    /**
     * delivery mapping (tiu => CRM)
     * https://my.tiu.ru/cabinet/shop_settings/delivery_options
     */
    'delivery' => array(
        'Транспортная компания' => 'ems',
        'Доставка курьером' => 'courier',
        'Самовывоз' => 'self-delivery'
    ),

    /**
     * payment types mapping (tiu => CRM)
     * https://my.tiu.ru/cabinet/shop_settings/payment_options
     */
    'payment' => array(
        'Наличными' => 'cash',
        'Оплата банковской картой' => 'bank-card',
        'Безналичный расчет' => 'bank-transfer'
    ),

    /**
     * order statuses (CRM => tiu)
     * https://my.tiu.ru/cabinet/order_v2
     */
    'order_statuses' => array(
        'new' => 'opened',
        'processing' => 'accepted',
        'complete' => 'closed',
        'cancel-other' => 'declined',
    ),

    /**
     * email address for notification
     */
    'support_email' => 'support@example.org',
    'support_email_subject' => 'Integration problem'
);
