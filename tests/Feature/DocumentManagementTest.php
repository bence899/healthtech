<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MedicalDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Document;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
    }

    public function test_patient_can_view_documents_page(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($patient)
            ->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.index');
    }

    public function test_patient_can_upload_document(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('test.pdf', 4);
        $filePath = 'medical-documents/' . $patient->id . '/' . $file->hashName();

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Test Document',
            'document' => $file,
            'description' => 'Test description'
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('medical_documents', [
            'user_id' => $patient->id,
            'title' => 'Test Document',
            'description' => 'Test description',
            'file_path' => $filePath,
            'file_type' => 'pdf',
            'file_size' => $file->getSize()
        ]);

        Storage::disk('s3')->assertExists($filePath);
    }

    public function test_patient_cannot_upload_invalid_file_type(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('test.exe', 500);

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Test Document',
            'document' => $file
        ]);

        $response->assertSessionHasErrors('document');
        $this->assertDatabaseCount('medical_documents', 0);
    }

    public function test_patient_can_delete_own_document(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $document = MedicalDocument::factory()->create([
            'user_id' => $patient->id,
            'file_path' => 'medical-documents/test.pdf'
        ]);

        Storage::disk('s3')->put($document->file_path, 'test content');

        $response = $this->actingAs($patient)
            ->delete(route('documents.destroy', $document));

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('medical_documents', ['id' => $document->id]);
        Storage::disk('s3')->assertMissing($document->file_path);
    }

    public function test_patient_cannot_delete_others_document(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $otherPatient = User::factory()->create(['role' => 'patient']);
        $document = MedicalDocument::factory()->create([
            'user_id' => $otherPatient->id
        ]);

        $response = $this->actingAs($patient)
            ->delete(route('documents.destroy', $document));

        $response->assertStatus(403);
        $this->assertDatabaseHas('medical_documents', ['id' => $document->id]);
    }

    public function test_admin_can_view_all_documents(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = User::factory()->create(['role' => 'patient']);
        $document = MedicalDocument::factory()->create([
            'user_id' => $patient->id
        ]);

        $response = $this->actingAs($admin)
            ->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewHas('documents');
        $response->assertSee($document->title);
    }

    public function test_document_download(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $document = MedicalDocument::factory()->create([
            'user_id' => $patient->id,
            'file_path' => 'medical-documents/' . $patient->id . '/test.pdf'
        ]);

        Storage::disk('s3')->put($document->file_path, 'test content');

        $response = $this->actingAs($patient)
            ->get(route('documents.download', $document));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/octet-stream');
    }

    public function test_large_file_upload_validation(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('large.pdf', 1025); // Just over 1MB

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Large Document',
            'document' => $file
        ]);

        $response->assertSessionHasErrors(['document' => 'The document field must not be greater than 1024 kilobytes.']);
        $this->assertDatabaseCount('medical_documents', 0);
    }

    public function test_enforces_monthly_upload_limit(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        
        // Simulate reaching monthly limit
        MedicalDocument::factory()->count(5)->create([
            'user_id' => $patient->id,
            'created_at' => now(),
            'file_size' => 1024 // 1mb
        ]);

        $file = UploadedFile::fake()->create('new-document.pdf', 1024);
        
        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Monthly Limit Test',
            'document' => $file
        ]);

        $response->assertSessionHasErrors('document');
        $this->assertStringContainsString(
            'Monthly upload limit reached',
            session('errors')->first('document')
        );
    }

    public function test_enforces_total_storage_limit(): void
    {
        Storage::fake('local');
        
        $patient = User::factory()->create(['role' => 'patient']);
        $this->actingAs($patient);

        // Create a file that would exceed the storage limit
        $largeFile = UploadedFile::fake()->create('document.pdf', 104858); // Just over 100MB

        $response = $this->post(route('documents.store'), [
            'document' => $largeFile,
            'title' => 'Test Document',
            'description' => 'Test Description'
        ]);

        $response->assertSessionHasErrors('document');
        $this->assertDatabaseCount('medical_documents', 0);
    }

    public function test_tracks_monthly_upload_count(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('test.pdf', 4); // Use 4KB to stay under 5120 bytes limit

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Test Document',
            'document' => $file
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('documents.index'));

        $this->assertDatabaseHas('medical_documents', [
            'user_id' => $patient->id,
            'file_size' => $file->getSize(),
            'upload_month' => now()->format('Y-m')
        ]);
    }

    public function test_enforces_monthly_request_limit(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        
        // Create 5 documents (reaching the limit)
        MedicalDocument::factory()->count(5)->create([
            'user_id' => $patient->id,
            'created_at' => now(),
            'file_size' => 1024 * 1024 // 1MB each
        ]);

        $file = UploadedFile::fake()->create('test.pdf', 1024);

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Request Limit Test',
            'document' => $file
        ]);

        $response->assertSessionHasErrors('document');
        $this->assertStringContainsString(
            'Monthly upload limit reached',
            session('errors')->first('document')
        );
    }

    public function test_tracks_usage_metrics(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('test.pdf', 4); // Use 4KB instead of 1MB

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Test Document',
            'document' => $file
        ]);

        $response->assertSessionHasNoErrors();
        $document = MedicalDocument::latest()->first();
        
        $this->assertEquals(4 * 1024, $document->file_size); // 4KB in bytes
        $this->assertEquals(now()->format('Y-m'), $document->created_at->format('Y-m'));
    }

    public function test_tracks_file_size(): void
    {
        Storage::fake('s3');
        
        $patient = User::factory()->create(['role' => 'patient']);
        $this->actingAs($patient);

        $file = UploadedFile::fake()->create('document.pdf', 1024); // 1MB

        $response = $this->post(route('documents.store'), [
            'title' => 'Test Document',
            'document' => $file,
            'description' => 'Test Description'
        ]);

        $document = MedicalDocument::first();
        $this->assertEquals(1024 * 1024, $document->file_size); // Size in bytes
    }

    public function test_enforces_file_size_limit(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('test.pdf', 1025); // Just over 1MB

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Test Document',
            'document' => $file
        ]);

        $response->assertSessionHasErrors(['document']);
        $this->assertStringContainsString(
            'must not be greater than 1024 kilobytes',
            session('errors')->first('document')
        );
    }

    public function test_allows_valid_upload_within_limits(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $file = UploadedFile::fake()->create('test.pdf', 4); // Change from 512KB to 4KB

        $response = $this->actingAs($patient)->post(route('documents.store'), [
            'title' => 'Valid Document',
            'document' => $file
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('documents.index'));
        
        $this->assertDatabaseHas('medical_documents', [
            'user_id' => $patient->id,
            'title' => 'Valid Document',
            'file_size' => $file->getSize()
        ]);
    }
} 