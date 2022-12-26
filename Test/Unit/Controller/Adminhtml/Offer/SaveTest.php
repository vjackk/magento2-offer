<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Test\Unit\Controller\Adminhtml\Offer;

use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use PHPUnit\Framework\TestCase;
use Vjackk\Offer\Api\OfferRepositoryInterface;
use Vjackk\Offer\Controller\Adminhtml\Offer\PostDataProcessor;
use Vjackk\Offer\Controller\Adminhtml\Offer\Save;
use Vjackk\Offer\Model\OfferFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;

class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private $controller;

    /**
     * @var PostDataProcessor|MockObject
     */
    private $dataProcessorMock;

    /**
     * @var DataPersistorInterface|MockObject
     */
    private $dataPersistorMock;

    /**
     * @var OfferFactory|MockObject
     */
    private $offerFactoryMock;

    /**
     * @var OfferRepositoryInterface|MockObject
     */
    private $offerRepositoryMock;

    /**
     * @var TypeListInterface|MockObject
     */
    private $cacheTypeListMock;

    /**
     * @var Pool|MockObject
     */
    private $cacheFrontendPoolMock;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var array
     */
    private $formData = [
        'offer_id' => 1,
        'label' => 'Offer test label',
        'image_data' => '[{"name":"Test.png","full_path":"Test.png","type":"image\/png","tmp_name":"\/tmp\/phpd08ZeW","error":"0","size":"37934","file":"Test.png","url":"https:\/\/app.test-technique-dnd.test\/media\/tmp\/offer\/image\/Test.png","previewType":"image","id":"Q2FwdHVyZSBk4oCZw6ljcmFuIDIwMjItMTItMjEgMDk1NjQ5LnBuZw,,"}]',
        'image_alt' => 'Offer test image alt',
        'redirect_link' => 'https://google.com',
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getPostValue']
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->dataProcessorMock = $this->getMockBuilder(PostDataProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataPersistorMock = $this->getMockBuilder(DataPersistorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->offerFactoryMock = $this->getMockBuilder(OfferFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->offerRepositoryMock = $this->getMockBuilder(OfferRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cacheTypeListMock = $this->getMockBuilder(TypeListInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cacheFrontendPoolMock = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Save::class,
            [
                'context' => $contextMock,
                'dataProcessor' => $this->dataProcessorMock,
                'dataPersistor' => $this->dataPersistorMock,
                'offerFactory' => $this->offerFactoryMock,
                'offerRepository' => $this->offerRepositoryMock,
                'cacheTypeList' => $this->cacheTypeListMock,
                'cacheFrontendPool' => $this->cacheFrontendPoolMock,
            ]
        );
    }

    /**
     * Testing of execute method, redirect if get data from form is empty
     */
    public function testExecute()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(null);

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPath'])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
