<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 1/24/21
 * @time: 10:58 PM
 */

namespace mhunesi\asist\exceptions;

class AsistExceptions extends \yii\base\Exception
{
    /**
     * @var int error code, such as -1, -2, -3, etc.
     */
    public $errorCode;

    /**
     * @var array
     */
    public static $errorCodes = [
        -1 => 'Girilen bilgilere sahip bir kullanıcı bulunamadı.',
        -2 => 'Kullanıcı pasif durumda.',
        -3 => 'Kullanıcı bloke durumda',
        -4 => 'Kullanıcı hesabı bulunamadı.',
        -5 => 'Kullanıcı hesabı pasif durumda.',
        -6 => 'Kayıt bulunamadı.',
        -7 => 'Hatalı xml istek yapısı.',
        -8 => 'Alınan parametrelerden biri veya birkaçı hatalı.',
        -9 => 'Prepaid hesap bulunamadı.',
        -10 => 'Operatör servisinde geçici kesinti.',
        -11 => 'Başlangıç tarihi ile şu an ki zaman arasındaki fark 30 dakikadan az.',
        -12 => 'Başlangıç tarihi ile şu an ki zaman arasındaki fark 30 günden fazla.',
        -13 => 'Geçersiz gönderici bilgisi.',
        -14 => 'Hesaba ait SMS gönderim yetkisi bulunmuyor.',
        -15 => 'Mesaj içeriği boş veya limit olan karakter sayısını aşıyor',
        -16 => 'Geçersiz alıcı bilgisi.',
        -17 => 'Parametre adetleri ile şablon içerisindeki parametre adedi uyuşmuyor',
        -18 => 'Gönderim içerisinde birden fazla hata mevcut. MessageId kontrol edilmelidir.',
        -19 => 'Mükerrer gönderim isteği.',
        -20 => 'Bilgilendirme mesajı almak istemiyor.',
        -21 => 'Numara karalistede.',
        -22 => 'Yetkisiz IP Adresi',
        -23 => 'Kullanıcı yetkisi bulunmamaktadır.',
        -24 => 'Belirtilen paket zaten onaylanmıştır.',
        -25 => 'Belirtilen Id için onaylanmamış bir paket bulunamadı.',
        -1000 => 'SYSTEM_ERROR',
    ];

    /**
     * Constructor.
     * @param int $errorCode
     * @param string $message error message
     * @param int $code error code
     */
    public function __construct($errorCode, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->errorCode = $errorCode;

        if (!$message && isset(self::$errorCodes[$this->errorCode])) {
            $message = self::$errorCodes[$this->errorCode];
        }

        parent::__construct($message, $errorCode, $previous);
    }

    public function getName()
    {
        return 'Asist Exception';
    }

}