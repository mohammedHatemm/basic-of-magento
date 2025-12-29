<?php
declare(strict_types=1);
namespace Elshrif\HelloWorld\Controller\Frontend;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;


class Save implements HttpPostActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private RedirectFactory $redirectFactory,
        private Validator $formKeyValidator,
        private ManagerInterface $messageManager
    ){}

    public function execute():Redirect
    {
        $redirect = $this->redirectFactory->create();
        if(!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('Invalid request'));
            return $redirect->setPath('*/*/');

        }
        try {
            $data = $this->request->getPostValue();
            //the data you want to save it

            $this->messageManager->addSuccessMessage(__('your data has been saved.'));
            $redirect->setPath('*/*/index');
            return $redirect;
        }
        catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $redirect->setPath('*/*/');
        }
    }
}
