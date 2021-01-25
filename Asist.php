<?php

namespace mhunesi\asist;

use mhunesi\asist\service\ContactService;
use mhunesi\asist\service\SmsProxy;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * This is just an example.
 */
class Asist extends Component
{
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
     * @var string
     */
    public $default_originator;


    /**
     * @param array $options
     * @return SmsProxy
     */
    public function smsProxy($options = [])
    {
        $options = ArrayHelper::merge([
            'user_code' => $this->user_code,
            'username' => $this->username,
            'password' => $this->password,
            'account_id' => $this->account_id,
            'originator' => $this->default_originator,
        ],$options);

        return new SmsProxy($options);
    }
    /**
     * @param array $options
     * @return ContactService
     */
    public function contactService($options = [])
    {
        $options = ArrayHelper::merge([
            'user_code' => $this->user_code,
            'username' => $this->username,
            'password' => $this->password,
            'account_id' => $this->account_id,
        ],$options);

        return new ContactService($options);
    }
}
