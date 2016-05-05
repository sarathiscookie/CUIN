<h3>Hello {{ $commentedEntry->nameto }},</h3>

<div>
    <p>Customer, {{ title_case($commentedEntry->name) }} ( email: {{ $commentedEntry->email }} ) has commented on the entry: "{{ $commentedEntry->title }}" as :</p>
    <p>{!! $commentbody->content !!}</p>
    <p><a href="{{ url('/customers/list/process/entry/'.$commentedEntry->eid.'/comments') }}">Click here</a> to see the comment!</p>
</div>

<section>
    Kind Regards <br />
    Info CUIN
</section>

