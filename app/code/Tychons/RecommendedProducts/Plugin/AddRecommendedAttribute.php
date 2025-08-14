<?php

namespace Tychons\RecommendedProducts\Plugin;

use Magento\Catalog\Block\Product\ListProduct;
use Psr\Log\LoggerInterface;

class AddRecommendedAttribute
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterGetLoadedProductCollection(ListProduct $subject, $collection)
    {
        if ($collection) {
            $collection->addAttributeToSelect('recommended');
        }
        return $collection;
    }
}
