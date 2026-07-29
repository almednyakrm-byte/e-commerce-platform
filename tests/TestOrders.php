<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\OrdersController;
use App\Repository\OrdersRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use PDOStatement;

class TestOrders extends TestCase
{
    private $ordersController;
    private $ordersRepository;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->ordersRepository = $this->createMock(OrdersRepository::class);
        $this->ordersController = new OrdersController($this->ordersRepository);
    }

    public function testGetOrders()
    {
        $expectedResponse = ['orders' => []];
        $this->ordersRepository->expects($this->once())
            ->method('getAllOrders')
            ->willReturn($expectedResponse);
        $response = $this->ordersController->getOrders();
        $this->assertEquals($expectedResponse, $response);
    }

    public function testCreateOrder()
    {
        $orderData = ['customer_name' => 'John Doe', 'order_date' => '2022-01-01'];
        $expectedResponse = ['message' => 'Order created successfully'];
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->createMock(PDOStatement::class));
        $this->pdo->expects($this->once())
            ->method('execute')
            ->with($orderData);
        $this->ordersRepository->expects($this->once())
            ->method('insertOrder')
            ->with($orderData);
        $response = $this->ordersController->createOrder($orderData);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testUpdateOrder()
    {
        $orderId = 1;
        $orderData = ['customer_name' => 'Jane Doe', 'order_date' => '2022-01-02'];
        $expectedResponse = ['message' => 'Order updated successfully'];
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->createMock(PDOStatement::class));
        $this->pdo->expects($this->once())
            ->method('execute')
            ->with($orderData);
        $this->ordersRepository->expects($this->once())
            ->method('updateOrder')
            ->with($orderId, $orderData);
        $response = $this->ordersController->updateOrder($orderId, $orderData);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testDeleteOrder()
    {
        $orderId = 1;
        $expectedResponse = ['message' => 'Order deleted successfully'];
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->createMock(PDOStatement::class));
        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['id' => $orderId]);
        $this->ordersRepository->expects($this->once())
            ->method('deleteOrder')
            ->with($orderId);
        $response = $this->ordersController->deleteOrder($orderId);
        $this->assertEquals($expectedResponse, $response);
    }
}



// App\Controller\OrdersController.php

namespace App\Controller;

use App\Repository\OrdersRepository;
use PDO;

class OrdersController
{
    private $ordersRepository;

    public function __construct(OrdersRepository $ordersRepository)
    {
        $this->ordersRepository = $ordersRepository;
    }

    public function getOrders()
    {
        return $this->ordersRepository->getAllOrders();
    }

    public function createOrder(array $orderData)
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE orders (id INTEGER PRIMARY KEY, customer_name TEXT, order_date TEXT)');
        $stmt = $pdo->prepare('INSERT INTO orders (customer_name, order_date) VALUES (:customer_name, :order_date)');
        $stmt->execute($orderData);
        $pdo = null;
        return ['message' => 'Order created successfully'];
    }

    public function updateOrder(int $orderId, array $orderData)
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE orders (id INTEGER PRIMARY KEY, customer_name TEXT, order_date TEXT)');
        $stmt = $pdo->prepare('UPDATE orders SET customer_name = :customer_name, order_date = :order_date WHERE id = :id');
        $stmt->execute($orderData);
        $pdo = null;
        return ['message' => 'Order updated successfully'];
    }

    public function deleteOrder(int $orderId)
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE orders (id INTEGER PRIMARY KEY, customer_name TEXT, order_date TEXT)');
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->execute(['id' => $orderId]);
        $pdo = null;
        return ['message' => 'Order deleted successfully'];
    }
}



// App\Repository\OrdersRepository.php

namespace App\Repository;

class OrdersRepository
{
    public function getAllOrders()
    {
        return ['orders' => []];
    }

    public function insertOrder(array $orderData)
    {
        // Implement database logic to insert order
    }

    public function updateOrder(int $orderId, array $orderData)
    {
        // Implement database logic to update order
    }

    public function deleteOrder(int $orderId)
    {
        // Implement database logic to delete order
    }
}