<?php

namespace Kokoc\Demo\Console\Command;

use Magento\Catalog\Model\Product\Visibility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class CatsApplyCommand extends Command
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
     * CatsApplyCommand constructor.
     * @param \Kokoc\Demo\Model\Cats\Source $catsSource
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryInterfaceFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory
     * @param \Magento\Framework\EntityManager\HydratorInterface $hydrator
     */
    public function __construct(
        \Kokoc\Demo\Model\Cats\Source $catsSource,
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryInterfaceFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Framework\EntityManager\HydratorInterface $hydrator
    ) {
        $this->catSource = $catsSource;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryInterfaceFactory;
        $this->productFactory = $productInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->hydrator = $hydrator;

        parent::__construct();

        $this->appState = $appState;
        $this->appState->setAreaCode('adminhtml');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cats:apply')->setDescription('Put some cats in the catalog');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Magento\Catalog\Api\Data\CategoryInterface $categoryDto */
        $categoryDto = $this->categoryFactory->create();
        $categoryDto->setName('Cats');
        $categoryDto->setParentId(2);
        $categoryDto->setIsActive(1);

        $categoryDto = $this->categoryRepository->save($categoryDto);
        $output->writeln('Cats category has been created');
        $output->writeln('');

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
                ],
                'media_gallery_entries' => [[
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
                ]],
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

            $output->write('.');
        }


        //$output->writeln(print_r($this->catSource->getCats(), true));
    }
}
