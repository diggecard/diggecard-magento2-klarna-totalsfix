<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2020 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\KlarnaPaymentsIntegration\Model\Checkout\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Model\Checkout\Orderline\AbstractLine;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Generate order line details for Diggecard gift card
 */
class Diggecard extends AbstractLine
{
    /**
     * Checkout item type
     */
    const ITEM_TYPE_GIFTCARD = 'diggecard_giftcard_discount';

    /**
     * {@inheritdoc}
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $checkout->getObject();
        $totals = $quote->getTotals();

        if (!is_array($totals) || !isset($totals[self::ITEM_TYPE_GIFTCARD])) {
            return $this;
        }
        $total = $totals[self::ITEM_TYPE_GIFTCARD];
        $amount = $total->getValue();
        if ($amount !== 0) {
            $amount = $total->getValue();
            $value = $this->helper->toApiFloat($amount);

            $checkout->addData([
                'giftcardaccount_unit_price'   => $value,
                'giftcardaccount_tax_rate'     => 0,
                'giftcardaccount_total_amount' => $value,
                'giftcardaccount_tax_amount'   => 0,
                'giftcardaccount_title'        => $total->getTitle(),
                'giftcardaccount_reference'    => $total->getCode()
            ]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(BuilderInterface $checkout)
    {
        return $this;
    }
}
