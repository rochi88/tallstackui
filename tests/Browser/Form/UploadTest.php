<?php

namespace Tests\Browser\Form;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Form;
use Livewire\Livewire;
use Livewire\WithFileUploads;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Finder\SplFileInfo;
use Tests\Browser\BrowserTestCase;

class UploadTest extends BrowserTestCase
{
    #[Test]
    public function can_close_after_upload(): void
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" wire:model.live="photo" close-after-upload />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg')
            ->assertNotVisible('@tallstackui_upload_floating');
    }

    #[Test]
    public function can_delete_existent_files()
    {
        Artisan::call('storage:link');

        File::ensureDirectoryExists(storage_path('app/public/test'));

        File::copy(__DIR__.'/../../Fixtures/test.jpeg', public_path('storage/test/test.jpeg'));

        $this->assertTrue(File::exists(public_path('storage/test/test.jpeg')));

        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function mount(): void
            {
                $this->photo = collect(File::allFiles(public_path('storage/test')))->map(fn (SplFileInfo $file) => [
                    'name' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'size' => $file->getSize(),
                    'path' => $file->getPathname(),
                    'url' => Storage::url('public/test/'.$file->getFilename()),
                ])->toArray();
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-upload label="Document" 
                              wire:model.live="photo" 
                              static 
                              delete />
                </div>
                HTML;
            }

            public function deleteUpload(array $content): void
            {
                if (! $this->photo) {
                    return;
                }

                File::delete($content['path']);

                $files = Arr::wrap($this->photo);

                $collect = collect($files)->filter(fn (array $item) => $item['name'] !== $content['real_name']);

                $this->photo = $collect->toArray();
            }
        })
            ->assertSee('Document')
            ->click('@tallstackui_upload_input')
            ->waitForText('test.jpeg')
            ->assertSee('test.jpeg')
            ->waitForLivewire()
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div[3]/ul/li/div[2]/button')
            ->assertMissing('@uploaded')
            ->waitForText('No images.')
            ->assertSee('No images.')
            ->waitForText('You don\'t have any image yet.')
            ->assertSee('You don\'t have any image yet.');

        $this->assertFalse(File::exists(public_path('storage/test/test.jpeg')));

        File::deleteDirectory(storage_path('app/public/test'));
    }

    #[Test]
    public function can_delete_file()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" wire:model.live="photo" delete />
                </div>
                HTML;
            }

            public function deleteUpload(array $content): void
            {
                if (! $this->photo) {
                    return;
                }

                $files = Arr::wrap($this->photo);

                /** @var UploadedFile $file */
                $file = collect($files)->filter(fn (UploadedFile $item) => $item->getFilename() === $content['temporary_name'])->first();

                rescue(fn () => $file->delete(), report: false); // @phpstan-ignore-line

                $collect = collect($files)->filter(fn (UploadedFile $item) => $item->getFilename() !== $content['temporary_name']);

                $this->photo = is_array($this->photo) ? $collect->toArray() : $collect->first();
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg')
            ->waitForLivewire()
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div[4]/ul/li/div[2]/button')
            ->assertMissing('@uploaded');
    }

    #[Test]
    public function can_delete_file_using_custom_method()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" wire:model.live="photo" delete delete-method="fooBar" />
                </div>
                HTML;
            }

            public function fooBar(array $content): void
            {
                if (! $this->photo) {
                    return;
                }

                $files = Arr::wrap($this->photo);

                /** @var UploadedFile $file */
                $file = collect($files)->filter(fn (UploadedFile $item) => $item->getFilename() === $content['temporary_name'])->first();

                rescue(fn () => $file->delete(), report: false); // @phpstan-ignore-line

                $collect = collect($files)->filter(fn (UploadedFile $item) => $item->getFilename() !== $content['temporary_name']);

                $this->photo = is_array($this->photo) ? $collect->toArray() : $collect->first();
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg')
            ->waitForLivewire()
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div[4]/ul/li/div[2]/button')
            ->assertMissing('@uploaded');
    }

    #[Test]
    public function can_only_see_footer_slot_when_not_empty()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>                    
                    <x-upload label="Document" wire:model="photo">
                        <x-slot:footer when-uploaded>
                            Foo Bar Baz
                        </x-slot:footer>
                    </x-upload>
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertDontSee('Foo Bar Baz')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->waitUntilMissingText('Foo Bar Baz')
            ->assertDontSee('Foo Bar Baz')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForText('Foo Bar Baz')
            ->assertSee('Foo Bar Baz');
    }

    #[Test]
    public function can_see_empty_state_for_static_usage()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>                    
                    <x-upload label="Document" wire:model="photo" static />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->click('@tallstackui_upload_input')
            ->waitForText('No images.')
            ->assertSee('No images.')
            ->waitForText('You don\'t have any image yet.')
            ->assertSee('You don\'t have any image yet.');
    }

    #[Test]
    public function can_see_footer_slot()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>                    
                    <x-upload label="Document" wire:model="photo">
                        <x-slot:footer>
                            Foo Bar Baz
                        </x-slot:footer>
                    </x-upload>
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertDontSee('Foo Bar Baz')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->waitForText('Foo Bar Baz')
            ->assertSee('Click here to upload')
            ->assertSee('Foo Bar Baz');
    }

    #[Test]
    public function can_see_preview(): void
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" wire:model.live="photo" />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg')
            ->click('@tallstackui_file_preview')
            ->waitFor('@tallstackui_file_preview_backdrop')
            ->assertVisible('@tallstackui_file_preview_backdrop');
    }

    #[Test]
    public function can_see_preview_for_existent_files()
    {
        Artisan::call('storage:link');

        File::ensureDirectoryExists(storage_path('app/public/test'));

        File::copy(__DIR__.'/../../Fixtures/test.jpeg', public_path('storage/test/test.jpeg'));

        $this->assertTrue(File::exists(public_path('storage/test/test.jpeg')));

        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function mount(): void
            {
                $this->photo = collect(File::allFiles(public_path('storage/test')))->map(fn (SplFileInfo $file) => [
                    'name' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'size' => $file->getSize(),
                    'path' => $file->getPathname(),
                    'url' => Storage::url('public/test/'.$file->getFilename()),
                ])->toArray();
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-upload label="Document" 
                              wire:model.live="photo" 
                              static />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->click('@tallstackui_upload_input')
            ->waitForText('test.jpeg')
            ->assertSee('test.jpeg')
            ->click('@tallstackui_file_preview')
            ->waitFor('@tallstackui_file_preview_backdrop')
            ->assertVisible('@tallstackui_file_preview_backdrop');

        File::deleteDirectory(storage_path('app/public/test'));
    }

    #[Test]
    public function can_see_tip()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>                    
                    <x-upload label="Document" tip="Accept pdf or png" wire:model="photo" />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertDontSee('Accept pdf or png')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->waitForText('Accept pdf or png')
            ->assertSee('Click here to upload')
            ->assertSee('Accept pdf or png');
    }

    #[Test]
    public function can_thrown_exception_if_property_bind_was_not_defined()
    {
        Livewire::visit(new class extends Component
        {
            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-upload label="Document" />
                </div>
                HTML;
            }
        })->assertSee('The [upload] component requires a property to bind using [wire:model].');
    }

    #[Test]
    public function can_upload_multiple_file(): void
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photos = [];

            protected $saved = [];

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($photos && count($photos) === 2)
                        <p dusk="upload-finish">Uploaded</p>
                        @if ($photos && $photos[0])
                            <p dusk="uploaded-0">{{ $photos[0]->getClientOriginalName() }}</p>
                        @endif
                        @if ($photos && $photos[1])
                            <p dusk="uploaded-1">{{ $photos[1]->getClientOriginalName() }}</p>
                        @endif
                    @endif
                    
                    <x-upload label="Document" wire:model="photos" multiple />
                </div>
                HTML;
            }

            public function updatingPhotos(): void
            {
                if (! is_array($this->photos)) {
                    return;
                }

                $this->saved = $this->photos;
            }

            public function updatedPhotos(): void
            {
                if (! $this->photos || ! is_array($this->photos)) {
                    return;
                }

                $file = Arr::flatten(array_merge($this->saved, [$this->photos]));

                $this->photos = collect($file)->unique(fn (UploadedFile $item) => $item->getClientOriginalName())->toArray();

                // This is only necessary in tests, to make sure
                // the order of the files is always the same
                $this->photos = array_values($this->photos);
            }

            public function sync()
            {
                // ...
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded-0')
            ->assertMissing('@uploaded-1')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.pdf')
            // This is necessary because Livewire always send
            // request to the backend when file is uploaded
            ->waitForTextIn('@upload-finish', 'Uploaded')
            ->waitForTextIn('@uploaded-0', 'test.jpeg')
            ->assertSeeIn('@uploaded-0', 'test.jpeg')
            ->waitForTextIn('@uploaded-1', 'test.pdf')
            ->assertSeeIn('@uploaded-1', 'test.pdf');
    }

    #[Test]
    public function can_upload_single_file(): void
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" wire:model.live="photo" />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg');
    }

    #[Test]
    public function can_upload_single_file_using_livewire_form(): void
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public Upload $form;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($form)
                        <p dusk="uploaded">{{ $form->photo?->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" wire:model.live="form.photo" />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg');
    }

    #[Test]
    public function can_use_existent_files()
    {
        Artisan::call('storage:link');

        File::ensureDirectoryExists(storage_path('app/public/test'));

        File::copy(__DIR__.'/../../Fixtures/test.jpeg', public_path('storage/test/test.jpeg'));

        $this->assertTrue(File::exists(public_path('storage/test/test.jpeg')));

        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public function mount(): void
            {
                $this->photo = collect(File::allFiles(public_path('storage/test')))->map(fn (SplFileInfo $file) => [
                    'name' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'size' => $file->getSize(),
                    'path' => $file->getPath(),
                    'url' => Storage::url('test/'.$file->getFilename()),
                ])->toArray();
            }

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    <x-upload label="Document" 
                              wire:model.live="photo" 
                              static />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('test.jpeg')
            ->assertSee('test.jpeg');

        File::deleteDirectory(storage_path('app/public/test'));
    }

    #[Test]
    public function can_use_remove_event()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public $removed = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($removed)
                        <p dusk="remove">{{ $removed }}</p>
                    @endif
                
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" 
                              wire:model.live="photo"
                              x-on:remove="$wire.set('removed', 'Remove')"
                              delete />
                </div>
                HTML;
            }

            public function deleteUpload(array $content): void
            {
                if (! $this->photo) {
                    return;
                }

                $files = Arr::wrap($this->photo);

                /** @var UploadedFile $file */
                $file = collect($files)->filter(fn (UploadedFile $item) => $item->getFilename() === $content['temporary_name'])->first();

                rescue(fn () => $file->delete(), report: false); // @phpstan-ignore-line

                $collect = collect($files)->filter(fn (UploadedFile $item) => $item->getFilename() !== $content['temporary_name']);

                $this->photo = is_array($this->photo) ? $collect->toArray() : $collect->first();
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg')
            ->waitForLivewire()
            ->clickAtXPath('/html/body/div[3]/div/div[2]/div[4]/ul/li/div[2]/button')
            ->assertMissing('@uploaded')
            ->assertVisible('@remove')
            ->waitForTextIn('@remove', 'Remove')
            ->assertSeeIn('@remove', 'Remove');
    }

    #[Test]
    public function can_use_upload_event()
    {
        Livewire::visit(new class extends Component
        {
            use WithFileUploads;

            public $photo;

            public $uploaded = null;

            public function render(): string
            {
                return <<<'HTML'
                <div>
                    @if ($uploaded)
                        <p dusk="upload">{{ $uploaded }}</p>
                    @endif
                    
                    @if ($photo)
                        <p dusk="uploaded">{{ $photo->getClientOriginalName() }}</p>
                    @endif
                    
                    <x-upload label="Document" 
                              wire:model.live="photo" 
                              x-on:upload="$wire.set('uploaded', 'Upload')" />
                </div>
                HTML;
            }
        })
            ->assertSee('Document')
            ->assertMissing('@uploaded')
            ->click('@tallstackui_upload_input')
            ->waitForText('Click here to upload')
            ->attach('@tallstackui_file_select', __DIR__.'/../../Fixtures/test.jpeg')
            ->waitForTextIn('@uploaded', 'test.jpeg')
            ->assertSeeIn('@uploaded', 'test.jpeg')
            ->assertVisible('@upload')
            ->waitForTextIn('@upload', 'Upload')
            ->assertSeeIn('@upload', 'Upload');
    }
}

class Upload extends Form
{
    public $photo;
}
