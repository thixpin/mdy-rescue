<?php

namespace App\Imports;

use App\Enums\Currency;
use App\Models\Donation;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DonationsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Handle different date formats
            $date = $row['donate_date'];
            if (is_numeric($date)) {
                // If it's an Excel date number
                $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            } else {
                // Try to parse the date string
                $date = Carbon::parse($date);
            }

            return new Donation([
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'donation_amount' => $row['donation_amount'],
                'currency' => $row['currency'] ?? 'MMK',
                'amount_in_text' => $row['amount_in_text'],
                'donate_date' => $date->format('Y-m-d H:i:s'),
                'verified' => false,
                // 'certificate_url' => $row['certificate_url'] ?? null,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error processing row: '.json_encode($row).'. Error: '.$e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'currency' => ['required', 'string', 'in:'.implode(',', Currency::values())],
            'donation_amount' => 'required|numeric|min:0',
            'amount_in_text' => 'nullable|string',
            'donate_date' => 'required',
            'certificate_url' => 'nullable|url',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The name field is required.',
            'donation_amount.required' => 'The donation amount field is required.',
            'donation_amount.numeric' => 'The donation amount must be a number.',
            'donation_amount.min' => 'The donation amount must be greater than 0.',
            'donate_date.required' => 'The donate date field is required.',
            'certificate_url.url' => 'The certificate URL must be a valid URL.',
            // 'currency.string' => 'The currency must be a string.',
            // 'currency.in' => 'The currency must be one of the following: '.implode(', ', Currency::values()),
        ];
    }
}
