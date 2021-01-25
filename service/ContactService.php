<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 1/22/21
 * @time: 10:09 AM
 */

namespace mhunesi\asist\service;

use mhunesi\asist\Client;
use mhunesi\asist\exceptions\AsistExceptions;
use mhunesi\asist\utils\Array2XML;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Class ContactService
 * @package mhunesi\sms\providers\asist\service
 */
class ContactService extends BaseObject
{
    /**
     * @var string
     */
    public $url = 'https://webservice.asistiletisim.com.tr/ContactService.asmx?wsdl';

    /**
     * @var Client
     *
     */
    public $client;

    /**
     * @var string
     */
    public $user_code;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $account_id;

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


    public function getContact($receiver)
    {
        $response = $this->client->getContact([
            'requestXml' => Array2XML::convert([
                'Username' => $this->username,
                'Password' => $this->password,
                'UserCode' => $this->user_code,
                'Receiver' => $receiver,
            ],'GetContact')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'getContactResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }

    public function addContact($name,$surname,$receiver,$groupId = null,$isBlackList = false)
    {
        $response = $this->client->addContact([
            'requestXml' => Array2XML::convert([
                'Username' => $this->username,
                'Password' => $this->password,
                'UserCode' => $this->user_code,
                'Name' => $name,
                'Surname' => $surname,
                'GroupId' => $groupId,
                'Receiver' => $receiver,
            ],'AddContact')
        ]);

        $this->_response = ArrayHelper::toArray($response);

        if(($errorCode = ArrayHelper::getValue($this->_response,'addContactResult.ErrorCode')) && (int)$errorCode !== 0){
            throw new AsistExceptions($errorCode);
        }

        return $this->_response;
    }

}