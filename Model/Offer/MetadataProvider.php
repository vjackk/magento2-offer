<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\Offer;

use Magento\Theme\Model\Design\Config\MetadataProviderInterface;

class MetadataProvider implements MetadataProviderInterface
{
    /**
     * @var array
     */
    protected $metadata;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function get()
    {
        return $this->metadata;
    }
}
