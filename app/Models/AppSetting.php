<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function defaults(): array
    {
        return [
            'shop_name' => 'Excel Power',
            'shop_name_second_line' => 'Hardware and Tools',
            'address' => 'Address',
            'phone_number' => 'Phone Number',
            'email' => '',
            'logo' => '',
            'currency' => 'Rs.',
            'invoice_prefix' => 'INV',
            'print_size' => '80mm',
            'footer_message' => 'Thank you, Come again',
            'timezone' => config('app.timezone', 'UTC'),
            'language' => config('app.locale', 'en'),
        ];
    }

    public static function allAsArray(): array
    {
        return array_merge(
            self::defaults(),
            self::query()->pluck('value', 'key')->toArray()
        );
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return self::query()->where('key', $key)->value('value')
            ?? self::defaults()[$key]
            ?? $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => is_null($value) ? null : (string) $value]
        );
    }
}
