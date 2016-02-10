<?php
/**
 * Server class.
 *
 * Used to get iframe url and generate specific token.
 *
 *  @since     1.0.0
 *
 *  @author    Web In Color <contact@prestashop.com>
 *  @copyright 2012-2015 Web In Color
 *  @license   http://www.apache.org/licenses/  Apache License
 *  International Registered Trademark & Property of Web In Color
 */

final class WIC_Server
{
    const URL = 'https://apps.tenbucks.io/';

    /**
     * Retrieve server url.
     *
     * @param array $query
     * @param bool  $iframe mode iframe
     *
     * @return string iframe url with query
     */
    public static function getUrl($path, array $query, $standalone = false)
    {
        $path = preg_replace('/^\//', '', $path);
        $url = self::URL.$path;

        if ($standalone) {
            $query['standalone'] = true;
        }

        if (count($query)) {
            $url .= '?'.http_build_query($query);
        }

        return $url;
    }

    /**
     * Send a POST request to TenBucks server
     *
     * @param string $path Path to reach
     * @param array $data POST data
     * @return array result
     */
    public static function post($path, array $data)
    {
        $url = self::URL.preg_replace('/^\//', '', $path);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true );
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Process
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        } catch (Exception $e) {
            Logger::addLog($e->getTraceAsString(), 3, $e->getCode());
            return false;
        }

    }
}
