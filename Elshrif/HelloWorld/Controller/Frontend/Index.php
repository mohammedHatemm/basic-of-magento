<?php
declare(strict_types=1);

namespace Elshri\HelloWorld\Controller\Frontend;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index implements HttpGetActionInterface {

    public function __construct(
        PageFactory $pageFactory,
        RequestInterface $request,
    ){}

    public function execute():page
    {
        $id = $this->request->getParam('id');
        $page = $pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Hello World'));
        return $page;

    }

}
