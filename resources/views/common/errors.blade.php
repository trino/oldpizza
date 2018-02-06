@if (count($errors) > 0)
    <!-- Form Error List -->
    <div class="alert alert-danger">
        <h6>Whoops! Something went wrong!</h6>

        <br><br>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
