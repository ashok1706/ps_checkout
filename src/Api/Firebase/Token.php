<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopCheckout\Api\Firebase;

use PrestaShop\Module\PrestashopCheckout\Api\Firebase\Client\FirebaseClient;

/**
 * Handle authentication firebase requests
 */
class Token extends FirebaseClient
{
    /**
     * Refresh the token
     *
     * @see https://firebase.google.com/docs/reference/rest/auth#section-refresh-token Firebase documentation
     *
     * @return array
     */
    public function refresh()
    {
        $this->setRoute('https://securetoken.googleapis.com/v1/token');

        $response = $this->post([
            'json' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->prestaShopConfiguration->get(
                    'PS_PSX_FIREBASE_REFRESH_TOKEN',
                    ['id_shop' => $this->prestaShopContext->getShopId()]
                )
            ],
        ]);

        if (true === $response['status']) {
            $this->prestaShopConfiguration->set(
                'PS_PSX_FIREBASE_ID_TOKEN',
                $response['body']['id_token'],
                ['id_shop' => $this->prestaShopContext->getShopId()]
            );
            $this->prestaShopConfiguration->set(
                'PS_PSX_FIREBASE_REFRESH_TOKEN',
                $response['body']['refresh_token'],
                ['id_shop' => $this->prestaShopContext->getShopId()]
            );
            $this->prestaShopConfiguration->set(
                'PS_PSX_FIREBASE_REFRESH_DATE',
                date('Y-m-d H:i:s'),
                ['id_shop' => $this->prestaShopContext->getShopId()]
            );
        } elseif (isset($response['httpCode']) && 400 === $response['httpCode']) {
            $this->prestaShopConfiguration->set(
                'PS_PSX_FIREBASE_ID_TOKEN',
                '',
                ['id_shop' => $this->prestaShopContext->getShopId()]
            );
            $this->prestaShopConfiguration->set(
                'PS_PSX_FIREBASE_REFRESH_TOKEN',
                '',
                ['id_shop' => $this->prestaShopContext->getShopId()]
            );
            $this->prestaShopConfiguration->set(
                'PS_PSX_FIREBASE_REFRESH_DATE',
                '',
                ['id_shop' => $this->prestaShopContext->getShopId()]
            );
        }

        return $response;
    }

    /**
     * Check we can request an other token.
     *
     * @return bool
     */
    public function shouldRefreshToken()
    {
        return $this->hasRefreshToken() && $this->isExpired();
    }

    /**
     * Check if we have a refresh token
     *
     * @return bool
     */
    public function hasRefreshToken()
    {
        $refresh_token = $this->prestaShopConfiguration->get(
            'PS_PSX_FIREBASE_REFRESH_TOKEN',
            ['id_shop' => $this->prestaShopContext->getShopId()]
        );

        return !empty($refresh_token);
    }

    /**
     * Check the token validity. The token expire time is set to 3600 seconds.
     *
     * @return bool
     */
    public function isExpired()
    {
        $refresh_date = $this->prestaShopConfiguration->get(
            'PS_PSX_FIREBASE_REFRESH_DATE',
            ['id_shop' => $this->prestaShopContext->getShopId()]
        );

        if (empty($refresh_date)) {
            return true;
        }

        return strtotime($refresh_date) + 3600 < time();
    }

    /**
     * Get the user firebase token
     *
     * @return string
     */
    public function getToken()
    {
        if ($this->shouldRefreshToken()) {
            $this->refresh();
        }

        return $this->prestaShopConfiguration->get(
            'PS_PSX_FIREBASE_ID_TOKEN',
            ['id_shop' => $this->prestaShopContext->getShopId()]
        );
    }
}
