<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Import Ops</title>
</head>
<body>
    <h1>Import Operations</h1>
    <p>Currently we can import soups. UPC will not be duplicated.</p>

    <form action="{{ route('import-soups') }}" method="POST" >
    @csrf
        <div class="form-group">
            <button class="btn btn-primary">Import Soups</button>
        </div>
    </form>
</body>
</html>