@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'error']) }}>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
