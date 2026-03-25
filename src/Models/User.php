<?php

declare(strict_types=1);

namespace App\Models;

/**
 * User model and mapper.
 */
final class User extends BaseModel
{
    public int $id = 0;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $birth_date = '';
    public int $is_active = 0;
    public int $has_children = 0;
    public string $created_at = '';

    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): self
    {
        $user = new self();
        $user->id = (int) ($data['id'] ?? 0);
        $user->first_name = (string) ($data['first_name'] ?? '');
        $user->last_name = (string) ($data['last_name'] ?? '');
        $user->email = (string) ($data['email'] ?? '');
        $user->birth_date = (string) ($data['birth_date'] ?? '');
        $user->is_active = (int) ($data['is_active'] ?? 0);
        $user->has_children = (int) ($data['has_children'] ?? 0);
        $user->created_at = (string) ($data['created_at'] ?? '');

        return $user;
    }

    protected static function tableName(): string
    {
        return 'users';
    }
}
