<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Username</th>
            <th scope="col">Rank</th>
            <th scope="col">Reputation</th>
            <th scope="col">Joined At</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        @foreach ($members as $member)
            <tr>
                <th scope="row">{{ $i }}</th>
                <td>{!! $member->user->displayName !!}</td>
                <td>{!! $member->rank->displayName ?? 'None' !!}</td>
                <td>{{ $member->reputation }}</td>
                <td>{!! pretty_date($member->joined_at) !!}</td>
            </tr>
            @if (isset($limit) && $i === $limit)
            @break
        @endif
        <?php $i++; ?>
    @endforeach
</tbody>
</table>
