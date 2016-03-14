# tiu-client

Application for export orders from [tiu.ru](http://tiu.ru) to [retailCRM](http://retailcrm.pro)

Can be used with [prom.ua](http://prom.ua), [deal.by](http://deal.by), [satu.kz](http://satu.kz)

##Setup

Execute in your shell

```sh
git clone https://github.com/retailcrm/tiu-client.git
cd tiu-client
curl -sS https://getcomposer.org/installer | php
php composer.phar require retailcrm/api-client-php ~3.0.0
```

Create & fill configuration file at config/config.php (see /config/config-dist.php)

##Usage

Create a Cron job

```sh
*/10 * * * * /usr/bin/php /path/to/tiu-client/run.php
```

##Data structure

tiu.ru exports orders data as xml file available via special link like this

```
    https://my.tiu.ru/cabinet/export_orders/xml/2372403?hash_tag=47158ffb1af38cb31f1c521dc8a1e1208
```

This link [can be taken from backoffice](https://my.tiu.ru/cabinet/order/export_orders)

####XML example

```xml
<orders date="2015-08-31 17:32">
    <order id="2803303" state="new">
        <name>John Doe</name>
        <phone>+79111111111</phone>
        <email>test@example.org</email>
        <date>22.07.15 19:24</date>
        <address>Moscow, Sample st., 15</address>
        <paymentType>Cash</paymentType>
        <deliveryType>Courier</deliveryType>
        <priceRUB>691.00</priceRUB>
        <items>
            <item id="68534240">
                <external_id>717432</external_id>
                <name>Hama H-74229 hdmi 1.3 a-c (mini)</name>
                <quantity>1.00</quantity>
                <currency>RUB</currency>
                <image>http://images.ru.prom.st/hama-h-74229.jpg</image>
                <url>http://example-shop.tiu.ru/hama-h-74229</url>
                <price>366.00</price>
                <sku/>
            </item>
            <item id="68534241">
                <external_id>717436</external_id>
                <name>Hama H-74237 hdmi 1.3 a-c (mini)</name>
                <quantity>1.00</quantity>
                <currency>RUB</currency>
                <image>http://images.ru.prom.st/hama-h-74237.jpg</image>
                <url>http://example-shop.tiu.ru/hama-h-74237</url>
                <price>325.00</price>
                <sku/>
            </item>
        </items>
    </order>
</orders>
```
