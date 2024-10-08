<?php

use App\Enums\Roles;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->attributes = [
        'id' => 1,
        'slug' => fake()->slug,
        'name' => fake()->name(),
        'last_name' => fake()->lastName(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => null,
        'password' => Hash::make('password'),
        'date_of_birth' => fake()->date(),
        'phone_number' => fake()->phoneNumber(),
        'status' => fake()->randomElement([UserStatus::Active->value, UserStatus::Inactive->value]),
        'roles' => [Roles::SuperAdministrator->value],
        'remember_token' => null,
        'instrument_id' => null,
        'voice_id' => null,
        'entity_id' => null
    ];

});

it('can create admin users', function () {
    $this->withoutExceptionHandling();

    $attributes = $this->attributes;

    $response = $this->postJson(route('api.v1.admin-users.store'), $attributes)->assertCreated();

    $user = User::first();
    unset($attributes['password']);
    unset($attributes['remember_token']);
    $attributes['created_at'] = $user->created_at->toISOString();
    $attributes['updated_at'] = $user->updated_at->toISOString();
    expect($response->headers->get('Location'))->toBe(route('api.v1.admin-users.show', $user))
        ->and($response->json())->toMatchArray([
            'data' => [
                'type' => 'users',
                'id' => (string)$user->getRouteKey(),
                'attributes' => $attributes,
                'links' => [
                    'self' => route('api.v1.admin-users.show', $user)
                ]
            ]
        ]);
});

describe('validation errors', function () {
    it('requires a slug', function () {
        $attributes = $this->attributes;
        $attributes['slug'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('slug');
    });

    it('slug must be unique', function () {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.admin-users.store'), $user->toArray())
            ->assertJsonApiValidationErrors('slug');
    });

    it('slug must only contain letters numbers and dashes', function () {
        $user = User::factory()->create();
        $user->slug = 'invalid slug';
        $this->postJson(route('api.v1.admin-users.store'), $user->toArray())
            ->assertJsonApiValidationErrors('slug');
    });

    it('slug must not contain underscores', function () {
        $user = User::factory()->create();
        $user->slug = 'invalid_slug';
        $this->postJson(route('api.v1.admin-users.store'), $user->toArray())
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    });

    it('slug must not start with dashes', function () {
        $user = User::factory()->create();
        $user->slug = '-invalid-slug';
        $this->postJson(route('api.v1.admin-users.store'), $user->toArray())
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    });

    it('slug must not end with dashes', function () {
        $user = User::factory()->create();
        $user->slug = 'invalid-slug-';
        $this->postJson(route('api.v1.admin-users.store'), $user->toArray())
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    });

    it('requires a name', function () {
        $attributes = $this->attributes;
        $attributes['name'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('name');
    });

    it('requires a last name', function () {
        $attributes = $this->attributes;
        $attributes['last_name'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('last_name');
    });

    it('requires an email', function () {
        $attributes = $this->attributes;
        $attributes['email'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('email');
    });

    it('requires a valid email', function () {
        $attributes = $this->attributes;
        $attributes['email'] = 'invalid-email';
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('email');
    });

    it('requires a unique email', function () {
        $attributes = $this->attributes;
        User::factory()->create(['email' => $attributes['email']]);
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('email');
    });

    it('requires a password', function () {
        $attributes = $this->attributes;
        $attributes['password'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('password');
    });

    it('requires a date of birth', function () {
        $attributes = $this->attributes;
        $attributes['date_of_birth'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('date_of_birth');
    });

    it('requires a valid date of birth', function () {
        $attributes = $this->attributes;
        $attributes['date_of_birth'] = 'invalid-date';
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('date_of_birth');
    });

    it('requires a phone number', function () {
        $attributes = $this->attributes;
        $attributes['phone_number'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('phone_number');
    });

    it('requires a status', function () {
        $attributes = $this->attributes;
        $attributes['status'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('status');
    });

    it('requires a valid status', function () {
        $attributes = $this->attributes;
        $attributes['status'] = 'invalid-status';
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('status');
    });

    it('requires a roles', function () {
        $attributes = $this->attributes;
        $attributes['roles'] = null;
        $this->postJson(route('api.v1.admin-users.store'), $attributes)
            ->assertJsonApiValidationErrors('roles');
    });
});
