<?php

namespace Kokoc\Demo\Model;

use Kokoc\Demo\Api\CatsServiceInterface;
use Magento\Catalog\Model\Product\Visibility;

class CatsService implements CatsServiceInterface
{
    private $catSource = null;

    private $categoryRepository;
    private $categoryFactory;

    private $productRepository;
    private $productFactory;

    private $hydrator;
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;
    /**
     * @var \Magento\Framework\App\State
     */

    /**
     * @var \Magento\Catalog\Api\CategoryListInterface
     */
    private $categoryList;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * CatsService constructor.
     * @param Cats\Source $catsSource
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryInterfaceFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory
     * @param \Magento\Framework\EntityManager\HydratorInterface $hydrator
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Kokoc\Demo\Model\Cats\Source $catsSource,
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryInterfaceFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Framework\EntityManager\HydratorInterface $hydrator,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\CategoryListInterface $categoryList
    ) {
        $this->catSource = $catsSource;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryInterfaceFactory;
        $this->productFactory = $productInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->hydrator = $hydrator;
        $this->appState = $appState;
        $this->registry = $registry;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryList = $categoryList;
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        //$this->appState->setAreaCode('adminhtml');
        /** @var \Magento\Catalog\Api\Data\CategoryInterface $categoryDto */
        $categoryDto = $this->categoryFactory->create();
        $categoryDto->setName('Cats');
        $categoryDto->setParentId(2);
        $categoryDto->setIsActive(1);

        $categoryDto = $this->categoryRepository->save($categoryDto);

        foreach ($this->catSource->getCats() as $cat) {
            $image = file_get_contents($cat['image']);

            /** @var \Magento\Catalog\Api\Data\ProductInterface $productDto */
            $productDto = $this->productFactory->create();

            $productDto = $this->hydrator->hydrate($productDto, [
                'name' => $cat['name'],
                'price' => $cat['price'],
                'description' => $cat['description'],
                'sku' => $cat['price'],
                'category_ids' => [$categoryDto->getId()],
                'extension_attributes' => [
                    'stock_item' => [
                        'qty' => $cat['price'],
                        'is_in_stock' => true
                    ],
                    'is_cat' => true
                ],
                'media_gallery_entries' => [
                    [
                        "media_type" => "image",
                        "label" => "Product Image",
                        "position" => 1,
                        "disabled" => false,
                        "types" => [
                            "image",
                            "small_image",
                            "thumbnail"
                        ],
                        "content" => [
                            "base64_encoded_data" => base64_encode($image),
                            "type" => "image/jpeg",
                            "name" => $cat['price'] . "test.jpg"
                        ]
                    ]
                ],
                'attribute_set_id' => 4,
                'type_id' => 'simple',
                'status' => 1,
                'visibility' => Visibility::VISIBILITY_BOTH
            ]);
            $entries = $productDto->getMediaGalleryEntries();
            /** @var \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $media */
            $media = array_shift($entries);
            $media->getContent()->setBase64EncodedData(base64_encode($image));
            $media->getContent()->setType('image/jpeg');
            $media->getContent()->setName($cat['price'] . "test.jpg");
            $productDto->setMediaGalleryEntries([$media]);


            $this->productRepository->save($productDto);
        }
    }

    /**
     * @inheritdoc
     */
    public function remove()
    {
        //$this->appState->setAreaCode('adminhtml');
        $this->registry->register('isSecureArea', true);

        $searchCretiria = $this->searchCriteriaBuilder->create();
        foreach ($this->categoryList->getList($searchCretiria)->getItems() as $category) {
            if ($category->getName() == 'Cats') {
                $this->categoryRepository->deleteByIdentifier($category->getId());
            }
        }

        foreach ($this->productRepository->getList($searchCretiria)->getItems() as $product) {
            if ($product->getExtensionAttributes()->getIsCat()) {
                $this->productRepository->deleteById($product->getId());
            }
        }


    }
}