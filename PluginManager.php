<?php
/**
 * Author: lqdung1992@gmail.com
 * Date: 1/29/2019
 * Time: 2:52 PM
 */

namespace Plugin\OSGHNDelivery;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\DeliveryRepository;
use Plugin\OSGHNDelivery\Entity\GHNConfig;
use Plugin\OSGHNDelivery\Entity\GHNDelivery;
use Plugin\OSGHNDelivery\Repository\GHNConfigRepository;
use Plugin\OSGHNDelivery\Repository\GHNDeliveryRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginManager
 * @package Plugin\OSGHNDelivery
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * Install the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function install(array $meta, ContainerInterface $container)
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $this->loadFixturesGHNPref($em);
        $this->setupGHNDelivery($container, $em);
        $this->setupPageLayout($em);

        // flush all
        $em->flush();
    }

    /**
     * Update the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function update(array $meta, ContainerInterface $container)
    {
    }

    /**
     * Enable the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function enable(array $meta, ContainerInterface $container)
    {
    }

    /**
     * Disable the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function disable(array $meta, ContainerInterface $container)
    {
    }

    /**
     * Uninstall the plugin.
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function uninstall(array $meta, ContainerInterface $container)
    {
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $tableNameGHNDelivery = $em->getClassMetadata(GHNDelivery::class)->getTableName();
        $conn = $em->getConnection();
        $sql = 'SELECT * FROM ' . $tableNameGHNDelivery;
        $ghnDelivery = $conn->fetchAll($sql);
        foreach($ghnDelivery as $item) {
            $ecDelivery = $container->get(DeliveryRepository::class)->find($item['id']);
            foreach ($ecDelivery->getDeliveryFees() as $deliveryFee) {
                $em->remove($deliveryFee);
            }
            $ecDelivery->setVisible(false);
            $ecDelivery->setName("Giao hàng nhanh - Đã xóa");
            $em->persist($ecDelivery);
        }

        $em->flush();
    }

    /**
     * @param $em
     */
    private function loadFixturesGHNPref($em): void
    {
        // setup giao hang nhanh province
        $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
        $loader->loadFromDirectory(__DIR__ . '/Resource/doctrine/import_csv/');
        $executor = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
        $fixtures = $loader->getFixtures();
        $executor->execute($fixtures);
    }

    /**
     * @param ContainerInterface $container
     * @param $em
     */
    private function setupGHNDelivery(ContainerInterface $container, EntityManagerInterface $em): void
    {
        // setup GHN delivery
        $saleType = $em->getRepository(SaleType::class)->find(SaleType::SALE_TYPE_NORMAL);
        $paymentMethods = $em->getRepository(Payment::class)->findAll();
        $delivery = new Delivery();
        $delivery->setName('Giao hàng nhanh')
            ->setSaleType($saleType)
            ->setServiceName('GHN')
            ->setVisible(true)
            ->setConfirmUrl("https://giaohangnhanh.vn");
        $em->persist($delivery);
        $em->flush($delivery);

        foreach ($paymentMethods as $paymentMethod) {
            $method = new PaymentOption();
            $method->setDelivery($delivery)
                ->setDeliveryId($delivery->getId())
                ->setPayment($paymentMethod)
                ->setPaymentId($paymentMethod->getId());
            $em->persist($method);
        }

        // save delivery id
        $configDelivery = new GHNDelivery();
        $configDelivery->setDelivery($delivery);
        $em->persist($configDelivery);

        // setup delivery fee - all zero
        $prefRepo = $container->get(PrefRepository::class);
        $allPrefs = $prefRepo->findAll();
        foreach ($allPrefs as $pref) {
            $GHNFee = new DeliveryFee();
            $GHNFee->setPref($pref)
                ->setDelivery($delivery)
                ->setFee(0);
            $em->persist($GHNFee);
        }
    }

    /**
     * @param $em
     */
    private function setupPageLayout(EntityManagerInterface $em): void
    {
        // add to layout
        $page = new Page();
        $page->setName("Giao hàng nhanh - Tính phí")
            ->setUrl('ghn_delivery_shopping')
            ->setFileName("@OSGHNDelivery/front/Shopping/delivery.twig")
            ->setEditType(Page::EDIT_TYPE_DEFAULT);
        $em->persist($page);
        $em->flush($page);

        $layout = $em->getRepository(Layout::class)->find(Layout::DEFAULT_LAYOUT_UNDERLAYER_PAGE);
        $pageLayout = new PageLayout();
        $pageLayout->setPage($page)
            ->setPageId($page->getId())
            ->setLayout($layout)
            ->setLayoutId($layout->getId())
            ->setSortNo(1);
        $page->addPageLayout($pageLayout);
        $em->persist($pageLayout);
    }
}