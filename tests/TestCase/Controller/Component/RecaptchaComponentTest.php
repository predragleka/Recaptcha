<?php
declare(strict_types=1);

namespace Recaptcha\View\Helper\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest as Request;
use Cake\TestSuite\TestCase;
use Recaptcha\Controller\Component\RecaptchaComponent;

/**
 * Test case for RecaptchaComponentTest.
 */
class RecaptchaComponentTest extends TestCase
{
    /**
     * @var \Cake\Controller\Controller
     */
    protected $controller;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Recaptcha\Controller\Component\RecaptchaComponent
     */
    protected $Recaptcha;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->controller = new Controller(new Request());

        $this->Recaptcha = $this->getMockBuilder(RecaptchaComponent::class)
                                ->onlyMethods(['apiCall'])
                                ->setConstructorArgs([
                                    new ComponentRegistry($this->controller),
                                    [
                                        'enable' => true,
                                        'sitekey' => 'sitekey',
                                        'theme' => 'theme',
                                        'type' => 'type',
                                        'lang' => 'lang',
                                    ],
                                ])
                                ->getMock();
    }

    /**
     * testVerifyFalse
     *
     * @return void
     */
    public function testVerifyFalse()
    {
        $this->assertFalse($this->Recaptcha->verify());

        $this->controller->setRequest(
            $this->controller->getRequest()->withData('g-recaptcha-response', 'foo')
        );

        $this->Recaptcha->expects($this->once())
                        ->method('apiCall')
                        ->will($this->returnValue(null));

        $this->assertFalse($this->Recaptcha->verify());
    }

    /**
     * testVerifyTrue
     *
     * @return void
     */
    public function testVerifyTrue(): void
    {
        $this->controller->setRequest(
            $this->controller->getRequest()->withData('g-recaptcha-response', 'foo')
        );

        $this->Recaptcha->expects($this->once())
                        ->method('apiCall')
                        ->will($this->returnValue(['success' => true]));

        $this->assertTrue($this->Recaptcha->verify());

        $this->Recaptcha->setConfig('enable', false);
        $this->assertTrue($this->Recaptcha->verify());
    }
}
