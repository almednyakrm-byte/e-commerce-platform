<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\PaymentsController;
use App\Repository\PaymentsRepository;
use App\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class TestPayments extends TestCase
{
    private $controller;
    private $repository;
    private $router;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock('PDO');
        $this->repository = $this->createMock(PaymentsRepository::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->controller = new PaymentsController($this->repository, $this->router);
    }

    public function testGetPayments(): void
    {
        $payments = [
            new Payment(1, 'John Doe', 100.0),
            new Payment(2, 'Jane Doe', 200.0),
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($payments);

        $response = $this->controller->getPayments();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($payments), $response->getContent());
    }

    public function testGetPayment(): void
    {
        $payment = new Payment(1, 'John Doe', 100.0);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($payment);

        $response = $this->controller->getPayment(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($payment), $response->getContent());
    }

    public function testGetPaymentNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->getPayment(1);
    }

    public function testCreatePayment(): void
    {
        $payment = new Payment(1, 'John Doe', 100.0);
        $request = new Request([], [], ['name' => 'John Doe', 'amount' => 100.0]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO payments (name, amount) VALUES (:name, :amount)')
            ->willReturn($this->createMock('PDOStatement'));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['name' => 'John Doe', 'amount' => 100.0]);

        $response = $this->controller->createPayment($request);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(json_encode($payment), $response->getContent());
    }

    public function testUpdatePayment(): void
    {
        $payment = new Payment(1, 'John Doe', 100.0);
        $request = new Request([], [], ['name' => 'Jane Doe', 'amount' => 200.0]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE payments SET name = :name, amount = :amount WHERE id = :id')
            ->willReturn($this->createMock('PDOStatement'));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Jane Doe', 'amount' => 200.0, 'id' => 1]);

        $response = $this->controller->updatePayment(1, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($payment), $response->getContent());
    }

    public function testDeletePayment(): void
    {
        $payment = new Payment(1, 'John Doe', 100.0);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM payments WHERE id = :id')
            ->willReturn($this->createMock('PDOStatement'));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $response = $this->controller->deletePayment(1);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}


This test file covers the following scenarios:

1.  **GET /payments**: Tests that the `getPayments` method returns a list of all payments.
2.  **GET /payments/{id}**: Tests that the `getPayment` method returns a single payment by ID.
3.  **GET /payments/{id} (not found)**: Tests that the `getPayment` method throws a `NotFoundHttpException` when the payment is not found.
4.  **POST /payments**: Tests that the `createPayment` method creates a new payment.
5.  **PUT /payments/{id}**: Tests that the `updatePayment` method updates an existing payment.
6.  **DELETE /payments/{id}**: Tests that the `deletePayment` method deletes a payment.

Each test method uses the `createMock` method to create mock objects for the `PDO` and `PaymentsRepository` classes. The mock objects are configured to return specific values or throw exceptions when certain methods are called. This allows the test to isolate the behavior of the `PaymentsController` class and test its functionality in isolation.