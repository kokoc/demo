<?php

namespace Kokoc\Demo\Model;

class PermissionsMock extends \Magento\PricePermissions\Observer\ObserverData
{
    public function setCanEditProductStatus($canEditProductStatus)
    {
        return true;
    }

    public function isCanEditProductStatus()
    {
        return true;
    }

}