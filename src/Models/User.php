<?php

namespace App\Models;

final class User extends BaseModel
{
    public ?int $id = null;
    public string $country;
    public string $city;
    public int $is_active;
    public string $gender;
    public string $birth_date;
    public int $salary;
    public int $has_children;
    public string $family_status;
    public string $registration_date;


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
        $user->is_active = (int)($data['is_active'] ?? 0);
        $user->gender = $data['gender'] ?? '';
        $user->birth_date = $data['birth_date'] ?? '';
        $user->salary = (int)($data['salary'] ?? 0);
        $user->has_children = (int)($data['has_children'] ?? 0);
        $user->family_status = $data['family_status'] ?? '';
        $user->registration_date = $data['registration_date'] ?? '';

        return $user;
    }
}
