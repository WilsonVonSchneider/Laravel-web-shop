<?php

namespace App\Repositories\Admin\Products;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\ProductCategory;
use App\Models\ProductPriceList;

class ProductPriceListRepository
{

    public function paginated(string|null $search, string $sortBy, string $sort, int $perPage, int $page) : LengthAwarePaginator
    {
        $query = ProductPriceList::query();

        if ($search) {
            $query->where('name', 'ilike', '%' . $search . '%');
        }

        if ($sortBy && in_array($sortBy, ['name'])) {
            $query->orderBy($sortBy, $sort ?? 'asc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(string $productPriceListId) : ProductPriceList|null
    {
        return ProductPriceList::find($productPriceListId);
    }

    public function create(array $data): ProductPriceList
    {
        $data['sku'] = ProductPriceList::generateSku();
        return ProductPriceList::create($data);
    }

    public function update(productPriceList $productPriceList, array $data) : bool
    {
        return $productPriceList->update($data);
    }

    public function delete(productPriceList $productPriceList): void
    {
        $productPriceList->delete();
    }
}
