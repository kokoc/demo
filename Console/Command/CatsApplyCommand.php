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
     * @var \Kokoc\Demo\Api\CatsServiceInterface
     */
    private $service;

    /**
     * CatsApplyCommand constructor.
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
        $this->setName('cats:apply')->setDescription('Put some cats in the catalog');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->create();
    }
}
