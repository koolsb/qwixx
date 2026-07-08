<?php

declare(strict_types=1);

it('renders the picker with all three layouts', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Classic')
        ->assertSee('Mixed Numbers')
        ->assertSee('Mixed Colors');
});

it('links every layout to its solo and duo game routes', function () {
    $response = $this->get('/');

    foreach (['classic', 'mixed-numbers', 'mixed-colors'] as $id) {
        $response->assertSee("/play/$id/solo")->assertSee("/play/$id/duo");
    }
});
