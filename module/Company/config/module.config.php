<?php

use Application\View\Helper\Truncate;
use Company\Controller\{
    AdminController,
    CompanyController,
};
use Company\Controller\Factory\{
    AdminControllerFactory,
    CompanyControllerFactory,
};
use Laminas\Router\Http\{
    Literal,
    Segment,
};

return [
    'router' => [
        'routes' => [
            'company' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/career',
                    'priority' => 2,
                    'defaults' => [
                        'controller' => CompanyController::class,
                        'action' => 'list', // index is reserved for some magical frontpage for the company module, but since it is not yet implemented, a company list will be presented.
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'jobList' => [
                        'priority' => 3,
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:category',
                            'constraints' => [
                                'category' => '[a-zA-Z0-9_\-\.]*',
                            ],
                            'defaults' => [
                                'action' => 'jobList',
                            ],
                        ],
                    ],
                    'spotlight' => [
                        'priority' => 3,
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/spotlight',
                            'defaults' => [
                                'action' => 'spotlight',
                            ],
                        ],
                    ],
                    'list' => [
                        'priority' => 3,
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/list',
                            'defaults' => [
                                'action' => 'list',
                                'slugCompanyName' => '',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'companyItem' => [
                        'priority' => 2,
                        'type' => Segment::class,
                        'options' => [
                            'defaults' => [
                                'action' => 'show',
                            ],
                            // url will be company/<slugCompanyName>/jobs/<slugJobName>/<action>
                            // slugjobname and slugcompanyname will be in database, and can be set from the admin panel
                            // company/apple should give page of apple
                            // company/apple/jobs should be list of jobs of apple
                            // company/apple/jobs/ceo should be the page of ceo job
                            // company should give frontpage of company part
                            // company/list should give a list of companies
                            // company/index should give the frontpage
                            'route' => '/company/:slugCompanyName',
                            'constraints' => [
                                'slugCompanyName' => '[a-zA-Z0-9_\-\.]*',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'joblist' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/:category',
                                    'defaults' => [
                                        'action' => 'jobList',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'job_item' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/:slugJobName',
                                            'constraints' => [
                                                'slugJobName' => '[a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'action' => 'jobs',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'priority' => 100,
            ],
            'admin_company' => [
                'priority' => 1000,
                'type' => Literal::class,
                'options' => [
                    'route' => '/admin/company',
                    'defaults' => [
                        'controller' => AdminController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'deleteCompany' => [
                        'priority' => 3,
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/delete/[:slugCompanyName]',
                            'defaults' => [
                                'action' => 'deleteCompany',
                            ],
                            'constraints' => [
                                'slugCompanyName' => '[a-zA-Z0-9_\-\.]*',
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'editCompany' => [
                        'priority' => 3,
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/edit/[:slugCompanyName]',
                            'defaults' => [
                                'action' => 'editCompany',
                            ],
                            'constraints' => [
                                'slugCompanyName' => '[a-zA-Z0-9_\-\.]*',
                            ],
                        ],
                        'may_terminate' => true,

                        'child_routes' => [
                            'editPackage' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/package/:packageId',
                                    'defaults' => [
                                        'action' => 'editPackage',
                                    ],
                                    'constraints' => [
                                        'packageId' => '[0-9]*',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'addJob' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/addJob',
                                            'defaults' => [
                                                'action' => 'addJob',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                    ],
                                    'deletePackage' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/delete',
                                            'defaults' => [
                                                'action' => 'deletePackage',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                    ],
                                    'editJob' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/job/:languageNeutralJobId',
                                            'defaults' => [
                                                'action' => 'editJob',
                                            ],
                                            'constraints' => [
                                                'languageNeutralJobId' => '[0-9]*',
                                            ],
                                            'may_terminate' => true,
                                        ],
                                    ],
                                    'deleteJob' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/job/:languageNeutralJobId/delete',
                                            'defaults' => [
                                                'action' => 'deleteJob',
                                            ],
                                            'may_terminate' => true,
                                        ],
                                    ],
                                ],
                            ],
                            'addPackage' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/addPackage/:type',
                                    'defaults' => [
                                        'action' => 'addPackage',
                                    ],
                                    'constraints' => [
                                        'type' => '[a-zA-Z0-9_-]*',
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'addJob' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/addJob',
                                    'defaults' => [
                                        'action' => 'addJob',
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'editJob' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/job/:jobName',
                                    'defaults' => [
                                        'action' => 'editJob',
                                    ],
                                    'constraints' => [
                                        'jobName' => '[a-zA-Z0-9_-]*',
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                        ],
                    ],
                    'editCategory' => [
                        'priority' => 3,
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/editCategory/:languageNeutralCategoryId',
                            'defaults' => [
                                'action' => 'editCategory',
                            ],
                            'constraints' => [
                                'languageNeutralCategoryId' => '\d+',
                            ],
                        ],
                    ],
                    'editLabel' => [
                        'priority' => 3,
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/editLabel/:languageNeutralLabelId',
                            'defaults' => [
                                'action' => 'editLabel',
                            ],
                            'constraints' => [
                                'languageNeutralLabelId' => '\d+',
                            ],
                        ],
                    ],
                    'default' => [
                        'priority' => 2,
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:action[/:slugCompanyName[/:slugJobName]]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AdminController::class => AdminControllerFactory::class,
            CompanyController::class => CompanyControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'company' => __DIR__ . '/../view/',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'company_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Model/'],
            ],
            'orm_default' => [
                'drivers' => [
                    'Company\Model' => 'company_entities',
                ],
            ],
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'truncate' => function () {
                return new Truncate();
            },
        ],
    ],
];
