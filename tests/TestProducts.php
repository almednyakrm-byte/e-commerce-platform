<?php

namespace App\Tests\Controller;

use App\Controller\ProductsController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestProducts extends TestCase
{
    private $productsController;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->productsController = new ProductsController($this->pdoMock);
    }

    public function testGetProducts()
    {
        $expectedResponse = new JsonResponse(['products' => ['product1', 'product2']]);
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM products')
            ->willReturn($expectedResponse);
        $response = $this->productsController->getProducts();
        $this->assertEquals($expectedResponse, $response);
    }

    public function testPostProduct()
    {
        $product = ['name' => 'product1', 'price' => 10.99];
        $expectedResponse = new JsonResponse(['message' => 'Product created successfully']);
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO products (name, price) VALUES (:name, :price)')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->with($product);
        $response = $this->productsController->postProduct($product);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testPutProduct()
    {
        $product = ['name' => 'product1', 'price' => 10.99];
        $expectedResponse = new JsonResponse(['message' => 'Product updated successfully']);
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET name = :name, price = :price WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->with($product);
        $response = $this->productsController->putProduct($product);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testDeleteProduct()
    {
        $id = 1;
        $expectedResponse = new JsonResponse(['message' => 'Product deleted successfully']);
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM products WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->pdoMock->expects($this->once())
            ->method('execute')
            ->with(['id' => $id]);
        $response = $this->productsController->deleteProduct($id);
        $this->assertEquals($expectedResponse, $response);
    }
}



// ProductsController.php

namespace App\Controller;

use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductsController
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getProducts(): JsonResponse
    {
        $stmt = $this->pdo->query('SELECT * FROM products');
        $products = $stmt->fetchAll();
        return new JsonResponse(['products' => $products]);
    }

    public function postProduct(array $product): JsonResponse
    {
        $stmt = $this->pdo->prepare('INSERT INTO products (name, price) VALUES (:name, :price)');
        $stmt->execute($product);
        return new JsonResponse(['message' => 'Product created successfully']);
    }

    public function putProduct(array $product): JsonResponse
    {
        $stmt = $this->pdo->prepare('UPDATE products SET name = :name, price = :price WHERE id = :id');
        $stmt->execute($product);
        return new JsonResponse(['message' => 'Product updated successfully']);
    }

    public function deleteProduct(int $id): JsonResponse
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return new JsonResponse(['message' => 'Product deleted successfully']);
    }
}