<?php

namespace App\Http\Requests;

use App\Tenancy\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'productName' => ['required', 'string'],
            'sellPrice' => ['required', 'numeric'],
            'sellPriceBs' => ['nullable', 'numeric'],
            'saleType' => ['required', 'in:unit,weight'],
            'lowStockThreshold' => ['required', 'integer', 'min:0'],
            'barcode' => [
                'nullable',
                'string',
                Rule::unique('products', 'barcode')
                    ->where('tenant_id', app(TenantContext::class)->id())
                    ->ignore($productId),
            ],
        ];
    }
}
