<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Commodity;
use App\Entity\Media;
use App\Entity\Price;
use App\Entity\Product;
use App\Enums\MediaTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Enums\CommodityOperationType;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        /** CATEGORIES */

        $categories = [
            [
                'level'         => 0,
                'categoryOrder' => 0,
                'isActive'      => true,
                'categoryName'  => 'Вишиванки',
                'ref'           => [
                    [
                        'level'         => 1,
                        'categoryOrder' => 1,
                        'categoryName'  => 'Чоловічі вишиванки',
                        'isActive'      => true,
                        'products'      => [
                            ['isActive'    => true,
                             'title'       => 'Чоловіча вишиванка чорна',
                             'description' => 'Чоловіча вишиванка чорного кольору',
                            ],
                            ['isActive'    => true,
                             'title'       => 'Чоловіча вишиванка біла',
                             'description' => 'Чоловіча вишиванка білого кольору',
                            ],
                            ['isActive'    => false,
                             'title'       => 'Чоловіча вишиванка льняна',
                             'description' => 'Чоловіча вишиванка із льону',
                            ],
                        ],
                    ],
                    [
                        'level'         => 1,
                        'categoryOrder' => 2,
                        'categoryName'  => 'Жіночі вишиванки',
                        'isActive'      => true,
                        'products'      => [
                            [
                                'isActive'    => true,
                                'title'       => 'Жіноча вишиванка льняна',
                                'description' => 'Жіноча вишиванка із льону',
                                'commodities' => [
                                    [
                                        'operation' => CommodityOperationType::IMPORT->name,
                                        'amount'    => 100,
                                        'prices'    => [
                                            ['price' => 1000],
                                            ['price' => 2000],
                                            ['price' => 3000],
                                        ],
                                        'sales'     => [
                                            ['operation' => CommodityOperationType::PREORDER->name, 'amount' => 5],
                                            ['operation' => CommodityOperationType::PREORDER->name, 'amount' => 2],
                                            ['operation' => CommodityOperationType::PREORDER->name, 'amount' => 3],
                                        ],
                                    ],
                                    [
                                        'operation' => CommodityOperationType::IMPORT->name,
                                        'amount'    => 50,
                                        'prices'    => [
                                            ['price' => 1700],
                                        ],
                                    ],

                                ],
                                'media'       => [
                                    [
                                        'binaryType'   => MediaTypes::JPEG->name,
                                        'binarySource' => '/images/products/82-6506f99c2f691802579775.jpg',
                                        'isMain'       => true,
                                    ],
                                    [
                                        'binaryType'   => MediaTypes::JPEG->name,
                                        'binarySource' => '/images/products/82-6506f99c2f691802579776.jpg',
                                        'isMain'       => false,
                                    ],
                                    [
                                        'binaryType'   => MediaTypes::MP4->name,
                                        'binarySource' => '/images/products/82-6506f99c2f691802579777.jpg',
                                        'isMain'       => false,
                                    ],
                                ],

                            ],
                            ['isActive'    => true,
                             'title'       => 'Жіноча вишиванка біла',
                             'description' => 'Жіноча вишиванка білого кольору',
                            ],
                            ['isActive'    => true,
                             'title'       => 'Жіноча вишиванка чорна',
                             'description' => 'Жіноча вишиванка чорного кольору',
                            ],
                        ],
                    ],
                    ['level' => 1, 'categoryOrder' => 3, 'categoryName' => 'Дитячі вишиванки', 'isActive' => true],
                ],

            ],
            [
                'level'         => 0,
                'categoryOrder' => 1,
                'isActive'      => true,
                'categoryName'  => 'Ігольниці',
                'ref'           => [
                    [
                        'level'         => 1,
                        'categoryOrder' => 0,
                        'categoryName'  => 'Ігольниці тип 1',
                        'isActive'      => true,
                        'products'      => [
                            ['isActive'    => true,
                             'title'       => 'Ігольниця вишита хрестиком',
                             'description' => 'Ігольниця вишита хрестиком вовною',
                            ],
                            ['isActive'    => false,
                             'title'       => 'Ігольниця вишита гладдю',
                             'description' => 'Ігольниця вишита шовковими нитками',
                            ],
                        ],
                    ],
                    ['level' => 1, 'categoryOrder' => 1, 'categoryName' => 'Ігольниці тип 2', 'isActive' => true],
                    ['level' => 1, 'categoryOrder' => 2, 'categoryName' => 'Ігольниці тип 3', 'isActive' => false],
                ],
            ],
            [
                'level'         => 0,
                'categoryOrder' => 2,
                'isActive'      => true,
                'categoryName'  => 'Ялинкові прикраси',
                'ref'           => [
                    [
                        'level'         => 1,
                        'categoryOrder' => 1,
                        'categoryName'  => 'Прикраси круглі',
                        'isActive'      => true,
                        'products'      => [
                            ['isActive'    => true,
                             'title'       => 'Прикраса велика',
                             'description' => 'Прикраса велика 20 см',
                            ],
                            ['isActive' => true, 'title' => 'Прикраса мала', 'description' => 'Прикраса мала 10 см'],

                        ],
                    ],
                    ['level' => 1, 'categoryOrder' => 2, 'categoryName' => 'Прикраси квадратні', 'isActive' => true],
                ],
            ],
            [
                'level'         => 0,
                'categoryOrder' => 3,
                'isActive'      => false,
                'categoryName'  => 'Спідниці',
                'ref'           => [
                    ['level' => 1, 'categoryOrder' => 1, 'categoryName' => 'Спідниця максі', 'isActive' => true],
                    ['level' => 1, 'categoryOrder' => 2, 'categoryName' => 'Спідниця міні', 'isActive' => false],
                ],
            ],


        ];
        foreach ($categories as $category) {
            $cat = new Category();
            $cat->setIsActive($category['isActive']);
            $cat->setLevel($category['level']);
            $cat->setCategoryName($category['categoryName']);
            $cat->setCategoryOrder($category['categoryOrder']);
            foreach ($category['ref'] as $child) {
                // add child categories
                $ch = new Category();
                $ch->setIsActive($child['isActive']);
                $ch->setLevel($child['level']);
                $ch->setCategoryName($child['categoryName']);
                $ch->setCategoryOrder($child['categoryOrder']);
                $cat->addChild($ch);
                $manager->persist($ch);
                // add products
                if (array_key_exists('products', $child)) {
                    foreach ($child['products'] as $product) {
                        $p = new Product();
                        $p->setIsActive($product['isActive']);
                        $p->setTitle($product['title']);
                        $p->setDescription($product['description']);
                        $manager->persist($p);
                        $ch->addProduct($p);
                        // add commodities
                        if (array_key_exists('commodities', $product)) {
                            foreach ($product['commodities'] as $commodity) {
                                $c = new Commodity();
                                $c->setAmount($commodity['amount']);
                                $c->setOperationType($commodity['operation']);
                                $manager->persist($c);
                                if (array_key_exists('prices', $commodity)) {
                                    // add prices
                                    foreach ($commodity['prices'] as $price) {
                                        $pr = new Price();
                                        $pr->setPrice($price['price']);
                                        $manager->persist($pr);
                                        $c->addPrice($pr);
                                    }
                                }
                                if (array_key_exists('sales', $commodity)) {
                                    foreach ($commodity['sales'] as $saleCommodity) {
                                        $sc = new Commodity();
                                        $sc->setOperationType($saleCommodity['operation']);
                                        $sc->setAmount($saleCommodity['amount']);
                                        $sc->setCommoditySource($c);
                                        $sc->setProduct($p);
                                        $manager->persist($sc);
                                    }

                                }
                                $p->addCommodity($c);
                            }
                        }
                        if (array_key_exists('media', $product)) {
                            foreach ($product['media'] as $media) {
                                $m = new Media();
                                $m->setProduct($p);
                                $m->setBinarySource($media['binarySource']);
                                $m->setBinaryType($media['binaryType']);
                                $m->setIsMain($media['isMain']);
                                $manager->persist($m);
                            }
                        }
                    }
                }
            }
            $manager->persist($cat);
            $manager->flush();
        }
//        for ($i = 0; $i < 20; $i++) {
//        }
//        $product = new Product();

        $manager->flush();
    }
}
