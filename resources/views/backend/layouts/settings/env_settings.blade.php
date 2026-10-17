@extends('backend.app')
@section('title', 'ENV & API Credentials')
@push('style')
<style>
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #495057;
        border-radius: 6px 6px 0 0;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
        color: #7366ff;
        border-bottom: 2px solid #7366ff;
    }
    .card-header-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .badge-key-info {
        font-size: 11px;
        background-color: #f1f3f5;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('page-content')

<x-breadcrumbs title="ENV & API Credentials" subtitle="Manage API keys, environment settings, and application cache" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            
            <!-- Quick System Actions Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-actions">
                        <div>
                            <h5 class="mb-1"><i class="fa fa-tachometer text-primary me-2"></i>System Optimization & Cache Tools</h5>
                            <p class="text-muted mb-0">Run full application optimize & clear cache commands sequentially (`optimize:clear` -> `config:clear` -> `cache:clear` -> `route:clear` -> `view:clear`).</p>
                        </div>
                        <div>
                            <form action="{{ route('env.clearCache') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to run optimize:clear and clear all system caches?');">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark font-weight-bold shadow-sm">
                                    <i class="fa fa-broom me-1"></i> Clear All Caches & Optimize
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environment Settings Form & Tabs -->
            <div class="card">
                <x-table-header title="Environment Configuration" subtitle="Configure and update third-party service credentials safely" />

                <div class="card-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-4" id="envTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="stripe-tab" data-bs-toggle="tab" data-bs-target="#stripe" type="button" role="tab"><i class="fa fa-credit-card me-1"></i> Stripe</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="mail-tab" data-bs-toggle="tab" data-bs-target="#mail" type="button" role="tab"><i class="fa fa-envelope me-1"></i> Mail</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="revenuecat-tab" data-bs-toggle="tab" data-bs-target="#revenuecat" type="button" role="tab"><i class="fa fa-shopping-cart me-1"></i> RevenueCat</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="llm-tab" data-bs-toggle="tab" data-bs-target="#llm" type="button" role="tab"><i class="fa fa-robot me-1"></i> LLM Credentials</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#image" type="button" role="tab"><i class="fa fa-image me-1"></i> Image & NanoBanana</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab"><i class="fa fa-video-camera me-1"></i> Kling & ElevenLabs</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pusher-tab" data-bs-toggle="tab" data-bs-target="#pusher" type="button" role="tab"><i class="fa fa-wifi me-1"></i> Pusher</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="google-tab" data-bs-toggle="tab" data-bs-target="#google" type="button" role="tab"><i class="fa fa-cogs me-1"></i> Google & AWS</button>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('env.update') }}">
                        @csrf
                        <div class="tab-content" id="envTabsContent">

                            <!-- Stripe Tab -->
                            <div class="tab-pane fade show active" id="stripe" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-credit-card me-2"></i>Stripe Payment Gateways</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="STRIPE_PUBLIC_KEY">STRIPE PUBLIC KEY / KEY:</label>
                                        <input class="form-control input-air-primary" id="STRIPE_PUBLIC_KEY" type="text" name="STRIPE_PUBLIC_KEY" value="{{ $env['STRIPE_PUBLIC_KEY'] ?? ($env['STRIPE_KEY'] ?? '') }}" placeholder="pk_test_...">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits). Leave unchanged to keep existing key.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="STRIPE_SECRET_KEY">STRIPE SECRET KEY / SECRET:</label>
                                        <input class="form-control input-air-primary" id="STRIPE_SECRET_KEY" type="text" name="STRIPE_SECRET_KEY" value="{{ $env['STRIPE_SECRET_KEY'] ?? ($env['STRIPE_SECRET'] ?? '') }}" placeholder="sk_test_...">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits). Leave unchanged to keep existing key.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="STRIPE_WEBHOOK_SECRET">STRIPE WEBHOOK SECRET:</label>
                                        <input class="form-control input-air-primary" id="STRIPE_WEBHOOK_SECRET" type="text" name="STRIPE_WEBHOOK_SECRET" value="{{ $env['STRIPE_WEBHOOK_SECRET'] ?? '' }}" placeholder="whsec_...">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="STRIPE_CURRENCY">STRIPE CURRENCY:</label>
                                        <input class="form-control input-air-primary" id="STRIPE_CURRENCY" type="text" name="STRIPE_CURRENCY" value="{{ $env['STRIPE_CURRENCY'] ?? 'EUR' }}" placeholder="EUR or USD">
                                    </div>
                                </div>
                            </div>

                            <!-- Mail Tab -->
                            <div class="tab-pane fade" id="mail" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-envelope me-2"></i>Mail Server Credentials</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="MAIL_MAILER">MAIL MAILER:</label>
                                        <input class="form-control input-air-primary" id="MAIL_MAILER" type="text" name="MAIL_MAILER" value="{{ $env['MAIL_MAILER'] ?? 'smtp' }}" placeholder="smtp">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="MAIL_HOST">MAIL HOST:</label>
                                        <input class="form-control input-air-primary" id="MAIL_HOST" type="text" name="MAIL_HOST" value="{{ $env['MAIL_HOST'] ?? '' }}" placeholder="smtp.gmail.com">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="MAIL_PORT">MAIL PORT:</label>
                                        <input class="form-control input-air-primary" id="MAIL_PORT" type="text" name="MAIL_PORT" value="{{ $env['MAIL_PORT'] ?? '587' }}" placeholder="587">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="MAIL_USERNAME">MAIL USERNAME:</label>
                                        <input class="form-control input-air-primary" id="MAIL_USERNAME" type="text" name="MAIL_USERNAME" value="{{ $env['MAIL_USERNAME'] ?? '' }}" placeholder="user@domain.com">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="MAIL_PASSWORD">MAIL PASSWORD:</label>
                                        <input class="form-control input-air-primary" id="MAIL_PASSWORD" type="text" name="MAIL_PASSWORD" value="{{ $env['MAIL_PASSWORD'] ?? '' }}" placeholder="Password">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="MAIL_ENCRYPTION">MAIL ENCRYPTION:</label>
                                        <input class="form-control input-air-primary" id="MAIL_ENCRYPTION" type="text" name="MAIL_ENCRYPTION" value="{{ $env['MAIL_ENCRYPTION'] ?? 'tls' }}" placeholder="tls">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="MAIL_FROM_ADDRESS">MAIL FROM ADDRESS:</label>
                                        <input class="form-control input-air-primary" id="MAIL_FROM_ADDRESS" type="email" name="MAIL_FROM_ADDRESS" value="{{ $env['MAIL_FROM_ADDRESS'] ?? '' }}" placeholder="noreply@domain.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="MAIL_FROM_NAME">MAIL FROM NAME:</label>
                                        <input class="form-control input-air-primary" id="MAIL_FROM_NAME" type="text" name="MAIL_FROM_NAME" value="{{ $env['MAIL_FROM_NAME'] ?? '' }}" placeholder="App Name">
                                    </div>
                                </div>
                            </div>

                            <!-- RevenueCat Tab -->
                            <div class="tab-pane fade" id="revenuecat" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-shopping-cart me-2"></i>RevenueCat Subscription Webhooks & API Keys</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="REVENUECAT_WEBHOOK_SECRET">REVENUECAT WEBHOOK SECRET:</label>
                                        <input class="form-control input-air-primary" id="REVENUECAT_WEBHOOK_SECRET" type="text" name="REVENUECAT_WEBHOOK_SECRET" value="{{ $env['REVENUECAT_WEBHOOK_SECRET'] ?? '' }}" placeholder="your-revenuecat-webhook-secret">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="REVENUECAT_API_KEY">REVENUECAT API KEY (Optional):</label>
                                        <input class="form-control input-air-primary" id="REVENUECAT_API_KEY" type="text" name="REVENUECAT_API_KEY" value="{{ $env['REVENUECAT_API_KEY'] ?? '' }}" placeholder="goog_... or appl_...">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                </div>
                            </div>

                            <!-- LLM Credentials Tab -->
                            <div class="tab-pane fade" id="llm" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-robot me-2"></i>LLM Provider & Credentials Selection</h6>
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label fw-bold" for="LLM_PROVIDER">ACTIVE LLM PROVIDER:</label>
                                        <select class="form-select input-air-primary" id="LLM_PROVIDER" name="LLM_PROVIDER">
                                            <option value="gemini" {{ ($env['LLM_PROVIDER'] ?? '') === 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                                            <option value="openai" {{ ($env['LLM_PROVIDER'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                                            <option value="anthropic" {{ ($env['LLM_PROVIDER'] ?? '') === 'anthropic' ? 'selected' : '' }}>Anthropic Claude</option>
                                        </select>
                                    </div>

                                    <hr>
                                    <h6 class="text-secondary"><i class="fa fa-google me-2"></i>Google Gemini Credentials</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="GEMINI_API_KEY">GEMINI API KEY:</label>
                                        <input class="form-control input-air-primary" id="GEMINI_API_KEY" type="text" name="GEMINI_API_KEY" value="{{ $env['GEMINI_API_KEY'] ?? '' }}" placeholder="your_gemini_api_key">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="GEMINI_MODEL">GEMINI MODEL:</label>
                                        <input class="form-control input-air-primary" id="GEMINI_MODEL" type="text" name="GEMINI_MODEL" value="{{ $env['GEMINI_MODEL'] ?? 'gemini-2.5-flash' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="GEMINI_VISION_MODEL">GEMINI VISION MODEL:</label>
                                        <input class="form-control input-air-primary" id="GEMINI_VISION_MODEL" type="text" name="GEMINI_VISION_MODEL" value="{{ $env['GEMINI_VISION_MODEL'] ?? 'gemini-2.5-flash' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="GEMINI_BASE_URL">GEMINI BASE URL:</label>
                                        <input class="form-control input-air-primary" id="GEMINI_BASE_URL" type="text" name="GEMINI_BASE_URL" value="{{ $env['GEMINI_BASE_URL'] ?? 'https://generativelanguage.googleapis.com/v1beta' }}">
                                    </div>

                                    <hr>
                                    <h6 class="text-secondary"><i class="fa fa-code me-2"></i>OpenAI Credentials</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="OPENAI_API_KEY">OPENAI API KEY:</label>
                                        <input class="form-control input-air-primary" id="OPENAI_API_KEY" type="text" name="OPENAI_API_KEY" value="{{ $env['OPENAI_API_KEY'] ?? '' }}" placeholder="sk-proj-...">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="OPENAI_MODEL">OPENAI MODEL:</label>
                                        <input class="form-control input-air-primary" id="OPENAI_MODEL" type="text" name="OPENAI_MODEL" value="{{ $env['OPENAI_MODEL'] ?? 'gpt-4o' }}">
                                    </div>

                                    <hr>
                                    <h6 class="text-secondary"><i class="fa fa-terminal me-2"></i>Anthropic Claude Credentials</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="ANTHROPIC_API_KEY">ANTHROPIC API KEY:</label>
                                        <input class="form-control input-air-primary" id="ANTHROPIC_API_KEY" type="text" name="ANTHROPIC_API_KEY" value="{{ $env['ANTHROPIC_API_KEY'] ?? '' }}" placeholder="sk-ant-...">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="ANTHROPIC_MODEL">ANTHROPIC MODEL:</label>
                                        <input class="form-control input-air-primary" id="ANTHROPIC_MODEL" type="text" name="ANTHROPIC_MODEL" value="{{ $env['ANTHROPIC_MODEL'] ?? 'claude-sonnet-4-20250514' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Image Preview & NanoBanana Tab -->
                            <div class="tab-pane fade" id="image" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-picture-o me-2"></i>Image Preview Driver & NanoBanana Settings</h6>
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label fw-bold" for="IMAGE_PREVIEW_DRIVER">IMAGE PREVIEW DRIVER:</label>
                                        <select class="form-select input-air-primary" id="IMAGE_PREVIEW_DRIVER" name="IMAGE_PREVIEW_DRIVER">
                                            <option value="openai" {{ ($env['IMAGE_PREVIEW_DRIVER'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                                            <option value="nanobanana" {{ ($env['IMAGE_PREVIEW_DRIVER'] ?? '') === 'nanobanana' ? 'selected' : '' }}>NanoBanana</option>
                                        </select>
                                    </div>

                                    <h6 class="text-secondary mt-3"><i class="fa fa-magic me-2"></i>NanoBanana Credentials</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="NANOBANANA_API_KEY">NANOBANANA API KEY:</label>
                                        <input class="form-control input-air-primary" id="NANOBANANA_API_KEY" type="text" name="NANOBANANA_API_KEY" value="{{ $env['NANOBANANA_API_KEY'] ?? '' }}" placeholder="API Key">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="NANOBANANA_MODEL">NANOBANANA MODEL:</label>
                                        <input class="form-control input-air-primary" id="NANOBANANA_MODEL" type="text" name="NANOBANANA_MODEL" value="{{ $env['NANOBANANA_MODEL'] ?? 'gemini-3.1-flash-image-preview' }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="NANOBANANA_BASE_URL">NANOBANANA BASE URL:</label>
                                        <input class="form-control input-air-primary" id="NANOBANANA_BASE_URL" type="text" name="NANOBANANA_BASE_URL" value="{{ $env['NANOBANANA_BASE_URL'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Kling & ElevenLabs Tab -->
                            <div class="tab-pane fade" id="media" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-video-camera me-2"></i>Kling AI Video & ElevenLabs Audio Credentials</h6>
                                <div class="row g-3">
                                    <h6 class="text-secondary"><i class="fa fa-film me-2"></i>Kling AI Engine</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="KLING_ACCESS_KEY">KLING ACCESS KEY:</label>
                                        <input class="form-control input-air-primary" id="KLING_ACCESS_KEY" type="text" name="KLING_ACCESS_KEY" value="{{ $env['KLING_ACCESS_KEY'] ?? '' }}" placeholder="Access Key">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="KLING_SECRET_KEY">KLING SECRET KEY:</label>
                                        <input class="form-control input-air-primary" id="KLING_SECRET_KEY" type="text" name="KLING_SECRET_KEY" value="{{ $env['KLING_SECRET_KEY'] ?? '' }}" placeholder="Secret Key">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="KLING_BASE_URL">KLING BASE URL:</label>
                                        <input class="form-control input-air-primary" id="KLING_BASE_URL" type="text" name="KLING_BASE_URL" value="{{ $env['KLING_BASE_URL'] ?? 'https://api.klingai.com' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="KLING_MODEL">KLING MODEL:</label>
                                        <input class="form-control input-air-primary" id="KLING_MODEL" type="text" name="KLING_MODEL" value="{{ $env['KLING_MODEL'] ?? 'kling-v1-6' }}">
                                    </div>

                                    <hr>
                                    <h6 class="text-secondary"><i class="fa fa-microphone me-2"></i>ElevenLabs Voice Synthesis</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="ELEVENLABS_API_KEY">ELEVENLABS API KEY:</label>
                                        <input class="form-control input-air-primary" id="ELEVENLABS_API_KEY" type="text" name="ELEVENLABS_API_KEY" value="{{ $env['ELEVENLABS_API_KEY'] ?? '' }}" placeholder="ElevenLabs API Key">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="ELEVENLABS_BASE_URL">ELEVENLABS BASE URL:</label>
                                        <input class="form-control input-air-primary" id="ELEVENLABS_BASE_URL" type="text" name="ELEVENLABS_BASE_URL" value="{{ $env['ELEVENLABS_BASE_URL'] ?? 'https://api.elevenlabs.io/v1' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Pusher Tab -->
                            <div class="tab-pane fade" id="pusher" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-wifi me-2"></i>Broadcasting & Pusher Settings</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="BROADCAST_CONNECTION">BROADCAST CONNECTION:</label>
                                        <input class="form-control input-air-primary" id="BROADCAST_CONNECTION" type="text" name="BROADCAST_CONNECTION" value="{{ $env['BROADCAST_CONNECTION'] ?? 'pusher' }}" placeholder="pusher">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="BROADCAST_DRIVER">BROADCAST DRIVER:</label>
                                        <input class="form-control input-air-primary" id="BROADCAST_DRIVER" type="text" name="BROADCAST_DRIVER" value="{{ $env['BROADCAST_DRIVER'] ?? 'pusher' }}" placeholder="pusher">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="PUSHER_APP_ID">PUSHER APP ID:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_APP_ID" type="text" name="PUSHER_APP_ID" value="{{ $env['PUSHER_APP_ID'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="PUSHER_APP_KEY">PUSHER APP KEY:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_APP_KEY" type="text" name="PUSHER_APP_KEY" value="{{ $env['PUSHER_APP_KEY'] ?? '' }}">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="PUSHER_APP_SECRET">PUSHER APP SECRET:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_APP_SECRET" type="text" name="PUSHER_APP_SECRET" value="{{ $env['PUSHER_APP_SECRET'] ?? '' }}">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="PUSHER_APP_CLUSTER">PUSHER APP CLUSTER:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_APP_CLUSTER" type="text" name="PUSHER_APP_CLUSTER" value="{{ $env['PUSHER_APP_CLUSTER'] ?? 'mt1' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="PUSHER_HOST">PUSHER HOST:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_HOST" type="text" name="PUSHER_HOST" value="{{ $env['PUSHER_HOST'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="PUSHER_PORT">PUSHER PORT:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_PORT" type="text" name="PUSHER_PORT" value="{{ $env['PUSHER_PORT'] ?? '443' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="PUSHER_SCHEME">PUSHER SCHEME:</label>
                                        <input class="form-control input-air-primary" id="PUSHER_SCHEME" type="text" name="PUSHER_SCHEME" value="{{ $env['PUSHER_SCHEME'] ?? 'https' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Google & AWS Tab -->
                            <div class="tab-pane fade" id="google" role="tabpanel">
                                <h6 class="text-primary mb-3"><i class="fa fa-cogs me-2"></i>Google Auth, AWS S3 & FFmpeg Tools</h6>
                                <div class="row g-3">
                                    <h6 class="text-secondary"><i class="fa fa-google me-2"></i>Google OAuth Credentials</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="CLIENT_ID_ANDROID">CLIENT ID ANDROID:</label>
                                        <input class="form-control input-air-primary" id="CLIENT_ID_ANDROID" type="text" name="CLIENT_ID_ANDROID" value="{{ $env['CLIENT_ID_ANDROID'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="CLIENT_ID_IOS">CLIENT ID IOS:</label>
                                        <input class="form-control input-air-primary" id="CLIENT_ID_IOS" type="text" name="CLIENT_ID_IOS" value="{{ $env['CLIENT_ID_IOS'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="CLIENT_SECRET">CLIENT SECRET:</label>
                                        <input class="form-control input-air-primary" id="CLIENT_SECRET" type="text" name="CLIENT_SECRET" value="{{ $env['CLIENT_SECRET'] ?? '' }}">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="REDIRECT_URI">REDIRECT URI:</label>
                                        <input class="form-control input-air-primary" id="REDIRECT_URI" type="text" name="REDIRECT_URI" value="{{ $env['REDIRECT_URI'] ?? '' }}">
                                    </div>

                                    <hr>
                                    <h6 class="text-secondary"><i class="fa fa-aws me-2"></i>AWS S3 Storage Credentials</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="AWS_ACCESS_KEY_ID">AWS ACCESS KEY ID:</label>
                                        <input class="form-control input-air-primary" id="AWS_ACCESS_KEY_ID" type="text" name="AWS_ACCESS_KEY_ID" value="{{ $env['AWS_ACCESS_KEY_ID'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="AWS_SECRET_ACCESS_KEY">AWS SECRET ACCESS KEY:</label>
                                        <input class="form-control input-air-primary" id="AWS_SECRET_ACCESS_KEY" type="text" name="AWS_SECRET_ACCESS_KEY" value="{{ $env['AWS_SECRET_ACCESS_KEY'] ?? '' }}">
                                        <small class="text-muted"><i class="fa fa-lock text-warning"></i> Masked (Showing last 5 digits).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="AWS_DEFAULT_REGION">AWS DEFAULT REGION:</label>
                                        <input class="form-control input-air-primary" id="AWS_DEFAULT_REGION" type="text" name="AWS_DEFAULT_REGION" value="{{ $env['AWS_DEFAULT_REGION'] ?? 'us-east-1' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="AWS_BUCKET">AWS BUCKET:</label>
                                        <input class="form-control input-air-primary" id="AWS_BUCKET" type="text" name="AWS_BUCKET" value="{{ $env['AWS_BUCKET'] ?? '' }}">
                                    </div>

                                    <hr>
                                    <h6 class="text-secondary"><i class="fa fa-play-circle me-2"></i>FFmpeg Binary Paths</h6>
                                    <div class="col-md-6">
                                        <label class="form-label" for="FFMPEG_PATH">FFMPEG PATH:</label>
                                        <input class="form-control input-air-primary" id="FFMPEG_PATH" type="text" name="FFMPEG_PATH" value="{{ $env['FFMPEG_PATH'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="FFPROBE_PATH">FFPROBE PATH:</label>
                                        <input class="form-control input-air-primary" id="FFPROBE_PATH" type="text" name="FFPROBE_PATH" value="{{ $env['FFPROBE_PATH'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2"><i class="fa fa-arrow-left me-1"></i> Back to Dashboard</a>
                            <button class="btn btn-primary" type="submit"><i class="fa fa-save me-1"></i> Save Environment Settings</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
@endsection
