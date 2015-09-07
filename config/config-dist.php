<?php
$сonfig = array(
    // ссылка вида https://my.tiu.ru/cabinet/export_orders/xml/1234567?hash_tag=472g8fzb1af38d35f2c521dc8a1e1208
    // берется отсюда: https://my.tiu.ru/cabinet/order/export_orders
    'tiu_xml_url' => '',

    'retailcrm_url' => 'https://site.retailcrm.ru',
    'retailcrm_apikey' => 'qwerty123',
    'retailcrm_order_chunk_size' => 50,

    // загружать заказы только с определенной даты (Y-m-d H:i:s)
    'date_from' => '',

    'order_prefix' => 'TIU', // приписывается к номеру заказа в CRM
    'set_external_ids' => false, // задавать ли externalId заказам
    'order_method'  => 'tiu', // способ оформления заказа
    // соответствие доставок tiu => CRM
    // https://my.tiu.ru/cabinet/shop_settings/delivery_options
    'delivery' => array(
        'Транспортная компания' => 'courier',
        'Доставка курьером' => 'courier',
        'Самовывоз' => 'courier'
    ),

    // соответствие оплат tiu => CRM
    // https://my.tiu.ru/cabinet/shop_settings/payment_options
    'payment' => array(
        'Наличными' => 'cash',
        'Оплата банковской картой' => 'bank-card',
        'Безналичный расчет' => 'bank-transfer'
    ),

    // статусы заказов CRM => tiu
    // https://my.tiu.ru/cabinet/order_v2
    // по умолчанию заданы 4 статуса:
    // 'opened' - новый, 'accepted' - принят, 'declined' - отменен, 'closed' - выполнен
    'order_statuses' => array(
        'new' => 'opened',
        'processing' => 'accepted',
        'complete' => 'closed',
        'cancel-other' => 'declined',
    ),

    // почта для логов с ошибками
    'support_email' => '',
    'support_email_subject' => 'tiu fail'
);