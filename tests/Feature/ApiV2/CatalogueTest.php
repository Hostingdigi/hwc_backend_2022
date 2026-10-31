<?php

use Tests\Feature\ApiV2\ApiV2Fixtures;
use Tests\Support\Fixtures;

uses(ApiV2Fixtures::class);

beforeEach(fn () => $this->seedStore());

it('lists categories with their sub-categories', function () {
    $this->getJson('/api/v2/categories')->assertOk()
        ->assertJsonPath('data.0.name', 'Tools')
        ->assertJsonPath('data.0.children.0.name', 'Power Tools');
});

it('searches and filters products, with prices including GST', function () {
    Fixtures::insert('products', ['EnName' => 'Hammer', 'Code' => 'HM-1', 'Price' => 10, 'StandardPrice' => 10, 'Quantity' => 0, 'ProdStatus' => 1, 'Types' => 10]);
    Fixtures::insert('products', ['EnName' => 'Hidden', 'Price' => 1, 'ProdStatus' => 0]);

    $this->getJson('/api/v2/products?search=drill')->assertOk()
        ->assertJsonCount(1, 'data')->assertJsonPath('data.0.price', 54.5)->assertJsonPath('data.0.in_stock', true);

    // category 10 includes its sub-category 11
    $this->getJson('/api/v2/products?category=10')->assertJsonCount(2, 'data');
    $this->getJson('/api/v2/products?brand=7')->assertJsonCount(1, 'data');
    $this->getJson('/api/v2/products?search=Hidden')->assertJsonCount(0, 'data');
    $this->getJson('/api/v2/products?sort=bogus')->assertStatus(422);
});

it('shows a product with options, gallery and approved reviews only', function () {
    Fixtures::insert('product_options', ['Prod' => $this->drill, 'Title' => 'Kit', 'Price' => 10, 'Quantity' => 3, 'Status' => 1]);
    Fixtures::insert('product_reviews', ['ProdId' => $this->drill, 'CustomerId' => 1, 'rating' => 4, 'comments' => 'Good', 'status' => 1]);
    Fixtures::insert('product_reviews', ['ProdId' => $this->drill, 'CustomerId' => 2, 'rating' => 1, 'comments' => 'Spam', 'status' => 0]);

    $this->getJson("/api/v2/products/{$this->drill}")->assertOk()
        ->assertJsonPath('data.options.0.title', 'Kit')
        ->assertJsonPath('data.rating.count', 1)
        ->assertJsonPath('data.reviews.0.comment', 'Good');

    $this->getJson('/api/v2/products/999999')->assertNotFound();
});

it('lists payment methods with public keys only, never secrets', function () {
    $response = $this->getJson('/api/v2/payment-methods')->assertOk();

    expect($response->getContent())->not->toContain('secret')
        ->and(collect($response->json('data'))->firstWhere('id', 3)['public_key'])->toBe('pk_test_public');
});

it('serves countries, static pages and the app version', function () {
    Fixtures::insert('static_pages', ['EnTitle' => 'FAQ', 'EnContent' => '<p>Q&A</p>', 'UniqueKey' => 'faq']);
    Fixtures::insert('mobileversions', ['version_id' => '20', 'version_name' => '2.0.0']);

    $this->getJson('/api/v2/countries')->assertOk()->assertJsonPath('data.0.code', 'SG');
    $this->getJson('/api/v2/pages/faq')->assertOk()->assertJsonPath('data.title', 'FAQ');
    $this->getJson('/api/v2/app/version')->assertOk()->assertJsonPath('data.version_name', '2.0.0');
});

it('answers errors in JSON even without an Accept header', function () {
    $this->get('/api/v2/products/999999')->assertNotFound()->assertHeader('Content-Type', 'application/json');
});
