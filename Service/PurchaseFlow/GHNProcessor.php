<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/31/2019
 * Time: 2:03 PM
 */

namespace Plugin\OSGHNDelivery\Service\PurchaseFlow;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\ShoppingFlow;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;

/**
 * Class GHNProcessor
 * @package Plugin\OSGHNDelivery\Service\PurchaseFlow
 *
 * @ShoppingFlow()
 */
class GHNProcessor implements ItemHolderPreprocessor
{
    /** @var GHNDeliveryRepository */
    protected $GHNDeliveryRepo;
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * GHNProcessor constructor.
     * @param GHNDeliveryRepository $GHNDeliveryRepo
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(GHNDeliveryRepository $GHNDeliveryRepo, EntityManagerInterface $entityManager)
    {
        $this->GHNDeliveryRepo = $GHNDeliveryRepo;
        $this->entityManager = $entityManager;
    }

    // remove only
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $order = $itemHolder;
        if (!$order instanceof Order) {
            return;
        }
        $this->removeGHNDeliveryFeeItem($order);
    }

    /**
     * If OrderItem is not GHN delivery but has GHN fee => remove it
     *
     * @param Order $itemHolder
     */
    private function removeGHNDeliveryFeeItem(Order $itemHolder)
    {
        foreach ($itemHolder->getShippings() as $Shipping) {
            // remove if not GHN delivery
            $GHNDelivery = $this->GHNDeliveryRepo->find($Shipping->getDelivery());
            if ($GHNDelivery) {
                continue;
            }

            /** @var OrderItem $item */
            foreach ($Shipping->getOrderItems() as $item) {
                // GHN fee
                if ($item->isDeliveryFee() && $item->getProcessorName() == self::class) {
                    $Shipping->removeOrderItem($item);
                    $itemHolder->removeOrderItem($item);
                    $this->entityManager->remove($item);
                }
            }
        }
    }

}