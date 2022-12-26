<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Ui\Component\Offer;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Vjackk\Offer\Ui\Component\AddFilterInterface;

class FulltextFilter implements AddFilterInterface
{
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(FilterBuilder $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(SearchCriteriaBuilder $searchCriteriaBuilder, Filter $filter)
    {
        $titleFilter = $this->filterBuilder->setField('label')
            ->setValue(sprintf('%%%s%%', $filter->getValue()))
            ->setConditionType('like')
            ->create();
        $searchCriteriaBuilder->addFilter($titleFilter);
    }
}
