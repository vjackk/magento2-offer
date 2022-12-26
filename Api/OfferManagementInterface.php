<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Api;

use Vjackk\Offer\Api\Data\OfferInterface;

interface OfferManagementInterface
{
    /**
     * @param int $categoryId
     * @return OfferInterface[]
     */
    public function getListByCategoryId($categoryId);
}
