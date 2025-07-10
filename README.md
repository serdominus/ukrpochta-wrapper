# Ukrposhta PHP Wrapper (v2)

Проста та зручна PHP-бібліотека для роботи з офіційним API Укрпошти (eCom, Tracking, Counterparty).

Бібліотека підтримує автоматичний вибір токенів залежно від типу запиту і дозволяє легко працювати з адресами, клієнтами, посилками та трекінгом.

---

## Встановлення

Рекомендується встановлювати через Composer:

```bash
composer require serdominus/ukrpochta-wrapper
```
## Використання
```bash
require 'vendor/autoload.php';

use Ukrpochta\PochtaV2;

// Ініціалізація з токенами
$pochta = new PochtaV2([
    'ecom' => 'PRODUCTION_BEARER_ECOM',
    'tracking' => 'PRODUCTION_BEARER_STATUS_TRACKING',
    'counterparty' => 'PROD_COUNTERPARTY_TOKEN',
]);

// Приклад створення посилки
$response = $pochta->createParcel([
    'recipient' => [
        'name' => 'Іван Іванов',
        'address' => 'вул. Хрещатик, 1, Київ',
        // інші поля...
    ],
    'parcel' => [
        'weight' => 1.5,
        'type' => 'package',
        // інші параметри...
    ],
]);

echo $response;
```
## Особливості
Автоматичний вибір токенів за типом запиту

Підтримка основних ресурсів API: адреси, клієнти, відправлення, групи, трекінг

Робота з PDF-формами

Легко розширюється під інші методи API

## Залежності
PHP 7.4+

GuzzleHttp

## Ліцензія
MIT License





