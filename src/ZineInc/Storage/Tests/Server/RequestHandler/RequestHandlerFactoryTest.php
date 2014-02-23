<?php


namespace ZineInc\Storage\Tests\Server\RequestHandler;


use ZineInc\Storage\Server\RequestHandler\RequestHandlerFactory;

class RequestHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function requiredOptionsPassed_success()
    {
        //given

        $factory = new RequestHandlerFactory();

        //when

        $requestHandler = $factory->createRequestHandler(array(
            'storage.dir' => __DIR__,
            'secretKey' => 'abc',
        ));

        //then

        $this->assertInstanceOf('ZineInc\Storage\Server\RequestHandler\RequestHandler', $requestHandler);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function requiredOptionsMissing_error()
    {
        //given

        $factory = new RequestHandlerFactory();

        //when

        $factory->createRequestHandler(array());
    }
}
 