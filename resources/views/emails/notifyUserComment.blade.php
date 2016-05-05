<h3>Hello {{ $comment->name }},</h3>

<div>
    <p>Admin has commented on the entry: "{{ $comment->title }}" as :</p>
    <p>{!! $comment->content !!}</p>
    <p><a href="{{ url('/'.$comment->hash.'?pid='.$comment->pid) }}">Click here</a> to see more!</p>
</div>

<section>
    Kind Regards <br />
    Info CUIN
</section>

