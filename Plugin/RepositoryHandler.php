<?php

namespace Kokoc\Demo\Plugin;

use Kokoc\Demo\Model\Flag\Repository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class RepositoryHandler
{
    /**
     * @var Repository
     */
    private $catsRepository;

    /**
     * RepositoryHandler constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->catsRepository = $repository;
    }

    public function afterGet(ProductRepositoryInterface $repository, ProductInterface $product)
    {

        $value = (bool)(int)$this->catsRepository->get($product->getId());
        $product->getExtensionAttributes()->setIsCat($value);

        return $product;
    }

    public function afterSave(
        ProductRepositoryInterface $repository,
        ProductInterface $productResult,
        ProductInterface $productOriginal
    ) {
        $isCat = (bool)$productOriginal->getExtensionAttributes()->getIsCat();
        $this->catsRepository->save($productResult->getId(), $isCat);

        return $productResult;
    }
}
