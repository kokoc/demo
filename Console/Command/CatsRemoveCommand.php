<?php

namespace Kokoc\Demo\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class CatsRemoveCommand extends Command
{
     /**
     * @var \Magento\Framework\App\State
     */
    private $appState;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;
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
     * CatsRemoveCommand constructor.
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\CategoryListInterface $categoryList
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\CategoryListInterface $categoryList,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct();

        $this->appState = $appState;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoryList = $categoryList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cats:remove')->setDescription('Remove all cats');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('adminhtml');
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
        
        $output->writeln("All cats removed");

    }
}
