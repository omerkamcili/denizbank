<?php
/*
 * Denizbank 3D Pay
 * Denizbank sanal pos 3d pay ile ödeme alma
 * Ömer Kamçılı <omerkamcili@gmail.com>
 *
 */

namespace OmerKamcili\DenizBank;


/**
 * @property string pay3d_url
 * @property int rand
 */

class DenizbankPay3d{


    // Üye işyeri numaranız
    public $shop_code;


    // İşlem tutaru
    public $amount;


    // İşlem başarılıysa gidilecek url
    public $success_url;


    // İşlem başarısısz ise gidilecek url
    public $fail_url;


    // İşyeri 3D anahtarı
    public $merchant_pass;


    // Sizin sisteminize ait sipariş numarası
    public $order_id;


    // Taksit Sayısı
    public $installment = '';


    // İşlem tipi
    public $txn_type = 'Auth';


    // TL İçin 049
    public $currency = '949';


    // 3D Pay form url
    public $pay3d_url = 'https://sanaltest.denizbank.com/mpi/Default.aspx';


    /**
     * DenizbankPay3d constructor.
     */
    public function __construct(){

        $this->rand = microtime();

    }

    /**
     * @param $card_number - Kredi kartı numarası
     * @param $security_code - Cvv numarası
     * @param $expiration_date - Kredi kartı son kullanma tarihi,iki basamak, ay yıl (MMYY)
     * @param $card_type - Kredi kartı tipi, Visa için 0, Master kart için 1
     * @param $bonus
     * @return string - redirecting html form
     */
    public function getPaid($card_number, $security_code, $expiration_date, $card_type, $bonus=''){

        $hash = $this->createHash();

        $response = '<html><body onload="document.payment_form.submit()">';

        $response .= '<form method="post" name="payment_form" action="' . $this->pay3d_url . '">' . "\r\n";

        $response .= '<input type="hidden" name="Pan" value="' . $card_number . '"/>' . "\r\n";

        $response .= '<input type="hidden" name="Cvv2" value="' . $security_code . '"/>' . "\r\n";

        $response .= '<input type="hidden" name="Expiry" value="' . $expiration_date . '"/>' . "\r\n";

        $response .= '<input type="hidden" name="BonusAmount" value="' . $bonus . '"/>' . "\r\n";

        $response .= '<input type="hidden" name="CardType" value="' . $card_type . '"/>' . "\r\n";

        $response .= '<input type="hidden" name="ShopCode" value="' . $this->shop_code . '">' . "\r\n";

        $response .= '<input type="hidden" name="PurchAmount" value="' . $this->amount . '">' . "\r\n";

        $response .= '<input type="hidden" name="Currency" value="' . $this->currency . '">' . "\r\n";

        $response .= '<input type="hidden" name="OrderId" value="' . $this->order_id . '">' . "\r\n";

        $response .= '<input type="hidden" name="OkUrl" value="' . $this->success_url . '">' . "\r\n";

        $response .= '<input type="hidden" name="FailUrl" value="' . $this->fail_url . '">' . "\r\n";

        $response .= '<input type="hidden" name="Rnd" value="' . $this->rand . '" >' . "\r\n";

        $response .= '<input type="hidden" name="Hash" value="' . $hash . '" >' . "\r\n";

        $response .= '<input type="hidden" name="TxnType" value="' . $this->txn_type . '" />' . "\r\n";

        $response .= '<input type="hidden" name="InstallmentCount" value="' . $this->installment . '" />' . "\r\n";

        $response .= '<input type="hidden" name="SecureType" value="3DPay" >' . "\r\n";

        $response .= '<input type="hidden" name="Lang" value="tr">' . "\r\n";

        $response .= '</body></html>';

        return $response;
    }


    /**
     * @param array $post
     * @return array
     */
    public function result(array $post){


        $hashparams = $post["HASHPARAMS"];
        $hashparamsval = $post["HASHPARAMSVAL"];
        $hashparam = $post["HASH"];
        $paramsval = "";
        $index1 = 0;
        $index2 = 0;

        while($index1 < strlen($hashparams))
        {
            $index2 = strpos($hashparams,":",$index1);

            $vl = $_POST[substr($hashparams,$index1,$index2- $index1)];

            if($vl == null){

                $vl = "";

            }

            $paramsval = $paramsval . $vl;

            $index1 = $index2 + 1;
        }


        $hashval = $paramsval . $this->merchant_pass;
        $hash = base64_encode(pack('H*',sha1($hashval)));

        if($paramsval != $hashparamsval || $hashparam != $hash){

            return $this->response('03');

        }else{

            if(in_array($post["3DStatus"], array(1,2,3,4))){


                if($post['ProcReturnCode'] == '00'){

                    return $this->response('00');

                }else{

                    return $this->response('02', $post['ErrorMessage']);

                }


            }else{


                $this->response('01', $post["ErrorMessage"]);

            }


        }


        return $this->response('04');


    }

    /**
     * @return string
     */
    private function createHash(){

        $hash = $this->shop_code .
                $this->order_id .
                $this->amount .
                $this->success_url .
                $this->fail_url .
                $this->txn_type .
                $this->installment .
                $this->rand .
                $this->merchant_pass;

        $hash = base64_encode(pack('H*',sha1($hash)));
        return $hash;

    }

    /**
     * @param mixed $shop_code
     */
    public function setShopCode($shop_code)
    {
        $this->shop_code = $shop_code;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $success_url
     */
    public function setSuccessUrl($success_url)
    {
        $this->success_url = $success_url;
    }

    /**
     * @param mixed $fail_url
     */
    public function setFailUrl($fail_url)
    {
        $this->fail_url = $fail_url;
    }

    /**
     * @param mixed $merchant_pass
     */
    public function setMerchantPass($merchant_pass)
    {
        $this->merchant_pass = $merchant_pass;
    }

    /**
     * @param mixed $order_id
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * @param string $installment
     */
    public function setInstallment($installment)
    {
        $this->installment = $installment;
    }

    /**
     * @param string $txn_type
     */
    public function setTxnType($txn_type)
    {
        $this->txn_type = $txn_type;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param $error hata kodu
     * @param $message
     * @return array Burası hatasonuçlarını kısaltmamız için,
     * Burası hatasonuçlarını kısaltmamız için,
     * Birçok sonuç dönüyor fakat bizim için önemli dönmemiz gereken mesaj
     */
    private function response($error, $message=null){

        $messages['00'] = 'Başarılı';
        $messages['01'] = '3D İşlemi Başarısız';
        $messages['02'] = 'Ödeme İşlemi Başarısız';
        $messages['03'] = 'Dijital İmza Hatalı';
        $messages['04'] = 'Tanımlanmayan Hata';

        return array('error' => $error, 'message' => (!$message ? $messages[$error] : $message));

    }

    /**
     * @param string $pay3d_url
     */
    public function setPay3dUrl($pay3d_url)
    {
        $this->pay3d_url = $pay3d_url;
    }

}