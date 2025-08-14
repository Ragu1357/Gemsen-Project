<?php
namespace Tychons\RecommendedProducts\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Psr\Log\LoggerInterface;

class Recommended extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected CollectionFactory $productCollectionFactory;
    protected LayerResolver $layerResolver;
    protected LoggerInterface $logger;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        LayerResolver $layerResolver,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->layerResolver = $layerResolver;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * Get recommended products for the current category
     */
    public function getRecommendedProducts(): Collection
    {
        $category = $this->getCurrentCategory();
        if (!$category) {
            $this->logger->info('[RecommendedProducts] No category found.');
            return $this->getEmptyCollection();
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image', 'thumbnail','recommended'])
            ->addFinalPrice()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('recommended', ['eq' => 1])
            ->addAttributeToFilter('visibility', ['in' => [2, 3, 4]])
            ->addAttributeToFilter('status', ['eq' => 1])
            ->addCategoryFilter($category);

        return $collection;
    }

    /**
     * Count how many recommended products are in the current category
     */
    public function getRecommendedCount(): int
    {
        return (int) $this->getRecommendedProducts()->getSize();
    }

    /**
     * Empty collection helper
     */
    private function getEmptyCollection(): Collection
    {
        return $this->productCollectionFactory->create()
            ->addAttributeToFilter('entity_id', 0);
    }

    /**
     * Get the current category from the layer
     */
    private function getCurrentCategory(): ?\Magento\Catalog\Model\Category
    {
        $layer = $this->layerResolver->get();
        return $layer->getCurrentCategory();
    }
}
