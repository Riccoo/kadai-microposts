<ul class="media-list">
@foreach ($microposts as $micropost)
    <?php $user = $micropost->user; ?>
    <li class="media">
        <div class="media-left">
            <img class="media-object img-rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
        </div>
        <div class="media-body">
            <div>
                {!! link_to_route('users.show', $user->name, ['id' => $user->id]) !!} <span class="text-muted">posted at {{ $micropost->created_at }}</span>
            </div>
            <div>
                <p>{!! nl2br(e($micropost->content)) !!}</p>
            </div>
            <div>
                <div class="form-inline">
                <div class="btn-group" role="group">
                <div class="colxs-5">
                @if (Auth::user()->is_favoriting($micropost->id))
                    {!! Form::open(['route' => ['user.unfavorite', $micropost->id], 'method' => 'delete', 'style' => 'display: inline-block;']) !!}
                        {!! Form::submit('Unfavorite', ['class' => "btn btn-success btn-block btn-xs"]) !!}
                    {!! Form::close() !!}
                @else
                    {!! Form::open(['route' => ['user.favorite', $micropost->id], 'style' => 'display: inline-block;']) !!}
                        {!! Form::submit('Favorite', ['class' => "btn btn-default btn-block btn-xs"]) !!}
                    {!! Form::close() !!}
                @endif
                @if (Auth::id() == $micropost->user_id)
                    {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete', 'style' => 'display: inline-block;']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) !!}
                    {!! Form::close() !!}
                @endif
                </div>
                </div>
                </div>
            </div>
        </div>
    </li>
@endforeach
</ul>
{!! $microposts->render() !!}