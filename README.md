# Denizbank
Denizbank 3D Pay

```php
use OmerKamcili\DenizBank\DenizbankPay3d;

### 3D Pay Örnek Kullanım

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

```
