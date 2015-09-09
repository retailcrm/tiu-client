# tiu-client

Скрипт выгрузки заказов из TIU.ru в [retailCRM](http://retailcrm.ru) через [REST API](http://retailcrm.ru/docs/Разработчики/СправочникМетодовAPIV3)

##Скрипт позволяет:
 * Выгружать заказы из tiu.ru в RetailCRM

##Что такое TIU
Это торговый центр в интернете, федеральная торговая площадка с конструктором сайтов и интернет-магазинов

##Установка

####1) Выполнить команды
```sh
git clone https://github.com/retailcrm/tiu-client.git
cd tiu-client
curl -sS https://getcomposer.org/installer | php
php composer.phar require retailcrm/api-client-php ~3.0.0
```

####2) Отредактировать /config/config.php аналогично примеру /config/config-dist.php

##Использование

####Выгрузка заказов в CRM:
Выполнить команду:
```sh
	/path/to/php /path/to/tiu-client/run.php
```

##Структура данных
Тиу предлагает получение заказов по ссылке вида https://my.tiu.ru/cabinet/export_orders/xml/2372403?hash_tag=47158ffb1af38cb31f1c521dc8a1e1208 в виде XML-файла. Ссылку можно получить в личном кабинете по адресу: https://my.tiu.ru/cabinet/order/export_orders

```xml
<orders date="2015-08-31 17:32">
    <order id="2803303" state="new">
        <name>Иван Петров</name>
        <phone>+79111111111</phone>
        <email>test@yandex.ru</email>
        <date>22.07.15 19:24</date>
        <address>Новокузнецк, ул тольятти 71 кв 37</address>
        <paymentType>Наличными</paymentType>
        <deliveryType>Доставка курьером</deliveryType>
        <priceRUB>691.00</priceRUB>
        <items>
            <item id="68534240">
                <external_id>717432</external_id>
                <name>Hama H-74229 hdmi 1.3 a-c (mini)</name>
                <quantity>1.00</quantity>
                <currency>RUB</currency>
                <image>http://images.ru.prom.st/*</image>
                <url>http://magazine-cs2372403.tiu.ru/*</url>
                <price>366.00</price>
                <sku/>
            </item>
            <item id="68534241">
                <external_id>717436</external_id>
                <name>Hama H-74237 hdmi 1.3 a-c (mini)</name>
                <quantity>1.00</quantity>
                <currency>RUB</currency>
                <image>http://images.ru.prom.st/*</image>
                <url>http://magazine-cs2372403.tiu.ru/*</url>
                <price>325.00</price>
                <sku/>
            </item>
        </items>
    </order>
</orders>
```

####Поля:
 * атрибут `state` поля order - статус заказа. По умолчанию есть 4 статуса 'opened' - новый, 'accepted' - принят, 'declined' - отменен, 'closed' - выполнен. Также возможно добавление своих статусов вот здесь: https://my.tiu.ru/cabinet/order_v2
 * address - если поле не было заполнено клиентом, в нем будет строка "Адрес неизвестен."
 * paymentType - список всех типов оплат можно увидеть по ссылке https://my.tiu.ru/cabinet/shop_settings/payment_options
 * deliveryType - список всех доставок можно увидеть по ссылке https://my.tiu.ru/cabinet/shop_settings/delivery_options
