<?php

namespace Tests\Unit;

use App\Services\CloudFileServices\S3FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class S3FileServiceTest extends TestCase
{
    public function test_it_can_upload_file()
    {
        $ownerId = 1;

        $fakePath = "images/$ownerId/profile-pictures/fake_image.jpg";
        Storage::shouldReceive('disk->put')->once()->andReturn($fakePath);

        $fakeTmpUrl = "https://s3-bucket/images/$ownerId/profile-pictures/fake_image.jpg/XXDDDFFF";
        Storage::shouldReceive('disk->temporaryUrl')->once()->andReturn($fakeTmpUrl);

        $service = new S3FileService();
        $file = UploadedFile::fake()->image('fake_image.jpg');
        $result = $service->upload($ownerId, $file, 'images', 'profile-pictures');

        $expectedResult = [
            'url' => $fakeTmpUrl,
            'path' => $fakePath,
            'owner_id' => $ownerId,
        ];

        $this->assertTrue($this->arraysHaveSameValue($expectedResult, $result));
    }
}
