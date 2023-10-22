@if ($errors->any())
    <div class="alert alert-danger mb-0">
        <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
                <li><i class="ri-close-fill align-bottom me-1"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
