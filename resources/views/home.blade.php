@extends('app')
@section('content')
<div class='container'>
    <h1>Fee Calculator</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="input-group">
              <input type="file" class="form-control" name='file' >        
        </div>
    </form>
    <ul>

    </ul>
</div>
@endsection