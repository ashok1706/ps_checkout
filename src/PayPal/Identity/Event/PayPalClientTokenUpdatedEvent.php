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

namespace PrestaShop\Module\PrestashopCheckout\PayPal\Identity\Event;

use PrestaShop\Module\PrestashopCheckout\PayPal\Order\Exception\PayPalOrderException;
use PrestaShop\Module\PrestashopCheckout\PayPal\Order\ValueObject\PayPalOrderId;
use Symfony\Component\EventDispatcher\Event;

class PayPalClientTokenUpdatedEvent extends Event
{
    const NAME = 'paypal.order.created';

    /**
     * @var PayPalOrderId
     */
    private $orderPayPalId;
    /**
     * @var string
     */
    private $token;
    /**
     * @var int
     */
    private $idToken;

    /**
     * @param string $orderPayPalId
     *
     * @throws PayPalOrderException
     */
    public function __construct($orderPayPalId,$token,$idToken)
    {
        $this->orderPayPalId = new PayPalOrderId($orderPayPalId);
        $this->token = $token;
        $this->idToken = $idToken;
    }

    /**
     * @return PayPalOrderId
     */
    public function getOrderPayPalId()
    {
        return $this->orderPayPalId;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getIdToken()
    {
        return $this->idToken;
    }
}