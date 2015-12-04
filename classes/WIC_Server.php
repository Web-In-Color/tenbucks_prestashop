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
    // const URL = 'https://apps.webincolor.fr/woocommerce/';
    const URL = 'https://symfony.local/app_dev.php/';

    /**
     * The HASH alorithm to use for oAuth signature, SHA256 or SHA1.
     */
    const HASH_ALGORITHM = 'SHA256';

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
}
