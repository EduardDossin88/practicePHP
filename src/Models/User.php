<?php

namespace App\Models;

final class User extends BaseModel
{
    public ?int $id = null;
    public string $country;
    public string $city;
    public string $gender;
    public int $salary;
    public string $first_name;
    public string $last_name;
    public string $birth_date;
    public string $registration_date;
    public int $is_active;

    protected static function getTableName(): string
    {
        return 'users';
    }

    /**
     * @param array<string, mixed> $data
     */
    protected static function create(array $data): static
    {
        $user = new self();
        $user->id = $data['id'] ?? null;
        $user->country = $data['country'] ?? '';
        $user->city = $data['city'] ?? '';
        $user->gender = $data['gender'] ?? '';
        $user->salary = (int)($data['salary'] ?? 0);
        $user->first_name = $data['first_name'] ?? '';
        $user->last_name = $data['last_name'] ?? '';
        $user->birth_date = $data['birth_date'] ?? '';
        $user->registration_date = $data['registration_date'] ?? '';
        $user->is_active = (int)($data['is_active'] ?? 0);
        return $user;
    }
}
