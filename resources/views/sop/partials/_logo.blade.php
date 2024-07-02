<div class="card" style="width: 18rem;">
    <img class="card-img-top" src="{{ asset('storage/logo/' . Session::get('logo')) }}" alt="Logo {{ $usaha->nama_usaha }}"
        id="previewLogo">
    <div class="card-body text-center">
        <button type="button" id="EditLogo" class="btn btn-primary">Edit</button>
    </div>
</div>

<form action="/pengaturan/logo/{{ $usaha->id }}" method="post" enctype="multipart/form-data" id="FormLogo">
    @csrf
    @method('PUT')

    <input type="file" name="logo" id="logo" class="d-none">
</form>
