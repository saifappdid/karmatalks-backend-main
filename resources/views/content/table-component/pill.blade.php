@forelse($parameters as $p)
    <div class="badge badge-pill badge-light-primary">{{ $p->parameter->name }}</div>
@empty
    <div class="badge badge-pill badge-light-danger">NA</div>
@endforelse
