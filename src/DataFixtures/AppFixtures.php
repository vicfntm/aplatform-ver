<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Commodity;
use App\Entity\Media;
use App\Entity\Order;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\User;
use App\Enums\MediaTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Enums\CommodityOperationType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Ulid;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasherInterface)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $ts = [
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
            Ulid::fromString(Ulid::generate()),
        ];

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
                            [
                                'isActive'    => true,
                                'title'       => 'Чоловіча вишиванка чорна',
                                'description' => 'Чоловіча вишиванка чорного кольору',
                            ],
                            [
                                'isActive'    => true,
                                'title'       => 'Чоловіча вишиванка біла',
                                'description' => 'Чоловіча вишиванка білого кольору',
                            ],
                            [
                                'isActive'    => false,
                                'title'       => 'Чоловіча вишиванка льняна',
                                'description' => 'Чоловіча вишиванка із льону',
                            ],
                        ],
                    ],
                    [
                        'level'         => 1,
                        'categoryOrder' => 0,
                        'categoryName'  => 'Жіночі вишиванки',
                        'isActive'      => true,
                        'products'      => [
                            [
                                'isActive'    => true,
                                'title'       => 'Жіноча вишиванка льняна',
                                'description' => 'Жіноча вишиванка із льону',
                                'commodities' => [
                                    [
                                        'operation'          => CommodityOperationType::IMPORT->name,
                                        'operationTimestamp' => $ts[6],
                                        'amount'             => 100,
                                        'prices'             => [
                                            ['price' => 1000],
                                            ['price' => 2000],
                                            ['price' => 3000],
                                        ],
                                        'sales'              => [
                                             [
                                                [
                                                    'operation'          => CommodityOperationType::PREORDER->name,
                                                    'amount'             => 5,
                                                    'operationTimestamp' => $ts[0],
                                                ],
                                                [
                                                    'operation'          => CommodityOperationType::PREORDER->name,
                                                    'amount'             => 2,
                                                    'operationTimestamp' => $ts[1],
                                                ],
                                                [
                                                    'operation'          => CommodityOperationType::PREORDER->name,
                                                    'amount'             => 3,
                                                    'operationTimestamp' => $ts[2],
                                                ],
                                            ],
                                             [
                                                 [
                                                     'operation'          => CommodityOperationType::PREORDER->name,
                                                     'amount'             => 6,
                                                     'operationTimestamp' => $ts[3],
                                                 ],
                                                 [
                                                     'operation'          => CommodityOperationType::PREORDER->name,
                                                     'amount'             => 4,
                                                     'operationTimestamp' => $ts[4],
                                                 ],
                                                 [
                                                     'operation'          => CommodityOperationType::PREORDER->name,
                                                     'amount'             => 2,
                                                     'operationTimestamp' => $ts[5],
                                                 ],
                                             ],
                                             [
                                                 [
                                                     'operation'          => CommodityOperationType::PREORDER->name,
                                                     'amount'             => 7,
                                                     'operationTimestamp' => $ts[6],
                                                 ],
                                                 [
                                                     'operation'          => CommodityOperationType::PREORDER->name,
                                                     'amount'             => 8,
                                                     'operationTimestamp' => $ts[7],
                                                 ],
                                                 [
                                                     'operation'          => CommodityOperationType::PREORDER->name,
                                                     'amount'             => 1,
                                                     'operationTimestamp' => $ts[8],
                                                 ],
                                             ],
                                        ],
                                    ],
                                    [
                                        'operation'          => CommodityOperationType::IMPORT->name,
                                        'operationTimestamp' => $ts[3],
                                        'amount'             => 50,
                                        'prices'             => [
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
                            [
                                'isActive'    => true,
                                'title'       => 'Жіноча вишиванка біла',
                                'description' => 'Жіноча вишиванка білого кольору',
                                'commodities' => [
                                    [
                                        'operation'          => CommodityOperationType::IMPORT->name,
                                        'operationTimestamp' => $ts[4],
                                        'amount'             => 8,
                                        'prices'             => [
                                            ['price' => 17000],
                                        ],

                                    ],
                                ],
                            ],
                            [
                                'isActive'    => true,
                                'title'       => 'Жіноча вишиванка чорна',
                                'description' => 'Жіноча вишиванка чорного кольору',
                                'commodities' => [
                                    [
                                        'operation'          => CommodityOperationType::IMPORT->name,
                                        'operationTimestamp' => $ts[5],
                                        'amount'             => 25,
                                        'prices'             => [
                                            ['price' => 20000],
                                        ],

                                    ],
                                ],
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
                            [
                                'isActive'    => true,
                                'title'       => 'Ігольниця вишита хрестиком',
                                'description' => 'Ігольниця вишита хрестиком вовною',

                            ],
                            [
                                'isActive'    => false,
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
                            [
                                'isActive'    => true,
                                'title'       => 'Прикраса велика',
                                'description' => 'Прикраса велика 20 см',
                            ],
                            [
                                'isActive'    => true,
                                'title'       => 'Прикраса мала',
                                'description' => 'Прикраса мала 10 см',
                            ],

                        ],
                    ],
                    [
                        'level'         => 1,
                        'categoryOrder' => 2,
                        'categoryName'  => 'Прикраси квадратні',
                        'isActive'      => true,
                    ],
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
                                $c->setOperationTimestamp($commodity['operationTimestamp']);
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
                                    foreach ($commodity['sales'] as $sale) {
                                        $order = new Order();
                                        foreach ($sale as $saleCommodity) {
                                            $sc = new Commodity();
                                            $sc->setOperationType($saleCommodity['operation']);
                                            $sc->setAmount($saleCommodity['amount']);
                                            $sc->setCommoditySource($c);
                                            $sc->setProduct($p);
                                            $sc->setOperationTimestamp($saleCommodity['operationTimestamp']);
                                            $manager->persist($sc);
                                            $order->addCommodity($sc);
                                            $manager->persist($order);
                                        }
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

        $users = [
            [
                'email'    => 'admin@testuser.com',
                'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
                'password' => 'secretP@sswd!1',
            ],
            [
                'email'    => 'usualuser@testuser.com',
                'roles'    => ['ROLE_USER'],
                'password' => 'secretP@sswd!2',
            ],
        ];


        foreach ($users as $user) {
            $ue = new User();
            $ue->setEmail($user['email']);
            $ue->setRoles($user['roles']);
            $ue->setPassword($this->userPasswordHasherInterface->hashPassword($ue, $user['password']));
            $manager->persist($ue);
        }

        $manager->flush();
    }
}
