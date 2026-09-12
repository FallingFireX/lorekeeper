@extends('account.layout')

@section('account-title')
    Settings
@endsection

@section('account-content')
    {!! breadcrumbs(['My Account' => Auth::user()->url, 'Invitations' => 'account/invitations']) !!}

    <h1>Guild Invitations</h1>

    @if ($invites && count($invites) > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Guild</th>
                    <th scope="col">Created</th>
                    <th scope="col">Expires In</th>
                    <th class="text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invites as $invite)
                    <tr>
                        <td>{!! $invite->guild->displayName !!}</td>
                        <td>{!! pretty_date($invite->created_at) !!}</td>
                        <td>{!! pretty_date($invite->expires_at) !!}</td>
                        <td class="text-right">
                            {!! Form::open(['url' => __('guilds.guilds') . '/' . $invite->guild->id . '/invite/']) !!}
                            <a href="#" data-action="accept" class="btn action-btn btn-success">Accept</a>
                            <a href="#" data-action="reject" class="btn action-btn btn-outline-danger">Reject</a>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No pending invitations.</p>
    @endif

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            $('.action-btn').click(function(e) {
                e.preventDefault();
                var action = $(this).data('action');
                var $form = $(this).closest('form');

                var url = $form.attr('action') || '';
                if (!url.endsWith('/')) {
                    url += '/';
                }

                $form.attr('action', url + action);
                $form.submit();
            });

        });
    </script>
@endsection
