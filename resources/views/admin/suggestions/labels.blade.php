@extends('admin.layout')

@section('admin-title')
    Labels/Tags
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Labels' => 'admin/suggestions/labels']) !!}

    <h1>Labels/Tags</h1>

    <p>This is all labels/tags you can add to suggestions</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/suggestions/labels/create') }}"><i class="fas fa-plus"></i> Create Label</a></div>
    @if (!count($labels))
        <p>No labels found.</p>
    @else
        <table class="table table-sm label-table">
            <tbody>
                @foreach ($labels as $label)
                    <tr data-id="{{ $label->id }}">
                        <td>
                            {!! $label->id !!}
                        </td>
                        <td>
                            {!! $label->name !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/suggestions/labels/edit/' . $label->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        
    @endif

@endsection
