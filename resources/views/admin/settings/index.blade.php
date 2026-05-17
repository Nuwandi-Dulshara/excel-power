@extends('admin.layouts.app')

@section('page-title', 'Settings')
@section('page-subtitle', 'Manage store, invoice, and system settings')

@section('content')

<style>
.settings-card {
    background: white;
    border: 1px solid #fde68a;
    border-radius: 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
    overflow: hidden;
}

.settings-header {
    background: radial-gradient(circle at top right, rgba(212, 175, 55, .25), transparent 30%), linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    padding: 18px 20px;
}

.settings-header h5 {
    font-weight: 900;
    margin: 0;
}

.settings-body {
    padding: 22px;
}

.form-label {
    color: #7f1d1d;
    font-size: .84rem;
    font-weight: 800;
    margin-bottom: 8px;
}

.theme-control {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 14px;
    color: #7f1d1d;
    font-weight: 650;
    padding: 12px 14px;
}

.theme-control:focus {
    background: white;
    border-color: #b91c1c;
    box-shadow: 0 0 0 4px rgba(185, 28, 28, .1);
}

.btn-save,
.btn-action-main {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    border: none;
    border-radius: 14px;
    color: white;
    font-weight: 850;
    padding: 12px 18px;
}

.btn-save:hover,
.btn-action-main:hover {
    color: white;
}

.btn-soft {
    background: #fff7ed;
    border: none;
    border-radius: 14px;
    color: #7f1d1d;
    font-weight: 850;
    padding: 12px 18px;
}

.alert-theme {
    border-radius: 16px;
    font-weight: 750;
    padding: 14px 18px;
}

.alert-success-theme {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.alert-error-theme {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.logo-preview {
    align-items: center;
    background: #fff7ed;
    border: 1px solid #fde68a;
    border-radius: 16px;
    display: flex;
    height: 88px;
    justify-content: center;
    overflow: hidden;
    width: 120px;
}

.logo-preview img {
    filter: grayscale(1) contrast(1.35);
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

.settings-note {
    color: #92400e;
    font-size: .85rem;
    font-weight: 650;
}
</style>

@if(session('success'))
<div class="alert-theme alert-success-theme mb-4">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert-theme alert-error-theme mb-4">
    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert-theme alert-error-theme mb-4">
    <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="settings-card mb-4">
        <div class="settings-header">
            <h5><i class="bi bi-shop-window me-1"></i> Store Settings</h5>
        </div>
        <div class="settings-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <label class="form-label">Shop Name</label>
                    <input type="text" name="shop_name" class="form-control theme-control"
                        value="{{ old('shop_name', $settings['shop_name']) }}" required>
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Shop Name Second Line</label>
                    <input type="text" name="shop_name_second_line" class="form-control theme-control"
                        value="{{ old('shop_name_second_line', $settings['shop_name_second_line']) }}">
                </div>

                <div class="col-lg-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" class="form-control theme-control">{{ old('address', $settings['address']) }}</textarea>
                </div>

                <div class="col-lg-4">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control theme-control"
                        value="{{ old('phone_number', $settings['phone_number']) }}">
                </div>

                <div class="col-lg-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control theme-control"
                        value="{{ old('email', $settings['email']) }}">
                </div>

                <div class="col-lg-4">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency" class="form-control theme-control"
                        value="{{ old('currency', $settings['currency']) }}" required>
                </div>

                <div class="col-lg-8">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control theme-control" accept="image/*">
                    <div class="settings-note mt-2">The logo is printed in black and white on the POS receipt.</div>
                </div>

                <div class="col-lg-4">
                    <label class="form-label">Current Logo</label>
                    <div class="logo-preview">
                        @if(!empty($settings['logo']))
                            <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Current logo">
                        @else
                            <i class="bi bi-image text-muted fs-2"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="settings-card mb-4">
        <div class="settings-header">
            <h5><i class="bi bi-receipt-cutoff me-1"></i> Invoice Settings</h5>
        </div>
        <div class="settings-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    <label class="form-label">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" class="form-control theme-control"
                        value="{{ old('invoice_prefix', $settings['invoice_prefix']) }}" required>
                </div>

                <div class="col-lg-4">
                    <label class="form-label">Print Size</label>
                    <select name="print_size" class="form-select theme-control" required>
                        <option value="80mm" {{ old('print_size', $settings['print_size']) === '80mm' ? 'selected' : '' }}>80mm Receipt</option>
                        <option value="58mm" {{ old('print_size', $settings['print_size']) === '58mm' ? 'selected' : '' }}>58mm Receipt</option>
                    </select>
                </div>

                <div class="col-lg-12">
                    <label class="form-label">Footer Message</label>
                    <input type="text" name="footer_message" class="form-control theme-control"
                        value="{{ old('footer_message', $settings['footer_message']) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="settings-card mb-4">
        <div class="settings-header">
            <h5><i class="bi bi-sliders me-1"></i> System Settings</h5>
        </div>
        <div class="settings-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <label class="form-label">Timezone</label>
                    <select name="timezone" class="form-select theme-control" required>
                        @foreach($timezones as $timezone)
                            <option value="{{ $timezone }}" {{ old('timezone', $settings['timezone']) === $timezone ? 'selected' : '' }}>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Language</label>
                    <select name="language" class="form-select theme-control" required>
                        @foreach($languages as $code => $label)
                            <option value="{{ $code }}" {{ old('language', $settings['language']) === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <button type="submit" class="btn-save">
            <i class="bi bi-save me-1"></i> Save Settings
        </button>
    </div>
</form>

<div class="settings-card">
    <div class="settings-header">
        <h5><i class="bi bi-database-fill-gear me-1"></i> Database Tools</h5>
    </div>
    <div class="settings-body">
        <div class="row g-4 align-items-end">
            <div class="col-lg-5">
                <form method="POST" action="{{ route('admin.settings.backup') }}">
                    @csrf
                    <label class="form-label">Backup Database</label>
                    <button type="submit" class="btn-action-main d-block">
                        <i class="bi bi-download me-1"></i> Download Backup
                    </button>
                </form>
            </div>

            <div class="col-lg-7">
                <form method="POST" action="{{ route('admin.settings.restore') }}" enctype="multipart/form-data"
                    onsubmit="return confirm('Restore database from this file? A backup of the current database will be created first.');">
                    @csrf
                    <label class="form-label">Restore Database</label>
                    <div class="d-flex flex-wrap gap-2">
                        <input type="file" name="database_file" class="form-control theme-control" accept=".sqlite,.db" required>
                        <button type="submit" class="btn-soft">
                            <i class="bi bi-upload me-1"></i> Restore
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
