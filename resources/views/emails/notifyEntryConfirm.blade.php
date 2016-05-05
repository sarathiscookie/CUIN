<h3>Hello {{ $confirmEntry->nameto }},</h3>

<div>
    <p>Customer, {{ title_case($confirmEntry->name) }} ( email: {{ $confirmEntry->email }} ) has set a confirmation for the entry: "{{ $confirmEntry->title }}" as @if($input=='confrm') Confirmed @else Rejected @endif.</p>
</div>

<section>
    Kind Regards <br />
    Info CUIN
</section>

