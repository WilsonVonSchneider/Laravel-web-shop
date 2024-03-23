<?php

namespace App\Services\Admin\Products;

use App\Repositories\Admin\Products\ProductPriceListRepository;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\ProductCategory;
use App\Models\ProductPriceList;

class ProductPriceListService
{
    private $productPriceListRepository;

    public function __construct(ProductPriceListRepository $productPriceListRepository)
    {
        $this->productPriceListRepository = $productPriceListRepository;
    }

    public function paginated(string|null $search, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        return $this->productPriceListRepository->paginated($search, $sortBy, $sort, $perPage, $page);
    }

    public function getById(string $productPriceListId) : ProductPriceList
    {
        $productPriceList =  $this->productPriceListRepository->getById($productPriceListId);

        if (!$productPriceList) {
            throw new \Exception('Product Price List not found', 404);
        }

        return $productPriceList;
    }

    public function create(array $data): ProductPriceList
    {
        return $this->productPriceListRepository->create($data);
    }

    public function update(string $productPriceListId, array $data) : bool
    {
        $productPriceList = $this->getById($productPriceListId);

        return $this->productPriceListRepository->update($productPriceList, $data);  
    }

    public function delete(string $productPriceListId) : void
    {
        $productPriceList = $this->getById($productPriceListId);
       
        $this->productPriceListRepository->delete($productPriceList);
    }
}

