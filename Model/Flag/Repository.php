<?php
namespace Kokoc\Demo\Model\Flag;


class Repository
{
    private $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->connection = $resource->getConnection('catalog');
    }

    public function save($productId, $value)
    {
        $this->connection->insertOnDuplicate(
            $this->connection->getTableName('cats_flag'),
            ['product_id' => $productId, 'flag' => $value],
            ['flag']
        );
    }

    public function get($productId)
    {
        $select = $this->connection->select();
        $select
            ->from($this->connection->getTableName('cats_flag'), ['flag'])
            ->where('product_id = ?', $productId);

        return (bool)(int)$this->connection->fetchOne($select);
    }
}