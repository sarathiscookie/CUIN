<h3>Hello {{ $customer->name }},</h3>

<div>
    <p>Please activate your account with CUIN and use the below password to Login. <a href="{{ url('/customer/activation/'.$customer->hash) }}">Activate now</a></p>
    <p>Your CUIN login password : {{ $password }}
</div>

<section>
    Kind Regards <br />
    Team CUIN
</section>

