@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
                <li><i class="ri-close-fill align-bottom me-1"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
