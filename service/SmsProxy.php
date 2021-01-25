<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 1/22/21
 * @time: 10:08 AM
 */

namespace mhunesi\asist\service;

use yii\base\BaseObject;
use mhunesi\asist\Client;
use yii\helpers\ArrayHelper;
use mhunesi\asist\utils\Array2XML;
use mhunesi\asist\exceptions\AsistExceptions;

/**
 * Class SmsProxy
 * Document https://dosya.asistbt.com.tr/SmsProxy.pdf
 * @package mhunesi\asist\service
 */
class SmsProxy extends BaseObject
{
    /**
     * @var string
     */
    public $url = 'https://webservice.asistiletisim.com.tr/SmsProxy.asmx?wsdl';

    /**
     * @var Client
     *
     */
    public $client;

    /**
     * Sistemde tanımlı olan kullanıcı kodunuz.
     * @var string
     */
    public $user_code;

    /**
     * Sistemde tanımlı olan kullanıcı adınız.
     * @var string
     */
    public $username;

    /**
     * : Sistemde tanımlı olan şifreniz.
     * @var string
     */
    public $password;

    /**
     * Sistemde tanımlı olan kullanıcınızın hesap kodu.
     * @var string
     */
    public $account_id;

    /**
     * Sistemde tanımlı olan kullanıcı başlığı.
     * Max. 11 karakter uzunluğunda olabilir.
     * @var string
     */
    public $originator;

    /**
     * @var array
     */
    private $_receiverList = [];

    /**
     * @var array
     */
    private $_messageText = [];

    /**
     * Blacklist kontrolü gerçekleştirilmektedir.
     * @var boolean
     */
    private $_isCheckBlackList = true;

    /**
     * Mesaj geçerlilik süresi. Mesajların alıcılara gönderiminin denenmesini
     * istediğiniz süreyi belirlemek için kullanılır.
     * Bulk hesaplarda dakika cinsinden max 3360 OTP hesaplarda saniye cinsinden max 300 olarak belirtilebilir
     * @var int
     */
    private $_validityPeriod = 60;

    /**
     * İleri tarihli gönderim gerçekleştirmek için tarih formatı ddMMyyHHmmss
     * şeklinde girilmelidir. Default olarak gönderim anlık gerçekleştirileceğinden opsiyonel
     * olarak boş bırakılabilir.
     * @var
     */
    private $_sendDate;

    /**
     * Http Response
     * @var array
     */
    private $_response;

    /**
     * Client init
     */
    public function init()
    {
        parent::init();

        $this->client = new Client([
            'url' => $this->url,
            'options' => [
                'trace' => true,
                'soap_version' => SOAP_1_1,
                'cache_wsdl' => 1,
            ]
        ]);
    }

    /**
     * @param string|array $receiver
     * @return $this
     */
    public function addReceiver($receiver)
    {
        if(is_array($receiver)){
            foreach ($receiver as $item) {
                $this->_receiverList[] = $item;
            }
        }else{
            $this->_receiverList[] = $receiver;
        }
        return $this;
    }

    /**
     * @param string|array $message
     * @return $this
     */
    public function setMessage($message)
    {
        if(is_array($message)){
            foreach ($message as $item) {
                $this->_messageText[] = $item;
            }
        }else{
            $this->_messageText[] = $message;
        }
        return $this;
    }

    /**
     * @param string $originator
     * @return $this
     */
    public function setOriginator($originator)
    {
        $this->originator = $originator;
        return $this;
    }

    /**
     * @param $validityPeriod
     * @return $this
     */
    public function setValidityPeriod($validityPeriod)
    {
        $this->_validityPeriod = $validityPeriod;
        return $this;
    }

    /**
     * @param $sendDate
     * @return $this
     */
    public function setSendDate($sendDate)
    {
        $this->_sendDate = $sendDate;
        return $this;
    }

    /**
     * @param bool $isCheckBlackList
     * @return $this
     */
    public function setIsCheckBlackList($isCheckBlackList)
    {
        $this->_isCheckBlackList = $isCheckBlackList;
        return $this;
    }

    /**
     * Bilgilerini girdiğiniz kullanıcı hesabı sistem tarafından kontrol edilir,
     * cevap olarak tanımlı hesap prepaid (kredili) ise güncel kredi adedi bilgisi sorgulanır.
     * @return array
     * @throws \DOMException
     * @throws AsistExceptions
     */
    public function getCredit()
    {

        $response = $this->client->getCredit([
            'requestXml' => Array2XML::convert([
                'Username' => $this->username,
                'Password' => $this->password,
                'UserCode' => $this->user_code,
                'AccountId' => $this->account_id,
            ],'GetCredit')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'getCreditResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }


    /**
     * Bilgilerini girdiğiniz kullanıcı hesabı sistem tarafından kontrol edilir,
     * cevap olarak kullanıcıya tanımlı olan Originator (Alfanumeric Sender) bilgileri sorgulanır.
     * @return array
     * @throws \DOMException
     * @throws AsistExceptions
     */
    public function getOriginator()
    {

        $response = $this->client->getOriginator([
            'requestXml' => Array2XML::convert([
                'Username' => $this->username,
                'Password' => $this->password,
                'UserCode' => $this->user_code,
                'AccountId' => $this->account_id,
            ],'GetOriginator')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'getOriginatorResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }


    /**
     * SendSms Fonksiyonu, uygulama geliştiricilerin hazırlamış oldukları Kısa Mesajları (SMS),
     * sunucuya teslim edebilmelerini sağlayan gönderim fonksiyonudur.
     * @return bool
     * @throws AsistExceptions
     * @throws \DOMException
     */
    public function sendSms()
    {
        $sendOptions = [
            'Username' => $this->username,
            'Password' => $this->password,
            'UserCode' => $this->user_code,
            'AccountId' => $this->account_id,
            'Originator' => $this->originator,
            'SendDate' => $this->_sendDate,
            'ValidityPeriod' => $this->_validityPeriod,
            'IsCheckBlackList' => $this->_isCheckBlackList === true ? 1 : 0,
            'ReceiverList' => []
        ];

        foreach ($this->_receiverList as $item) {
            $sendOptions['ReceiverList']['Receiver'][] = $item;
        }

        if(count($this->_messageText) > 1){
            $sendOptions['MessageText'] = '[##MESAJ##]';

            foreach ($this->_messageText as $item) {
                $sendOptions['PersonalMessages']['PersonalMessage'][] = ['Parameter' => $item];
            }

        }else{
            $sendOptions['MessageText'] = $this->_messageText[0];
        }


        $response = $this->client->sendSms([
            'requestXml' => Array2XML::convert($sendOptions,'SendSms')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'sendSmsResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return true;
    }

    /**
     * İleri tarihli bir gönderimin iptal edilmesi için kullanılır. İleri tarihli gönderime ait PacketId değeri,
     * ilgili metod ile kullanılarak gönderim iptali sağlanır.
     * @param string $packetId
     * @return array
     * @throws \DOMException
     * @throws AsistExceptions
     */
    public function abortSms($packetId)
    {
        $response = $this->client->abortSms([
            'requestXml' => Array2XML::convert([
                'Username' => $this->username,
                'Password' => $this->password,
                'UserCode' => $this->user_code,
                'AccountId' => $this->account_id,
                'PacketId' => $packetId,
            ],'AbortSms')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'abortSmsResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }

    /**
     * getStatus fonksiyonu bir SMS gönderimine ait özet raporunun ya da
     * gsm bazlı durum raporlarının sorgulanması için kullanılır.
     * @param $packetId
     * @return array
     * @throws AsistExceptions
     * @throws \DOMException
     */
    public function getStatusByPacketId($packetId)
    {
        $response = $this->client->getStatus([
            'requestXml' => Array2XML::convert([
                'Username' => $this->username,
                'Password' => $this->password,
                'UserCode' => $this->user_code,
                'AccountId' => $this->account_id,
                'PacketId' => $packetId,
            ],'GetStatus')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'getStatusResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }

    /**
     * getStatus fonksiyonu bir SMS gönderimine ait özet raporunun ya da
     * gsm bazlı durum raporlarının sorgulanması için kullanılır.
     * @param array $messageIdList
     * @return array
     * @throws AsistExceptions
     * @throws \DOMException
     */
    public function getStatusByMessageId($messageIdList = [])
    {
        $sendOptions = [
            'Username' => $this->username,
            'Password' => $this->password,
            'UserCode' => $this->user_code,
            'AccountId' => $this->account_id,
        ];

        foreach ($messageIdList as $item) {
            $sendOptions['MessageIdList']['MessageId'][] = $item;
        }

        $response = $this->client->getStatus([
            'requestXml' => Array2XML::convert($sendOptions,'GetStatus')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'getStatusResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }
}