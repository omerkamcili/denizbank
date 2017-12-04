# Denizbank
Denizbank 3D Pay Php Sanal Pos Entegrasyonu

### Composer install
```php
composer require omerkamcili/denizbank
```


### 3D Pay Örnek Kullanım
```php
use OmerKamcili\DenizBank\DenizbankPay3d;

$payment = new DenizbankPay3d();
$payment->setShopCode('3123');
$payment->setMerchantPass('gDg1N');
$payment->setSuccessUrl('http://successurl');
$payment->setFailUrl('http://failurl');
// Testleri bitirdiğinizde buraya canlı ortamın 3d pay url'sini gireceksiniz.
//$this->setPay3dUrl('');
$payment->setAmount(20);
$payment->setOrderId(3388483);
// Form oluşturuluyor
$result = $payment->getPaid('5200190046477986', '319', '0121', '1');
echo $result;

```


### Result Örnek Kullanım

```php
// 3D işlemi sonrasnda banka post dönüyor, bu dönen postu result methoduna parametre olarak veriyoruz.

$payment = new PaymentDenizbank();
$result = $payment->result($_POST);

// Array döndürüyor, işlem başarılıysa error => '00' dönecek
print_r($result);

```
