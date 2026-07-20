@extends('admin.layout')

@section('admin-title')
    Categories
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Categories' => 'admin/suggestions/categories']) !!}

    <h1>Categories</h1>

    <p>These are the categories that suggestions can be submitted to</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/suggestions/categories/create') }}"><i class="fas fa-plus"></i> Create Category</a></div>
    @if (!count($categories))
        <p>No categories found.</p>
    @else
        <table class="table table-sm label-table">
            <tbody>
                @foreach ($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td>
                            {!! $category->id !!}
                        </td>
                        <td>
                            {!! $category->name !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/suggestions/categories/edit/' . $category->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        
    @endif

@endsection
