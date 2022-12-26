<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OfferSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Vjackk\Offer\Api\Data\OfferInterface[]
     */
    public function getItems();

    /**
     * @param \Vjackk\Offer\Api\Data\OfferInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
