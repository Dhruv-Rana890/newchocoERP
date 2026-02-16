<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentStatusRule = $this->input('pos') ? 'nullable' : 'required';

        // POS edit: when draft=1 we keep same reference_no, so ignore current sale_id
        $saleIdRaw = $this->input('sale_id');
        if (is_array($saleIdRaw)) {
            $saleIdRaw = $saleIdRaw[0] ?? null;
        }
        $saleId = (is_numeric($saleIdRaw) && (int) $saleIdRaw > 0) ? (int) $saleIdRaw : null;

        $referenceNoRule = ['nullable', 'string', 'max:191'];
        $referenceNoRule[] = $saleId !== null
            ? Rule::unique('sales', 'reference_no')->ignore($saleId, 'id')
            : 'unique:sales,reference_no';

        return [
            'reference_no'   => $referenceNoRule,
            'customer_id'    => 'required|exists:customers,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'currency_id'    => 'required',
            'item'           => 'required|min:1',
            'sale_status'    => 'required',
            'payment_status' => $paymentStatusRule,
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
        ];
    }
    
    public function messages(): array
    {
        return [
            'reference_no.unique'     => 'The reference number must be unique.',
            'customer_id.required'    => 'Please select a customer.',
            'warehouse_id.required'   => 'Please select a warehouse.',
            'item.required'           => 'Please add at least one item.',
            'sale_status.required'    => 'Sale status is required.',
            'payment_status.required' => 'Payment status is required.',
            'document.mimes'          => 'The document must be a file of type: jpg, jpeg, png, gif, pdf, csv, docx, xlsx, txt.',
        ];
    }
}
