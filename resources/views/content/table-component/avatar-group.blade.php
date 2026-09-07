<div class="avatar-group">
    @foreach ($images as $img)
        <div class="avatar avatar-lg pull-up">
            <img class="view-on-click" src="{{ asset($img) }}" alt="Avatar" height="32" width="32">
        </div>
    @endforeach
</div>
