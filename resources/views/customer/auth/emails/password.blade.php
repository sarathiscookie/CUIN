Click here to reset your Login password: <a href="{{ $link = url('/customer/password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
