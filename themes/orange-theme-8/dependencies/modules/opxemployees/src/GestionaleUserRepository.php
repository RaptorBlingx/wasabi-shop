<?php
/**
 * Copyright (c) OrangePix Srl  All rights reserved.
 *
 * DISCLAIMER
 *
 * Do not edit, modify or copy this file.
 * If you wish to customize it, contact us at info@orangepix.it
 * Web : https://www.orangepix.it
 *
 * @author    Carlos Batista <carlos.batista@orangepix.it> , Samuele Cisaro <samuele.cisaro@orangepix.it>
 * @license   Proprietary
 * @copyright OrangePix Srl
 */

namespace OrangePix\Repository;

class GestionaleUserRepository
{
    public static function getUtentiGestionale($super_token = 'DDEDHD401328334373783255HID') {
        if (empty($super_token)){
            return false;
        }
        // introduco un timeout di 3 secondi
        $ctx = stream_context_create(array('http' => array('timeout' => 3)));
        $url = 'https://app.orangepix.it/webservice/' . $super_token . '/user/sync/list';
        $json = file_get_contents($url, false, $ctx);
        $response = false;

        if (strlen($json) > 0) {
            $json = str_replace('"{\"res\":\"', '', $json);
            $json = str_replace('\"}"', '', $json);
            $user_sync_list_decrypted = json_decode(self::decryptAES256($json), true);

            if (isset($user_sync_list_decrypted[0]['password'])) {
                $response = $user_sync_list_decrypted;
            }
        }

        return $response;
    }

    private static function decryptAES256($string = '', $key = 'qZe2zF29sD')
    {
        list($encrypted_data, $iv) = explode('::', base64_decode($string), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }

}