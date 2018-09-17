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
     * @var \Kokoc\Demo\Api\CatsServiceInterface
     */
    private $service;

    /**
     * CatsRemoveCommand constructor.
     * @param \Kokoc\Demo\Api\CatsServiceInterface $service
     */
    public function __construct(
        \Kokoc\Demo\Api\CatsServiceInterface $service
    ) {
        parent::__construct();


        $this->service = $service;
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
        $this->service->remove();
        $output->writeln("All cats removed");

    }
}
