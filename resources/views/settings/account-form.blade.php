<h4 class="mb-4">Dados da Conta</h4>

<form action="{{ route('settingsAccountStoreOrUpdateURL') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="name">Nome da Empresa:</label>
        <input type="text" name="name" id="name" class="form-control" maxlength="190" value="{{ old('name', $settings['name'] ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="phone">Número do telefone móvel:</label>
        <input type="tel" name="phone" id="phone" class="form-control phone-mask" value="{{ old('phone', formatPhoneNumber($settings['phone']) ?? '') }}" maxlength="16" required>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title mb-0">Envie o logotipo de sua empresa</h4>
            <small class="form-text">Formato suportado: <strong class="text-theme">JPG/PNG</strong> | Recomendado PNG transparente na dimensão: 360 x 80 pixels</small>
        </div>
        <div class="card-body">
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    <div class="position-absolute bottom-0 end-0">
                        <label for="logo-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Clique aqui e envie o logotipo de sua empresa">
                            <div class="avatar-xs">
                                <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                    <i class="ri-image-fill text-theme"></i>
                                </div>
                            </div>
                        </label>
                        <input class="form-control d-none" name="logo" id="logo-image-input" type="file" accept="image/png, image/jpeg">
                    </div>
                    <div class="avatar-lg">
                        <div class="avatar-title bg-transparent">
                            <img
                            @if(isset($settings['logo']) && $settings['logo'])
                                src="{{ asset('storage/' . $settings['logo']) }}"
                            @else
                                src="{{URL::asset('build/images/logo.png')}}"
                            @endif
                            id="logo-img" alt="logo" data-user-id="{{ base64_encode(1) }}" style="max-height: 100px;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="submit" class="btn btn-theme" value="Atualizar Minha Conta">
</form>
