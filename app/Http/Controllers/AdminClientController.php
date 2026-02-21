<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AccessArea;
use App\Models\ClientApp;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\PassportClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\GlobalLogoutService;

class AdminClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = ClientApp::with('accessArea')
            ->orderBy('name')
            ->get();

        return view('admin.clients.index', [
            'clients' => $clients,
        ]);
    }

    public function create(): View
    {
        $accessAreas = AccessArea::orderBy('name')->get();

        return view('admin.clients.create', [
            'accessAreas' => $accessAreas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:client_apps,slug'],
            'base_url' => ['required', 'url', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'access_area_id' => ['required', 'integer', 'exists:access_areas,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $client = ClientApp::create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.created',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $client->id,
                'slug' => $client->slug,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client app created successfully.');
    }

    public function edit(ClientApp $client): View
    {
        $accessAreas = AccessArea::orderBy('name')->get();

        return view('admin.clients.edit', [
            'client' => $client,
            'accessAreas' => $accessAreas,
        ]);
    }

    public function update(Request $request, ClientApp $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:client_apps,slug,'.$client->id],
            'base_url' => ['required', 'url', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'access_area_id' => ['required', 'integer', 'exists:access_areas,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $client->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.updated',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $client->id,
                'slug' => $client->slug,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client app updated successfully.');
    }

    public function destroy(Request $request, ClientApp $client): RedirectResponse
    {
        $clientId = $client->id;
        $slug = $client->slug;

        // Hapus Passport Client jika ada
        if ($client->oauth_client_id) {
            PassportClient::where('id', $client->oauth_client_id)->delete();
        }

        $client->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.deleted',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $clientId,
                'slug' => $slug,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client app deleted successfully.');
    }

    public function generatePassportClient(Request $request, ClientApp $client): RedirectResponse
    {
        // Hanya superadmin yang bisa generate Passport client
        if (!$request->user()->hasRole('super_admin')) {
            abort(403, 'Hanya superadmin yang dapat membuat Passport client.');
        }

        // Cek apakah client sudah punya Passport client
        if ($client->hasPassportClient()) {
            return redirect()
                ->route('admin.clients.index')
                ->with('error', 'Client ini sudah memiliki Passport client. Gunakan tombol Info untuk melihat detailnya.');
        }

        // Validasi bahwa base_url sudah diisi
        if (empty($client->base_url)) {
            return redirect()
                ->route('admin.clients.index')
                ->with('error', 'Base URL harus diisi terlebih dahulu sebelum membuat Passport client.');
        }

        // Buat callback URL dari base_url
        $callbackUrl = rtrim($client->base_url, '/') . '/auth/callback';

        // Buat Passport Client langsung menggunakan DB
        $clientId = (string) Str::uuid();
        $clientSecret = Str::random(40);
        
        // Hash secret sebelum menyimpan ke database (seperti Passport)
        $hashedSecret = Hash::make($clientSecret);
        
        DB::table('oauth_clients')->insert([
            'id' => $clientId,
            'owner_id' => $request->user()->id,
            'owner_type' => 'App\\Models\\User',
            'name' => $client->name,
            'secret' => $hashedSecret, // Secret sudah di-hash
            'revoked' => false,
            'grant_types' => json_encode(['authorization_code', 'refresh_token']), // Tambahkan refresh_token
            'redirect_uris' => json_encode([$callbackUrl]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Ambil client yang baru dibuat
        $passportClient = PassportClient::find($clientId);

        // Generate webhook secret untuk global logout callback
        $webhookSecret = Str::random(40);
        $logoutCallbackUrl = rtrim($client->base_url, '/') . '/auth/sso/logout-callback';

        // Simpan oauth_client_id, logout_callback_url, dan encrypted_webhook_secret ke ClientApp
        $client->update([
            'oauth_client_id' => $passportClient->id,
            'logout_callback_url' => $logoutCallbackUrl,
            'encrypted_webhook_secret' => Crypt::encryptString($webhookSecret),
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.passport_generated',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $client->id,
                'slug' => $client->slug,
                'oauth_client_id' => $passportClient->id,
            ],
        ]);

        // Simpan plain secret dan webhook secret di session untuk ditampilkan sekali
        $request->session()->put('passport_client_secret', $clientSecret);
        $request->session()->put('passport_webhook_secret', $webhookSecret);
        $request->session()->put('show_secret_once', true);
        $request->session()->put('passport_client_id', $passportClient->id);
        
        return redirect()
            ->route('admin.clients.info', $client);
    }

    /**
     * Regenerate webhook secret untuk Global Logout (client lama yang belum punya).
     */
    public function regenerateWebhookSecret(Request $request, ClientApp $client): RedirectResponse
    {
        if (! $request->user()->hasRole('super_admin')) {
            abort(403, 'Hanya superadmin yang dapat regenerate webhook secret.');
        }

        if (! $client->hasPassportClient() || empty($client->base_url)) {
            return redirect()
                ->route('admin.clients.index')
                ->with('error', 'Client harus punya Passport client dan base_url.');
        }

        $webhookSecret = Str::random(40);
        $logoutCallbackUrl = rtrim($client->base_url, '/') . '/auth/sso/logout-callback';

        $client->update([
            'logout_callback_url' => $logoutCallbackUrl,
            'encrypted_webhook_secret' => Crypt::encryptString($webhookSecret),
        ]);

        $request->session()->put('passport_webhook_secret', $webhookSecret);
        $request->session()->put('show_secret_once', true);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.webhook_secret_regenerated',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => ['client_app_id' => $client->id, 'slug' => $client->slug],
        ]);

        return redirect()
            ->route('admin.clients.info', $client)
            ->with('status', 'Webhook secret berhasil dibuat. Simpan SSO_WEBHOOK_SECRET sekarang.');
    }

    public function infoPassportClient(Request $request, ClientApp $client): View
    {
        // Hanya superadmin yang bisa melihat info Passport client
        if (!$request->user()->hasRole('super_admin')) {
            abort(403, 'Hanya superadmin yang dapat melihat informasi Passport client.');
        }

        if (!$client->hasPassportClient()) {
            abort(404, 'Client ini belum memiliki Passport client.');
        }

        $passportClient = PassportClient::find($client->oauth_client_id);

        if (!$passportClient) {
            abort(404, 'Passport client tidak ditemukan.');
        }

        // Ambil secret dari session jika ada
        $plainSecret = $request->session()->get('passport_client_secret');
        $plainWebhookSecret = $request->session()->get('passport_webhook_secret');
        $showSecretOnce = $request->session()->get('show_secret_once', false);
        
        // Jika secret ada dan ini pertama kali, kita akan hapus setelah view ditampilkan
        // Tapi kita tidak hapus di sini karena view perlu akses ke secret
        // Kita akan hapus di JavaScript setelah secret ditampilkan

        return view('admin.clients.info', [
            'client' => $client,
            'passportClient' => $passportClient,
            'plainSecret' => $plainSecret,
            'plainWebhookSecret' => $plainWebhookSecret,
            'showSecretOnce' => $showSecretOnce,
        ]);
    }

    public function deletePassportClient(Request $request, ClientApp $client): RedirectResponse
    {
        // Hanya superadmin yang bisa delete Passport client
        if (!$request->user()->hasRole('super_admin')) {
            abort(403, 'Hanya superadmin yang dapat menghapus Passport client.');
        }

        if (!$client->hasPassportClient()) {
            return redirect()
                ->route('admin.clients.index')
                ->with('error', 'Client ini belum memiliki Passport client.');
        }

        $oauthClientId = $client->oauth_client_id;

        // Hapus Passport Client dari database
        DB::table('oauth_clients')->where('id', $oauthClientId)->delete();

        // Hapus oauth_client_id, logout_callback_url, dan encrypted_webhook_secret dari ClientApp
        $client->update([
            'oauth_client_id' => null,
            'logout_callback_url' => null,
            'encrypted_webhook_secret' => null,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.passport_deleted',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $client->id,
                'slug' => $client->slug,
                'oauth_client_id' => $oauthClientId,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Passport client berhasil dihapus. Anda dapat membuat Passport client baru untuk client ini.');
    }

    public function clearSecretSession(Request $request): \Illuminate\Http\JsonResponse
    {
        // Hapus session secret setelah ditampilkan
        $request->session()->forget('passport_client_secret');
        $request->session()->forget('passport_webhook_secret');
        $request->session()->forget('show_secret_once');

        return response()->json(['success' => true]);
    }

    /**
     * Test Global Logout webhook ke client - kembalikan hasil lengkap untuk debugging.
     */
    public function testGlobalLogout(Request $request, ClientApp $client): \Illuminate\Http\JsonResponse
    {
        if (! $request->user()->hasRole('super_admin')) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        if (! $client->logout_callback_url && empty($client->base_url)) {
            return response()->json([
                'success' => false,
                'error' => 'Client belum punya base_url atau logout_callback_url.',
            ], 400);
        }

        $result = app(GlobalLogoutService::class)->testWebhook($client, $request->user());

        return response()->json($result);
    }
}