@component('mail::message')
# Hello, {{ $recipientName }}!

Thank you for reaching out. Here is our reply to your message regarding **"{{ $originalSubject }}"**:

---

{!! nl2br(e($replyMessage)) !!}

---

If you have any further questions, feel free to contact us again.

Thanks,<br>
**{{ config('app.name') }}**
@endcomponent
