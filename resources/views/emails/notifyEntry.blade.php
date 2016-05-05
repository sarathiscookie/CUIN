<h3>Hello {{ $processEntry->name }},</h3>

<div>
    <p>A new entry with title: "{{ $processEntry->title }}" has been created under process: " {{ $processEntry->process }}",  <a href="{{ url('/'.$processEntry->hash.'?pid='.$processEntry->pid) }}">Click here</a> to see details.</p>
</div>

<section>
    Kind Regards <br />
    Team CUIN
</section>

