<?php

namespace Frontpage\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;

class PageController extends AbstractActionController
{
    /**
     * @var \Frontpage\Service\Page
     */
    private $pageService;

    public function __construct(\Frontpage\Service\Page $pageService)
    {
        $this->pageService = $pageService;
    }

    public function pageAction()
    {
        $category = $this->params()->fromRoute('category');
        $subCategory = $this->params()->fromRoute('sub_category');
        $name = $this->params()->fromRoute('name');
        $page = $this->pageService->getPage($category, $subCategory, $name);
        $parents = $this->pageService->getPageParents($page);

        if (is_null($page)) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'page' => $page,
            'parents' => $parents
        ]);
    }
}
