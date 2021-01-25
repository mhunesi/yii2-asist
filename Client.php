<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2020 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 2020-08-26
 * @time: 15:11
 */

namespace mhunesi\asist;

use \SoapClient;
use \SoapFault;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * The SOAP Client class.
 *
 * Note, PHP SOAP extension is required.
 */
class Client extends Component
{
    /**
     * @var string $url the URL of the WSDL file.
     */
    public $url;
    /**
     * @var array the array of SOAP client options.
     */
    public $options = [];

    /**
     * @var SoapClient the SOAP client instance.
     */
    public $_client;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->url === null) {
            throw new InvalidConfigException('The "url" property must be set.');
        }
        try {
            $this->_client = new SoapClient($this->url, $this->options);
        } catch (SoapFault $e) {
            throw new \Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        try {
            return call_user_func_array([$this->_client, $name], $arguments);
        } catch (SoapFault $e) {
            throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}