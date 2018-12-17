<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ImageUploadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_upload_image()
    {
        $this->signIn();
        Storage::fake('tmp');

        $this->post('/admin/images', [
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ])->seeStatusCode(200);
    }
}
