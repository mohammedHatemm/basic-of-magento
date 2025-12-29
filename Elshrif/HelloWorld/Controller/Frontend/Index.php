<?php
declare(strict_types=1);

namespace Elshrif\HelloWorld\Controller\Frontend;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private PageFactory $pageFactory,
        private RequestInterface $request,

    )
    {
//        $this->pageFactory = $pageFactory;
//        $this->request = $request;

    }

    public function execute()
    {

        $id = $this->request->getParam('id');

        $page = $this->pageFactory->create();

        $page->getConfig()->getTitle()->prepend(__('Hello World'));

        return $page;


    }

}
